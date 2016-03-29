<?php

namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Database\Eloquent\Builder;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\CandidateOrganizations;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;

/**
 * Class CandidateOrganizationsController
 * @package MissionNext\Controllers\Api\Matching
 */
class CandidateOrganizationsController extends BaseController
{
    /**
     * @param $candidate_id
     *
     * @return RestResponse
     */
    public function getIndex($candidate_id)
    {
        $rate = $this->request->get('rate');

        $candidateAppsIds = $this->securityContext()->getToken()->currentUser()->appIds();
        if (in_array($this->securityContext()->getApp()->id, $candidateAppsIds)) {
            return
                new RestResponse($this->matchingResultsRepo()
                    ->matchingResults(BaseDataModel::CANDIDATE, BaseDataModel::ORGANIZATION, $candidate_id, compact('rate')));
        }

        return new RestResponse([]);
    }

    public function getLive($candidate_id)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByCandidateOrganizations(BaseDataModel::ORGANIZATION, $candidate_id)->get();

        if (!$config->count()) {

            return new RestResponse([]);
        }
        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->mainData($candidate_id)->getData();

        $organizationData = (new UserCachedRepository(BaseDataModel::ORGANIZATION))->dataWithNotes($candidate_id)->get()->toArray();


        $Matching = new CandidateOrganizations($candidateData, $organizationData, $config->toArray());

        $orgData = $Matching->matchResults();

        return new RestResponse($orgData);
    }
} 