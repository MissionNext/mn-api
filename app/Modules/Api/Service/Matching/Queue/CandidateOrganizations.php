<?php
namespace App\Modules\Api\Service\Matching\Queue;

use App\Models\Application\Application;
use App\Models\DataModel\BaseDataModel;
use App\Modules\Api\Service\Matching\CandidateOrganizations as MatchCanOrgs;
use App\Repos\Matching\ConfigRepository;
use App\Modules\Api\Service\Matching\Queue\CandidateOrganizations as CanOrgsQueue;

class CandidateOrganizations extends QueueMatching
{
    protected $userType = BaseDataModel::ORGANIZATION;

    protected $forUserType = BaseDataModel::CANDIDATE;

    protected $matchingClass = MatchCanOrgs::class;

    protected $queueClass = CanOrgsQueue::class;

    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $application = Application::find($data["appId"]);
        $matchingId = isset($data["matchingId"]) ? $data["matchingId"] : null;
        $offset = isset($data["offset"]) ? $data["offset"] : 0;
        $this->job = $job;

        $this->securityContext()->getToken()->setApp($application);

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByCandidateOrganizations()->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }

        $matchingId ? $this->matchResult($userId, $matchingId, $config)
            : $this->matchResults($userId,  $config, $offset);
    }
}
