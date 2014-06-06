<?php

namespace MissionNext\Api\Service\Matching\Queue;


use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\CandidateJobs as MatchCanJobs;
use MissionNext\Repos\Matching\ConfigRepository;

class CandidateJobs extends QueueMatching
{
    protected $userType = BaseDataModel::JOB;

    protected $forUserType = BaseDataModel::CANDIDATE;

    protected $matchingClass = MatchCanJobs::class;

    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $matchingId = isset($data["matchingId"]) ? $data["matchingId"] : null;
        $appId = $data["appId"];
        $this->job = $job;

        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByCandidateJobs(BaseDataModel::JOB, $userId)->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }

        $matchingId ? $this->matchResult($userId, $matchingId, $config)
                    : $this->matchResults($userId,  $config);
    }

} 