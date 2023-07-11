<?php

namespace App\Modules\Api\Service\Matching\Queue;

use App\Models\Application\Application;
use App\Models\DataModel\BaseDataModel;
use App\Modules\Api\Service\Matching\CandidateJobs as MatchCanJobs;
use App\Repos\Matching\ConfigRepository;
use App\Modules\Api\Service\Matching\Queue\CandidateJobs as CanJobsQueue;

class CandidateJobs extends QueueMatching
{
    protected $userType = BaseDataModel::JOB;

    protected $forUserType = BaseDataModel::CANDIDATE;

    protected $matchingClass = MatchCanJobs::class;

    protected $queueClass = CanJobsQueue::class;

    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $application = Application::find($data["appId"]);
        $matchingId = isset($data["matchingId"]) ? $data["matchingId"] : null;
        $offset = isset($data["offset"]) ? $data["offset"] : 0;
        $this->job = $job;

        $this->securityContext()->getToken()->setApp($application);

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByCandidateJobs()->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }

        $matchingId ? $this->matchResult($userId, $matchingId, $config)
            : $this->matchResults($userId,  $config, $offset);
    }

}
