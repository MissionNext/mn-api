<?php


namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Database\Eloquent\Builder;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\OrganizationCandidates;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;

class OrganizationCandidatesController extends BaseController
{
    /**
     * @param $organizationId
     *
     * @return RestResponse
     */
    public function getIndex($organizationId)
    {

        return
            new RestResponse($this->matchingResultsRepo()
                ->matchingResults(BaseDataModel::ORGANIZATION, BaseDataModel::CANDIDATE, $organizationId));
    }

    /**
     * @param $organizationId
     *
     * @return RestResponse
     */
    public function getLive($organizationId)
    {
        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByOrganizationCandidates(BaseDataModel::CANDIDATE, $organizationId)->get();


        if (!$config->count()) {

            return new RestResponse([]);
        }

        $orgData = (new UserCachedRepository(BaseDataModel::ORGANIZATION))->mainData($organizationId)->getData();


        $canData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->dataWithNotes($organizationId)->get()->toArray();

        $Matching = new OrganizationCandidates($orgData, $canData, $config->toArray());

        $canData = $Matching->matchResults();

        return new RestResponse($canData);
    }

}