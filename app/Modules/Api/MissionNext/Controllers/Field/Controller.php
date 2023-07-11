<?php

namespace App\Modules\Api\MissionNext\Controllers\Field;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\Service\ResponseDataFormat\FieldChoicesFormat;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Field\Candidate;
use Input;

class Controller extends BaseController
{

    /**
     * @param string $type
     *
     * @return RestResponse
     */
    public function getIndex($type)
    {
        $item = $this->fieldRepo()->fieldsExpanded()->get();
        return new RestResponse(FieldChoicesFormat::format($item));
    }

    /**
     * @param string $type
     *
     * @return RestResponse
     */
    public function postIndex($type)
    {
        /** @var  $request \Symfony\Component\HttpFoundation\Request */
        $request = Request::instance()->request;

        $fields = $request->get("fields") ?? [];


        return new RestResponse($this->fieldRepo()->addFields($fields));
    }

    /**
     * @param string $type
     *
     * @return RestResponse
     */
    public function putIndex($type)
    {
        /** @var  $request \Symfony\Component\HttpFoundation\Request */
        $request = Request::instance()->request;

        $fields = $request->get("fields") ? $request->get("fields") : [];


        return new RestResponse($this->fieldRepo()->updateFields($fields));
    }

    /**
     * @param string $type
     *
     * @return RestResponse
     */
    public function deleteIndex($type)
    {
        /** @var  $request \Symfony\Component\HttpFoundation\Request */
        $query = Request::instance()->query;

        $ids = $query->has("ids") ? $query->get("ids") : [];

        return new RestResponse($this->fieldRepo()->deleteFields($ids));
    }


    /**
     * @param string $type
     *
     * @return RestResponse
     */
    public function getModel($type)
    {

        $item = FieldChoicesFormat::format($this->fieldRepo()->modelFieldsExpanded()->get());

        return new RestResponse($item);

    }

    /**
     * @param integer $fieldId
     *
     * @return RestResponse
     */
    public function postChoices($type, $fieldId)
    {
        $choices = $this->request->request->get('choices');

        $choicesIds = array_pluck($choices, 'id');
        /** @var  $field Candidate */
        $field = $this->fieldRepo()->getModel()->findOrFail($fieldId);
        /** @var  $choiceModels Collection */
        $choiceModels = $field->choices()->whereIn('id', $choicesIds)->get();
        $choiceModels->each(function ($model) use ($choices) {
            $modelData = array_first($choices, function ($key, $value) use ($model) {

                return $value['id'] == $model->id;
            });

            if (isset($modelData['meta'])) {

                $modelData['meta'] = json_encode($modelData['meta']);
            }
            $modelData = array_except($modelData, 'id');

            $model->update($modelData);
        });


        return new RestResponse(true);
    }


    /**
     * @param string $type
     *
     * @return RestResponse
     */
    public function postModel($type)
    {
        $fields = Input::get("fields", []);
        $sync = [];
        foreach ($fields as $field) {
            $sync[$field["id"]] = ["constraints" => $field["constraints"]];
        }

        $mFields = $this->fieldRepo()->modelFields();

        //$idsBeforSync = $mFields->getRelatedIds();
        $idsBeforSync = $mFields->allRelatedIds();

        count($fields)
            ? $mFields->sync($sync)
            : $mFields->detach();

        $idsAfterSync = $mFields->allRelatedIds();

        $viewIdsToRemove = array_diff(
            is_array($idsBeforSync) ? $idsBeforSync : array($idsBeforSync),
            is_array($idsAfterSync) ? $idsAfterSync : array($idsAfterSync)
        );

//        if (!empty($viewIdsToRemove)) {
//            $symbol_keys = $this->fieldRepo()->getModel()->whereIn('id', $viewIdsToRemove)->pluck('symbol_key');
//            $this->viewFieldRepo()->deleteByDMSymbolKeys($this->getApp()->DM(), $symbol_keys);
//        }

        return new RestResponse($mFields->get());
    }

}
