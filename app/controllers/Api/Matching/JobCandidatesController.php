<?php

namespace MissionNext\Controllers\Api\Matching;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\JobCandidates;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
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

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByJobCandidates(BaseDataModel::JOB, $jobId)->get();

        if (!$config->count()) {

            return new RestResponse([]);
        }
        $jobData = (new UserCachedRepository(BaseDataModel::JOB))->select('data')->findOrFail($jobId);
        if (empty($jobData)) {

            return new RestResponse([]);
        }

        $jobData = json_decode($jobData->data, true);
        //TODO dataWithNotes set owner(organization) id
        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->dataWithNotes(0)->get();

        $candidateData = !empty($candidateData) ? array_map(function ($d) {
            $data = json_decode($d->data, true);
            $data['notes'] = $d->notes;
            $data['folder'] = $d->folder;

            return $data;
        }, $candidateData) : [];


        $Matching = new JobCandidates($jobData, $candidateData, $config->toArray());

        $candidateData = $Matching->matchResults();

        return new RestResponse($candidateData);
    }
} 