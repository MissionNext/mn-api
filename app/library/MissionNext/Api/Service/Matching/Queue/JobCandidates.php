<?php

namespace MissionNext\Api\Service\Matching\Queue;

use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\JobCandidates as MatchJobCandidates;
use MissionNext\Repos\Matching\ConfigRepository;
use MissionNext\Api\Service\Matching\Queue\JobCandidates as JobCandidatesQueue;

class JobCandidates extends QueueMatching
{
    protected $userType = BaseDataModel::CANDIDATE;

    protected $forUserType = BaseDataModel::JOB;

    protected $matchingClass = MatchJobCandidates::class;

    protected $queueClass = JobCandidatesQueue::class;

    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $matchingId = isset($data["matchingId"]) ? $data["matchingId"] : null;
        $appId = $data["appId"];
        $offset = isset($data["offset"]) ? $data["offset"] : 0;
        $this->job = $job;

        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByJobCandidates()->get();
        if (!$config->count()) {

            $job->delete();
            return [];
        }

        $matchingId ? $this->matchResult($userId, $matchingId, $config)
            : $this->matchResults($userId,  $config, $offset);
    }
} 