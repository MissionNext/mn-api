<?php

namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Support\Facades\Input;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Field\Candidate;
use MissionNext\Models\Matching\Config;
use MissionNext\Models\Field\Candidate as CandidateFieldModel;

class ConfigController extends BaseController
{
    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function getIndex($type)
    {
        return new RestResponse($this->matchingConfigRepo()->getModel()->all());
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function postIndex($type)
    {
        $candidateFieldId = Input::get("candidate_field_id");
        $matchingFieldId = Input::get("matching_field_id");
        $weight = Input::get("weight");
        $matchingType = Input::get("matching_type");

        $model = new Config();
        $model->setMatchingType($matchingType);
        $model->setWeight($weight);
        $candidateField = (new CandidateFieldModel())->findOrFail($candidateFieldId);
        $model->candidateField()->associate($candidateField);
        $matchingField = $model->matchingField()->getRelated()->findOrFail($matchingFieldId);
        $model->matchingField()->associate($matchingField);
        $model->application()->associate($this->getApp());
        $model->save();

        return new RestResponse($model);
    }
} 