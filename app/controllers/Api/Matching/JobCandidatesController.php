<?php

namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Database\Eloquent\Builder;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\JobCandidates;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;

/**
 * Class JobCandidatesController
 * @package MissionNext\Controllers\Api\Matching
 */
class JobCandidatesController extends BaseController
{
    /**
     *
     * @param $jobId
     *
     * @return RestResponse
     */
    public function getIndex($jobId)
    {
        $userAppsIds = $this->securityContext()->getToken()->currentUser()->appIds();

        if (in_array($this->securityContext()->getApp()->id, $userAppsIds)) {

            return
                new RestResponse($this->matchingResultsRepo()
                    ->matchingResults(BaseDataModel::JOB, BaseDataModel::CANDIDATE, $jobId));
        }

        return new RestResponse([]);
    }

    public function getLive($jobId)
    {
        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByJobCandidates(BaseDataModel::JOB, $jobId)->get();

        if (!$config->count()) {

            return new RestResponse([]);
        }
        $jobData = (new UserCachedRepository(BaseDataModel::JOB))->mainData($jobId)->getData();

        //TODO dataWithNotes set owner(organization) id
        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->dataWithNotes()->get()->toArray();


        $Matching = new JobCandidates($jobData, $candidateData, $config->toArray());

        $candidateData = $Matching->matchResults();

        return new RestResponse($candidateData);
    }
} 