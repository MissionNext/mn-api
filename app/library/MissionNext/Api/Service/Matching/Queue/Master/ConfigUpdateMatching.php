<?php

namespace MissionNext\Api\Service\Matching\Queue\Master;

use Illuminate\Support\Facades\Queue;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Facade\SecurityContext as fs;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Application\Application;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Api\Service\Matching\Queue\CandidateJobs as CanJobsQueue;
use MissionNext\Api\Service\Matching\Queue\CandidateOrganizations as CanOrgsQueue;
use MissionNext\Api\Service\Matching\Queue\OrganizationCandidates as OrgCandidatesQueue;
use MissionNext\Api\Service\Matching\Queue\JobCandidates as JobCandidatesQueue;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;

class ConfigUpdateMatching extends MasterMatching
{
    protected  function match($role)
    {

        switch($role){
            case BaseDataModel::CANDIDATE:
                return
                    function($data){
                        $this->clearCache($data['userId'], 'candidate', 'organization');
                        $this->clearCache($data['userId'], 'candidate', 'job');
                        Queue::push(CanJobsQueue::class, $data);
                        Queue::push(CanOrgsQueue::class, $data);
                    };
                break;
            default:
                return
                    function($data){

                    };

        }

    }

    public static function run($queueData)
    {
        parent::run($queueData);
    }

    public function fire($job, $data)
    {
        $appId = $data["appId"];

        foreach ($this->matchingRoles as $role) {

            $cache = UserCachedData::table($role);
            $ids = $cache->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [$appId])->orderBy('id', 'asc')->lists("id");
            $d = ["appId" => $appId, "role" => $role, "userId" => null];
            foreach($ids as $id){
                $d["userId"] = $id;
                $m = $this->match($role);
                $m($d);
            }
        }

        $job->delete();

    }
}