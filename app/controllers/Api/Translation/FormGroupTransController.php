<?php


namespace MissionNext\Controllers\Api\Translation;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Form\AppForm;
use MissionNext\Models\Translation\FormGroupTrans;

class FormGroupTransController extends  BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        $data = $this->request->request->all();
        foreach($data['groups'] as $transGroup)
        {
            $dataGroup = new \ArrayObject($transGroup, \ArrayObject::ARRAY_AS_PROPS);
            $dataGroup->offsetSet('app_id', $this->getApp()->id());
            /** @var  $formGroupTrans FormGroupTrans */
            $formGroupTrans =  FormGroupTrans::firstOrNew(
                [
                    'lang_id' => $dataGroup->lang_id,
                    'group_id' => $dataGroup->group_id,
                    'app_id' => $dataGroup->app_id,

                ]);

            $formGroupTrans->value  ?   $formGroupTrans->updateTransData($dataGroup)
                                    :   $formGroupTrans->insertTransData($dataGroup);
        }

        return new RestResponse([$data]);
    }

    /**
     * @param $type
     * @param $formName
     *
     * @return RestResponse
     */
    public function getGroupTrans($type, $formName)
    {
        /** @var  $form AppForm */
        $form = $this->getApp()->DM()->forms()->whereSymbolKey($formName)->firstOrFail();

        $ids = $form->groups()->get()->lists('id') ? : [0];

        $groupTrans =  FormGroupTrans::whereIn('group_id', $ids)->whereAppId($this->getApp()->id())->get();

        return new RestResponse($groupTrans);
    }
} 