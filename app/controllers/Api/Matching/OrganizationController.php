<?php

namespace MissionNext\Controllers\Api\Matching;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\CandidateOrganizations;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Repos\CachedData\UserCachedRepository;

class OrganizationController extends BaseController
{

    public function getIndex($candidate_id)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByCandidate(BaseDataModel::ORGANIZATION, $candidate_id)->get();


        if (!$config->count()) {

            return new RestResponse([]);
        }
        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->select('data')->findOrFail($candidate_id);
        if (empty($candidateData)) {

            return new RestResponse([]);
        }

        $candidateData = json_decode($candidateData->data, true);

        $organizationData = (new UserCachedRepository(BaseDataModel::ORGANIZATION))->jobDataWithNotes($candidate_id)->get();

        $organizationData = !empty($organizationData) ? array_map(function ($d) {
            $data = json_decode($d->data, true);
            $data['notes'] = $d->notes;
            $data['folder'] = $d->folder;

            return $data;
         }, $organizationData) : [];

        $Matching = new CandidateOrganizations($candidateData, $organizationData, $config);

        $orgData = $Matching->matchResults();

        return new RestResponse($orgData);
    }
} 