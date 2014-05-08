<?php

namespace MissionNext\Api\Service\Matching\Queue;

use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\OrganizationCandidates as MatchOrgCandidates;
use MissionNext\Repos\Matching\ConfigRepository;

class OrganizationCandidates extends QueueMatching
{
    protected $userType = BaseDataModel::CANDIDATE;

    protected $forUserType = BaseDataModel::ORGANIZATION;

    protected $matchingClass = MatchOrgCandidates::class;


    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $appId = $data["appId"];
        $this->job = $job;


        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());


        $config = $configRepo->configByOrganizationCandidates(BaseDataModel::CANDIDATE, $userId)->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }

        $this->matchResults($userId, $config);
    }
} 