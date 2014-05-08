<?php


namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\FolderNotes\FolderNotes;
use MissionNext\Models\Matching\Config;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\Field\Field;
use MissionNext\Api\Service\Matching\CandidateJobs;

class CandidateJobsController extends BaseController
{
    /**
     * @param $candidate_id
     *
     * @return RestResponse
     */
    public function getIndex($candidate_id)
    {
        /** @var  $matchResults Builder */
        $matchResults =  Results::matchingResults(BaseDataModel::CANDIDATE, BaseDataModel::JOB, $candidate_id);

        $data = [];
        $matchResults->get()->each(function($el) use (&$data){
             $data[] = json_decode($el->data, true);
        });

        return new RestResponse($data);

    }

}






