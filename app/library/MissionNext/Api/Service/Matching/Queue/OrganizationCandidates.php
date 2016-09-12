<?php

namespace MissionNext\Api\Service\Matching\Queue;

use MissionNext\Models\Application\Application;
use MissionNext\Models\Configs\UserConfigs;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\OrganizationCandidates as MatchOrgCandidates;
use MissionNext\Repos\Matching\ConfigRepository;
use MissionNext\Api\Service\Matching\Queue\OrganizationCandidates as OrgCandidatesQueue;

class OrganizationCandidates extends QueueMatching
{
    protected $userType = BaseDataModel::CANDIDATE;

    protected $forUserType = BaseDataModel::ORGANIZATION;

    protected $matchingClass = MatchOrgCandidates::class;

    protected $queueClass = OrgCandidatesQueue::class;

    public function fire($job, $data)
    {
        $appId = $data["appId"];
        $userId = $data["userId"];
        $this->job = $job;
        $matchingId = isset($data["matchingId"]) ? $data["matchingId"] : null;
        $offset = isset($data["offset"]) ? $data["offset"] : 0;

        $last_login = null;
        if(isset($data["last_login"]))
            $last_login = $data["last_login"];
        elseif($apdates = UserConfigs::where(['app_id' => $appId, 'user_id' => $userId, 'key' => 'last_login'])->first())
            $last_login = $apdates['value'];

        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByOrganizationCandidates(BaseDataModel::CANDIDATE, $userId)->get();

        if (!$config->count()) {
            $job->delete();
            return [];
        }
        $matchingId ? $this->matchResult($userId, $matchingId, $config)
            : $this->matchResults($userId,  $config, $offset, $last_login);

    }
} 