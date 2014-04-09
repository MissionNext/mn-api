<?php
namespace Api\Form;

use Api\BaseController;
use MissionNext\Api\Exceptions\FormGroupsException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Models\Field\FieldGroup;
use MissionNext\Models\Form\AppForm;
use MissionNext\Models\Form\BaseForm;
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


        return new RestResponse(["list" => $this->syncGroupFields($reqGroups, $form)]);

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

        if (!$form || !$form->fields()->count()) {

            return new RestResponse(null);
        }
        $form->fields()->get();
       // dd(\DB::select('select "group_fields".*, "app_forms"."data_model_id", "app_data_model".* from "group_fields" inner join "form_groups" on "form_groups"."id" = "group_fields"."group_id" inner join "app_forms" on "app_forms"."id" = "form_groups"."form_id"  inner join "app_data_model" on   "app_data_model"."id" = "app_forms"."data_model_id" where "app_forms"."data_model_id" = ? and  "group_fields"."symbol_key" =?',array(2,'birth_date')));
        $viewFields = $form->fields()->with("formGroup")->orderBy("symbol_key")->get()->toArray();
        $groupFields = array_fetch($viewFields, 'symbol_key');
        $modelFields = $this->fieldRepo()->modelFieldsExpanded()->whereIn("symbol_key", $groupFields)->orderBy("symbol_key")->get()->toArray();
        $mergedData = array_replace_recursive($modelFields, $viewFields);
        $groups = [];
        foreach ($mergedData as $key => $data) {
            if (!isset($data["id"])) {
                continue;
            }
            $symbolKey = $data["form_group"]["symbol_key"];
            $groups[$symbolKey]["symbol_key"] = $data["form_group"]["symbol_key"];
            $groups[$symbolKey]["id"] = $data["form_group"]["id"];
            $groups[$symbolKey]["name"] = $data["form_group"]["name"];
            $groups[$symbolKey]["depends_on"] = $data["form_group"]["depends_on"];
            $groups[$symbolKey]["order"] = $data["form_group"]["order"];
            $groups[$symbolKey]["fields"][$key]["symbol_key"] = $data["symbol_key"];
            $groups[$symbolKey]["fields"][$key]["type"] = $data["type"];
            $groups[$symbolKey]["fields"][$key]["name"] = $data["name"];
            $groups[$symbolKey]["fields"][$key]["choices"] = $data["choices"] ? : [];
            $groups[$symbolKey]["fields"][$key]["order"] = $data["order"];
            $groups[$symbolKey]["fields"][$key]["id"] = $data["id"];
        }
        $groups = array_values($groups);
        foreach ($groups as $key => $group) {
            $groups[$key]["fields"] = array_values($groups[$key]["fields"]);
        }

        return new RestResponse(["list" => $groups]);
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

    }


}