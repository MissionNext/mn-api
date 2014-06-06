<?php


namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
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
        $candidateAppsIds = $this->securityContext()->getToken()->currentUser()->appIds();
        if (in_array($this->securityContext()->getApp()->id, $candidateAppsIds)){

            return
                new RestResponse($this->matchingResultsRepo()
                    ->matchingResults(BaseDataModel::CANDIDATE, BaseDataModel::JOB, $candidate_id));
        }

        return
            new RestResponse([]);
    }

    /**
     * @param $candidate_id
     *
     * @return RestResponse
     */
    public function getLive($candidate_id)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByCandidateJobs(BaseDataModel::JOB, $candidate_id)->get();

        if (!$config->count()) {

            return new RestResponse([]);
        }

        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))
                ->mainData($candidate_id)
                ->getData();

        $jobData = (new UserCachedRepository(BaseDataModel::JOB))->dataWithNotes($candidate_id)->get()->toArray();

        //@TODO add app to users when updates profile

        $Matching = new CandidateJobs($candidateData, $jobData, $config->toArray());

        $jobData = $Matching->matchResults();

        return new RestResponse($jobData);
    }

}






