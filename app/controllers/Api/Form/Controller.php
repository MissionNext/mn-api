<?php
namespace MissionNext\Controllers\Api\Form;

use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\FormGroupsException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\DB\SqlStatement\Sql;
use MissionNext\Models\Field\FieldGroup;
use MissionNext\Models\Form\AppForm;
use MissionNext\Models\Form\BaseForm;
use MissionNext\Models\DataModel\AppDataModel;
use Illuminate\Support\Facades\Request;
use MissionNext\Repos\Form\FormRepository;
use MissionNext\Repos\Form\FormRepositoryInterface;

/**
 * Class Controller
 * @package MissionNext\Controllers\Api\Form
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

        $form = $dm->forms()->whereSymbolKey($formName)->first();

        if (count($reqGroups) < 1 && $form) {

            $form->groups()->delete();

            return new RestResponse($form);
        }

        if ($form) {

            $form->groups()->delete();

        } else {

            $form = new AppForm();
            $form->setName($formName)
                ->setSymbolKey($formName)
                ->dataModel()->associate($dm);

            $form->save();
        }


        return new RestResponse( $this->syncGroupFields($reqGroups, $form) );

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
        $language = $this->getToken()->language();
        $forms = $dm->forms()->whereSymbolKey($formName)->get();
        /** @var  $form AppForm */
        $form = $forms->first();

        if (!$form || !$form->fields()->count()) {

            return new RestResponse(null);
        }
        /** @var  $formRepo FormRepository */
        $formRepo = $this->repoContainer[FormRepositoryInterface::KEY];
        $structuredData = [];
        $groupFields = $formRepo->groupedFields($form)->get();
        foreach($groupFields as $groupField){
            $groupId = $groupField->id;
            $fieldId = $groupField->field_id;
            $structuredData[$groupId]['symbol_key'] = $groupField->symbol_key;
            $structuredData[$groupId]['id'] = $groupId;
            $structuredData[$groupId]['name'] = $groupField->name;
            $structuredData[$groupId]['depends_on'] = $groupField->depends_on;
            $structuredData[$groupId]['is_outer_dependent'] = $groupField->is_outer_dependent;
            $structuredData[$groupId]['order'] = $groupField->order;

            $choices = $groupField->field_choices ? : [];
            $defChoices = $groupField->field_default_choices ? : [];
            $choicesIds = $groupField->field_dictionary_id ? : [];

            $structuredData[$groupId]['fields'][$fieldId]['id'] = $fieldId;
            $structuredData[$groupId]['fields'][$fieldId]['symbol_key'] = $groupField->field_symbol_key;
            $structuredData[$groupId]['fields'][$fieldId]['constraints'] = $groupField->field_constraints;
            $structuredData[$groupId]['fields'][$fieldId]['type'] = $groupField->field_type;
            $structuredData[$groupId]['fields'][$fieldId]['name'] = $groupField->field_name;
            $structuredData[$groupId]['fields'][$fieldId]['choices'] = $choices;
            $structuredData[$groupId]['fields'][$fieldId]['field_dictionary_ids'] = $groupField->field_dictionary_id ? : [];
            $structuredData[$groupId]['fields'][$fieldId]['default_choices'] = $defChoices;
            $structuredData[$groupId]['fields'][$fieldId]['field_default_dictionary_ids'] = $groupField->field_default_dictionary_id ? : [];
            $structuredData[$groupId]['fields'][$fieldId]['default_value'] = $groupField->field_default_value;
            $structuredData[$groupId]['fields'][$fieldId]['order'] = $groupField->field_order;
            $structuredData[$groupId]['fields'][$fieldId]['meta'] = json_decode($groupField->field_meta, true);

            $dictionary = [];
            foreach($choices as $key => $choice){
                $dictionary[$key]['value'] = $choice;
                $dictionary[$key]['default_value'] = $defChoices[$key];
                $dictionary[$key]['id'] = intval($choicesIds[$key]);
                $dictionary[$key]['order'] = 0;
            }

            $structuredData[$groupId]['fields'][$fieldId]['choices'] = $dictionary;

            //@TODO default_value to array
        }


        return new RestResponse($structuredData);



    }

    /**
     * @param array $reqGroups
     * @param BaseForm $form
     *
     * @return array
     */
    protected function syncGroupFields(array $reqGroups, BaseForm $form)
    {
        $timestamp = (new \DateTime)->format("Y-m-d H:i:s");
        $groupSymbolKeys = array_fetch($reqGroups, "symbol_key");

        $groupsInsert = array_map(function ($group) use ($form, $timestamp) {
            return [
                "symbol_key" => $group["symbol_key"],
                "name" => $group["name"],
                "order" => $group["order"],
                "form_id" => $form->id,
                "depends_on" => $group["depends_on"],
                "is_outer_dependent" => $group["is_outer_dependent"],
                "created_at" => $timestamp,
                "updated_at" => $timestamp,
            ];

        }, $reqGroups);

        $form->groups()->insert($groupsInsert);

        $formGroups = array_replace_recursive($reqGroups, $form->groups()->whereIn('symbol_key', $groupSymbolKeys)->get()->toArray());

        $timestamp = (new \DateTime)->format("Y-m-d H:i:s");

        foreach ($formGroups as $group) {

            $fieldsToIns = array_map(function ($field) use ($timestamp, $group) { //@TODO cannot be associated field_group has no id


               $searchOptions =   ["search_options"=>["is_expanded" => false ]];
               if  (isset($field["is_expanded"])){
                  $searchOptions["search_options"]["is_expanded"] = (bool)$field["is_expanded"];
               }

                return
                    array(
                        "group_id" => $group["id"],
                        "symbol_key" => $field["symbol_key"],
                        "order" => $field["order"],
                        "meta" => json_encode($searchOptions),
                        "created_at" => $timestamp,
                        "updated_at" => $timestamp,
                    );
            }, $group["fields"]);

         //   print_r($fieldsToIns); exit;


            FieldGroup::insert($fieldsToIns);
        }

        return $formGroups;

    }


}