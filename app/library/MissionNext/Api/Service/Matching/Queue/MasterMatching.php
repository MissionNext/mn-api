<?php

namespace MissionNext\Api\Service\Matching\Queue;


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

class MasterMatching
{

    private $matchingRoles = [BaseDataModel::CANDIDATE, BaseDataModel::ORGANIZATION, BaseDataModel::JOB];


    public function fire($job, $data)
    {
        Results::truncate();

        $userId = isset($data["userId"]) ? $data["userId"] : null;
        $appId = $data["appId"];
        $role = $data["role"];

        $roles = array_values(array_diff($this->matchingRoles, [$role]));
        array_unshift($roles, $role);

        foreach($roles as $role){
            $cacheRep = new UserCachedRepository($role);
            $ids = $cacheRep->all()->lists("id");
            $d = ["appId" => $appId, "role" => $role];
            foreach($ids as $id){
                $d["userId"] = $id;
                $m = $this->match($role);
                $m($d);
            }

        }

        $job->delete();

    }

    private function match($role)
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

        }

        return
            function($data){

           };

    }
} 