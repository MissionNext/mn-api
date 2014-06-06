<?php

namespace MissionNext\Api\Service\Matching\Queue\Master;

use Illuminate\Support\Facades\Queue;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Facade\SecurityContext as fs;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Application\Application;
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
                        Queue::push(CanJobsQueue::class, $data);
                        Queue::push(CanOrgsQueue::class, $data);
                    };
                break;
            case BaseDataModel::ORGANIZATION:
                return
                    function($data){
                        Queue::push(OrgCandidatesQueue::class, $data);
                    };
                break;
            case BaseDataModel::JOB:
                return
                    function($data){
                        Queue::push(JobCandidatesQueue::class, $data);
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
        Results::truncate();

        $appId = $data["appId"];
        $role = $data["role"];

        $m = $this->match($role);
        $m($data);

        $roles = array_values(array_diff($this->matchingRoles, [$role]));
        array_unshift($roles, $role);
        foreach($roles as $role){
            $cacheRep = new UserCachedRepository($role);
            $ids = $cacheRep->all()->lists("id");
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