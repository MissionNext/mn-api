<?php
namespace App\Modules\Api\Service\Matching\Queue;

use App\Models\Application\Application;
use App\Models\DataModel\BaseDataModel;
use App\Models\Job\Job;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Modules\Api\Service\Matching\JobCandidates as MatchJobCandidates;
use App\Repos\Matching\ConfigRepository;
use App\Modules\Api\Service\Matching\Queue\JobCandidates as JobCandidatesQueue;

class JobCandidates extends QueueMatching
{
    protected $userType = BaseDataModel::CANDIDATE;

    protected $forUserType = BaseDataModel::JOB;

    protected $matchingClass = MatchJobCandidates::class;

    protected $queueClass = JobCandidatesQueue::class;

    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $jobItem = Job::find($userId);
        $application = Application::find($data['appId']);
        if ($jobItem) {
            $organization = User::find($jobItem['organization_id']);
            if ($organization) {
                $subscription = $organization->subscriptions()
                    ->where('status', Subscription::STATUS_ACTIVE)
                    ->where('app_id', $data["appId"])->first();

                if ($organization->isActive() && $organization->isActiveInApp($application) && $subscription) {
                    $matchingId = isset($data["matchingId"]) ? $data["matchingId"] : null;
                    $offset = isset($data["offset"]) ? $data["offset"] : 0;
                    $this->job = $job;

                    $this->securityContext()->getToken()->setApp($application);

                    $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

                    $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

                    $config = $configRepo->configByJobCandidates()->get();
                    if (!$config->count()) {

                        $job->delete();
                        return [];
                    }

                    $matchingId ? $this->matchResult($userId, $matchingId, $config)
                        : $this->matchResults($userId,  $config, $offset);
                } else {
                    $job->delete();
                }
            } else {
                $job->delete();
            }
        } else {
            $job->delete();
        }
    }
}
