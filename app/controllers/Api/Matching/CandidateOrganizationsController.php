<?php

namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Database\Eloquent\Builder;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\CandidateOrganizations;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;

class CandidateOrganizationsController extends BaseController
{
    /**
     * @param $candidate_id
     *
     * @return RestResponse
     */
    public function getIndex($candidate_id)
    {

        return
            new RestResponse($this->matchingResultsRepo()
                ->matchingResults(BaseDataModel::CANDIDATE, BaseDataModel::ORGANIZATION, $candidate_id));
    }

    public function getLive($candidate_id)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByCandidateOrganizations(BaseDataModel::ORGANIZATION, $candidate_id)->get();


        if (!$config->count()) {

            return new RestResponse([]);
        }
        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->select('data')->findOrFail($candidate_id);
        if (empty($candidateData)) {

            return new RestResponse([]);
        }

        $candidateData = json_decode($candidateData->data, true);

        $organizationData = (new UserCachedRepository(BaseDataModel::ORGANIZATION))->dataWithNotes($candidate_id)->get();
        $organizationData = !empty($organizationData) ? array_map(function ($d) {
            $data = json_decode($d->data, true);
            $data['notes'] = $d->notes;
            $data['folder'] = $d->folder;

            return $data;
        }, $organizationData) : [];

        $Matching = new CandidateOrganizations($candidateData, $organizationData, $config->toArray());

        $orgData = $Matching->matchResults();

        return new RestResponse($orgData);
    }
} 