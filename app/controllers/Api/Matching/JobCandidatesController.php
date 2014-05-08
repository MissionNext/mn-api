<?php

namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Database\Eloquent\Builder;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\JobCandidates;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;

class JobCandidatesController extends BaseController
{
    /**
     *
     * @param $jobId
     * @return RestResponse
     */
    public function getIndex($jobId)
    {
        /** @var  $matchResults Builder */
        $matchResults =  Results::matchingResults(BaseDataModel::JOB, BaseDataModel::CANDIDATE, $jobId);

        $data = [];
        $matchResults->get()->each(function($el) use (&$data){
            $data[] = json_decode($el->data, true);
        });

        return new RestResponse($data);

    }
} 