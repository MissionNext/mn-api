<?php

namespace App\Modules\Api\MissionNext\Controllers\Translation;

use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Dictionary\BaseDictionary;
use App\Models\Field\IField;
use App\Models\Language\LanguageModel;
use App\Repos\Field\FieldRepository;
use App\Repos\Field\FieldRepositoryInterface;

/**
 * Class FieldController
 * @package App\Modules\Api\Controllers\Translation
 */
class FieldController extends BaseController
{

    public function getIndex($type)
    {
       /** @var  $fieldRepo FieldRepository */
       $fieldRepo = $this->repoContainer[FieldRepositoryInterface::KEY];

       $appLangIds = $this->getApp()->languages->pluck('id');
       array_unshift($appLangIds, 0);

       $transFields = [];
       foreach($appLangIds as $langId){
           array_push($transFields, [
                      'lang_id' => $langId,
                      'fields' => $fieldRepo->fieldsExpandedTrans(LanguageModel::find($langId) ?: new LanguageModel())->get()->toArray(),
                      ] );
       }

       return new RestResponse($transFields);
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function postIndex($type)
    {
        $translations = $this->request->request->get('languages');
        /** @var  $fieldRepo FieldRepository */
        $fieldRepo = $this->repoContainer[FieldRepositoryInterface::KEY];
        /** @var  $fieldModel IField */
        foreach($translations as $trans){
            $fieldModel = $fieldRepo->getModel()->findOrFail($trans["field_id"]);
            $langId = $trans["lang_id"];

            if ($trans['choices'] && is_array($trans['choices']) && $choices = $trans['choices']){
                $defChoicesIds = $fieldModel->choices()->get()->pluck('id');
                $transChoicesIds = array_keys($choices);

                foreach($defChoicesIds as $defId){
                    /** @var  $choice BaseDictionary */
                   $choice =  $fieldModel->choices()->getRelated()->findOrFail($defId);
                   $choice->languages()->detach([$langId]);

                    if (in_array($defId, $transChoicesIds)){

                        $choice->languages()->attach([$langId => [ 'value' => $choices[$defId]] ]);
                    }


                }
            }
            $fieldModel->languages()->detach([ $langId ]);
            $fieldModel->languages()->attach([ $langId => [ 'name' => $trans['label'], 'note' => $trans['note'] ] ]);
        }


        return new RestResponse($fieldModel->languages);
    }

}
