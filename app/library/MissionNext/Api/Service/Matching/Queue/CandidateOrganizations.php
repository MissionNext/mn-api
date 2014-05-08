<?php


namespace MissionNext\Api\Service\Matching\Queue;


use Illuminate\Support\Facades\Queue;
use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\CandidateOrganizations as MatchCanOrgs;
use MissionNext\Repos\Matching\ConfigRepository;

class CandidateOrganizations extends QueueMatching
{
    protected $userType = BaseDataModel::ORGANIZATION;

    protected $forUserType = BaseDataModel::CANDIDATE;

    protected $matchingClass = MatchCanOrgs::class;

    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $appId = $data["appId"];
        $this->job = $job;

        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByCandidateOrganizations(BaseDataModel::ORGANIZATION, $userId)->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }

        $this->matchResults($userId, $config);

    }
} 