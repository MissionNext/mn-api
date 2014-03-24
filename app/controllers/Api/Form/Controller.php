<?php
namespace Api\Form;

use Api\BaseController;
use MissionNext\Api\Response\RestData;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Input;
use MissionNext\Models\Form\AppForm;
use MissionNext\Models\Form\FormGroup;
use MissionNext\Models\DataModel\AppDataModel;

use MissionNext\Repos\Field\FieldRepository;

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
     */
    public function putIndex($type, $formName)
    {

        $fields = Input::get("fields", []);

        $application = $this->securityContext()->getApp();
        /** @var  $dm AppDataModel */
        $dm = $application->DM();
        $forms = $dm->forms()->whereSymbolKey($formName)->get();
        /** @var  $form AppForm */
        if ($forms->count()) {
            $form = $forms->first();
            $group = $form->groups()->first();
        } else {
            $form = new AppForm();
            $form->setName($formName)
                ->setSymbolKey($formName)
                ->dataModel()->associate($dm);

            $form->save();

            $group = new FormGroup();
            $group->setName($formName)
                ->setSymbolKey($formName)
                ->form()->associate($form);

            $group->save();
        }

        $groupFields = array_fetch($group->fields()->get()->toArray(), 'symbol_key');

        $fieldsIns = array_diff($fields, $groupFields);
        $fieldsRem = array_diff($groupFields, $fields);
        // dd($fields, $groupFields,  $fieldsIns, $fieldsRem);

        $groupId = $group->id;

        $fieldsToIns = array_map(function ($key) use ($fieldsIns, $groupId) {
            return
                array(
                    "group_id" => $groupId,
                    "symbol_key" => $key,
                );
        }, $fieldsIns);
        // dd($fieldsToIns);


        $fieldsToIns && $group->fields()->insert($fieldsToIns);

        $fieldsRem && $group->fields()
            ->whereIn('symbol_key', $fieldsRem)
            ->delete();

        RestData::withQueryLog();

        return new RestResponse($group->fields()->get()); //@TODO cache query or return from array

    }

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
              ?  $this->fieldsChoicesArr($dm->fieldsExp()->whereIn("symbol_key", $groupFields)->get())
              :  null;

        return new RestResponse($returnData);
    }


}