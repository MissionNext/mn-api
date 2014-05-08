<?php

namespace MissionNext\Api\Service\Matching\Queue;

use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\JobCandidates as MatchJobCandidates;
use MissionNext\Repos\Matching\ConfigRepository;

class JobCandidates extends QueueMatching
{
    protected $userType = BaseDataModel::CANDIDATE;

    protected $forUserType = BaseDataModel::JOB;

    protected $matchingClass = MatchJobCandidates::class;

    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $appId = $data["appId"];
        $this->job = $job;

        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByJobCandidates(BaseDataModel::JOB, $userId)->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }

        $this->matchResults($userId, $config);
    }
} 