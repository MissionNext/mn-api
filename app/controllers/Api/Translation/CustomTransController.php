<?php


namespace MissionNext\Controllers\Api\Translation;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Translation\CustomTrans;

class CustomTransController extends BaseController
{

    public function postIndex()
    {
        $data = $this->request->request->all();
        foreach($data['custom'] as $transGroup)
        {
            $dataTrans = new \ArrayObject($transGroup, \ArrayObject::ARRAY_AS_PROPS);
            $dataTrans->offsetSet('app_id', $this->getApp()->id());
            $dataTrans->lang_id = !$dataTrans->lang_id ? null : $dataTrans->lang_id;
            /** @var  $customTrans CustomTrans */
            $customTrans =  CustomTrans::firstOrNew(
                [
                    'lang_id' => $dataTrans->lang_id,
                    'app_id' => $dataTrans->app_id,
                    'key' => $dataTrans->key

                ]);

            $customTrans->value  ?   $customTrans->updateTransData($dataTrans)
                                 :   $customTrans->insertTransData($dataTrans);


        }

        return new RestResponse([$data]);
    }
} 