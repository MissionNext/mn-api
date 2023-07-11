<?php
namespace App\Modules\Api\MissionNext\Controllers\Translation;

use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Form\AppForm;
use App\Models\Translation\FormGroupTrans;
use Illuminate\Support\Facades\DB;

/**
 * Class FormGroupTransController
 * @package App\Modules\Api\MissionNext\Controllers\Translation
 */
class FormGroupTransController extends  BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        $data = $this->request->request->all();
        if (isset($data['groups']) && !empty($data['groups'])) {
            foreach($data['groups'] as $transGroup)
            {
                $dataTrans = new \ArrayObject($transGroup, \ArrayObject::ARRAY_AS_PROPS);
                $dataTrans->offsetSet('app_id', $this->getApp()->id());
                /** @var  $formGroupTrans FormGroupTrans */
                $formGroupTrans =  FormGroupTrans::whereLangId($dataTrans->lang_id)
                    ->whereAppId($dataTrans->app_id)
                    ->whereGroupId($dataTrans->group_id)
                    ->first() ? : new FormGroupTrans();

                $formGroupTrans->app_id  ?   $formGroupTrans->updateTransData($dataTrans)
                    :   $formGroupTrans->insertTransData($dataTrans);
            }
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
        $form = $this->getApp()->DM($type)->forms()->whereSymbolKey($formName)->firstOrFail();

        $ids = $form->groups()->get()->pluck('id')->toArray()  ? : [0];
        $arr = [];

        $groupTrans =  FormGroupTrans::whereIn('group_id',$ids)->whereAppId($this->getApp()->id())->get();

        //$ids = [12,15,9];

        //$groupTrans =  FormGroupTrans::get();

//        $groupTrans =  FormGroupTrans::select("*")
//            ->where('group_id',array_flip($ids))
//            ->get();
     // $groupTrans = DB::table('form_groups_trans')->where('group_id', 27651)->get();

       // dd($ids,$groupTrans,$this->getApp()->id());
        return new RestResponse($groupTrans);
    }
}
