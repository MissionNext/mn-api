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
} 