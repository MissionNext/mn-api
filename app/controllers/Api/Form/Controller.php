<?php
namespace Api\Form;

use Api\BaseController;
use MissionNext\Api\Exceptions\FormGroupsException;
use MissionNext\Api\Response\RestData;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Input;
use MissionNext\ModelObservers\FormGroupObserve;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\FieldGroup;
use MissionNext\Models\Form\AppForm;
use MissionNext\Models\Form\BaseForm;
use MissionNext\Models\Form\FormGroup;
use MissionNext\Models\DataModel\AppDataModel;
use Illuminate\Support\Facades\Request;

/**
 * Class Controller
 * @package Api\Profile
 * @description Field Controller
 */
class Controller extends BaseController
{

    /**
     * @param $type
     * @param $formName
     *
     * @return RestResponse
     *
     * @throws \MissionNext\Api\Exceptions\FormGroupsException
     */
    public function putIndex($type, $formName)
    {
        $application = $this->securityContext()->getApp();
        /** @var  $dm AppDataModel */
        $dm = $application->DM();

        $reqGroups = Request::instance()->request->get("groups");

        if (count($reqGroups) < 1) {

            throw new FormGroupsException("No groups specified", FormGroupsException::ON_CREATE);
        }

        $form = $dm->forms()->whereSymbolKey($formName)->first();

        $formGroups = [];

        $syncGroupFields = function (array $reqGroups, BaseForm $form) use (&$formGroups) {

            $timestamp = (new \DateTime)->format("Y-m-d H:i:s");

            $groupSymbolKeys = array_fetch($reqGroups, "symbol_key");

            $groupsInsert = array_map(function ($group) use ($form, $timestamp) {
                return [
                    "symbol_key" => $group["symbol_key"],
                    "name" => $group["name"],
                    "order" => $group["order"],
                    "form_id" => $form->id,
                    "created_at" => $timestamp,
                    "updated_at" => $timestamp,
                ];

            }, $reqGroups);

            $form->groups()->insert($groupsInsert);

            $formGroups = array_replace_recursive($reqGroups, $form->groups()->whereIn('symbol_key', $groupSymbolKeys)->get()->toArray());

            $timestamp = (new \DateTime)->format("Y-m-d H:i:s");

            foreach ($formGroups as $group) {

                $fieldsToIns = array_map(function ($field) use ($timestamp, $group) {
                    return
                        array(
                            "group_id" => $group["id"],
                            "symbol_key" => $field["symbol_key"],
                            "order" => $field["order"],
                            "created_at" => $timestamp,
                            "updated_at" => $timestamp,
                        );
                }, $group["fields"]);

                FieldGroup::insert($fieldsToIns);

            }

            return $formGroups;

        };


        if ($form) {
            $form->groups()->delete();

            $syncGroupFields($reqGroups, $form);

        } else {

            $form = new AppForm();
            $form->setName($formName)
                ->setSymbolKey($formName)
                ->dataModel()->associate($dm);

            $form->save();

            $syncGroupFields($reqGroups, $form);


        }


        return new RestResponse($formGroups);



    }

    /**
     * @param $type
     * @param $formName
     *
     * @return RestResponse
     */
    public function getIndex($type, $formName)
    {

        $application = $this->securityContext()->getApp();
        /** @var  $dm AppDataModel */
        $dm = $application->DM();

        $forms = $dm->forms()->whereSymbolKey($formName)->get();

        /** @var  $form AppForm */

        $form = $forms->first();


        if (!$form) {

            return new RestResponse(null);
        }
        $groupFields = array_fetch($form->fields()->get()->toArray(), 'symbol_key');

        $returnData = !empty($groupFields)
            ? $this->fieldsChoicesArr($dm->fieldsExp()->whereIn("symbol_key", $groupFields)->get())
            : null;

        return new RestResponse($returnData);
    }


}