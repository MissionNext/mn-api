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
    public function getIndex($organizationId)
    {

        /** @var  $matchResults Builder */
        $matchResults =  Results::matchingResults(BaseDataModel::ORGANIZATION, BaseDataModel::CANDIDATE, $organizationId);

        $data = [];
        $matchResults->get()->each(function($el) use (&$data){
            $data[] = json_decode($el->data, true);
        });

        return new RestResponse($data);
    }

    public function getLive($organizationId)
    {
        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByOrganizationCandidates(BaseDataModel::CANDIDATE, $organizationId)->get();


        if (!$config->count()) {

            return new RestResponse([]);
        }

        $orgData = (new UserCachedRepository(BaseDataModel::ORGANIZATION))->select('data')->findOrFail($organizationId);
        if (empty($orgData)) {

            return new RestResponse([]);
        }

        $orgData = json_decode($orgData->data, true);

        $canData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->dataWithNotes($organizationId)->get();

        $canData = !empty($canData) ? array_map(function ($d) {
            $data = json_decode($d->data, true);
            $data['notes'] = $d->notes;
            $data['folder'] = $d->folder;

            return $data;
        }, $canData) : [];

        $Matching = new OrganizationCandidates($orgData, $canData, $config->toArray());

        $canData = $Matching->matchResults();

        return new RestResponse($canData);
    }

}