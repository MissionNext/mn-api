<?php
namespace MissionNext\Controllers\Api\Field;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Input;
use MissionNext\Api\Service\ResponseDataFormat\FieldChoicesFormat;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Repos\Field\FieldRepositoryInterface;

/**
 * Class Controller
 * @package MissionNext\Controllers\Api\Field
 */
class Controller extends BaseController
{

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function getIndex($type)
    {

        return new RestResponse(FieldChoicesFormat::format($this->fieldRepo()->fieldsExpanded()->get()));
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function postIndex($type)
    {
        /** @var  $request \Symfony\Component\HttpFoundation\Request */
        $request = Request::instance()->request;
        $fields = $request->has("fields") ? $request->get("fields") : [];

        return new RestResponse($this->fieldRepo()->addFields($fields));
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function putIndex($type)
    {
        /** @var  $request \Symfony\Component\HttpFoundation\Request */
        $request = Request::instance()->request;

        $fields = $request->has("fields") ? $request->get("fields") : [];

        return new RestResponse($this->fieldRepo()->updateFields($fields));
    }

    /**
     * @param $type
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
     * @param $type
     *
     * @return RestResponse
     */
    public function getModel($type)
    {

        return new RestResponse($this->fieldRepo()->modelFieldsExpanded()->get());

    }


    /**
     * @param $type
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

        $idsBeforSync = $mFields->getRelatedIds();

        count($fields)
            ? $mFields->sync($sync)
            : $mFields->detach();

        $idsAfterSync = $mFields->getRelatedIds();

        $viewIdsToRemove = array_diff($idsBeforSync, $idsAfterSync);

        if (!empty($viewIdsToRemove)) {
            $symbol_keys = $this->fieldRepo()->getModel()->whereIn('id', $viewIdsToRemove)->lists('symbol_key');
            $this->viewFieldRepo()->deleteByDMSymbolKeys($this->getApp()->DM(), $symbol_keys);
        }

        return new RestResponse($mFields->get());
    }

}