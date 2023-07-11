<?php
namespace App\Modules\Api\MissionNext\Controllers\Form;

use App\Modules\Api\Exceptions\FormGroupsException;
use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Field\FieldGroup;
use App\Models\Form\AppForm;
use App\Models\Form\BaseForm;
use App\Models\DataModel\AppDataModel;
use Illuminate\Support\Facades\Request;
use App\Repos\Form\FormRepository;
use App\Repos\Form\FormRepositoryInterface;

/**
 * Class Controller<br>
 * FormController
 * @package pp\Modules\Api\Controllers\Form
 */
class Controller extends BaseController
{

    /**
     * @param $type
     * @param $formName
     *
     * @return RestResponse
     *
     * @throws FormGroupsException
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

        return new RestResponse($this->syncGroupFields($reqGroups, $form));

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

        return new RestResponse($formRepo->structuredGroupFields($form));
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
        $groupSymbolKeys = array_pluck($reqGroups, "symbol_key");

        $groupsInsert = array_map(function ($group) use ($form, $timestamp) {
            return [
                "symbol_key"            => $group["symbol_key"],
                "name"                  => $group["name"],
                "meta"                  => isset($group["meta"]) && !is_string($group['meta']) ? json_encode($group['meta']) : json_encode([]),
                "order"                 => $group["order"],
                "form_id"               => $form->id,
                "depends_on"            => $group["depends_on"],
                "depends_on_option"     => $group["depends_on_option"],
                "is_outer_dependent"    => $group["is_outer_dependent"],
                "created_at"            => $timestamp,
                "updated_at"            => $timestamp,
            ];

        }, $reqGroups);

        $form->groups()->insert($groupsInsert);

        $formGroups = array_replace_recursive($reqGroups, $form->groups()->whereIn('symbol_key', $groupSymbolKeys)->orderBy('id')->get()->toArray());

        $timestamp = (new \DateTime)->format("Y-m-d H:i:s");

        foreach ($formGroups as $group) {

            $fieldsToIns = array_map(function ($field) use ($timestamp, $group) {


                $meta = ["search_options" => ["is_expanded" => false], "before_notes" => [], "after_notes" => [],

                ];
                if (isset($field["is_expanded"])) {
                    $meta["search_options"]["is_expanded"] = (bool)$field["is_expanded"];
                }

                if (isset($field['before_notes'])) {
                    $meta['before_notes'] = $field['before_notes'];
                }
                if (isset($field['after_notes'])) {
                    $meta['after_notes'] = $field['after_notes'];
                }

                return
                    array(
                        "group_id" => $group["id"],
                        "symbol_key" => $field["symbol_key"],
                        "order" => $field["order"],
                        "meta" => json_encode($meta),
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
