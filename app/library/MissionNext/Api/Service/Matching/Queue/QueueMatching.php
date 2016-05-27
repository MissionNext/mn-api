<?php

namespace MissionNext\Api\Service\Matching\Queue;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Queue;
use MissionNext\Facade\SecurityContext;
use MissionNext\Api\Service\Matching\CandidateOrganizations as MatchCanOrgs;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Api\Service\Matching\Matching as ServiceMatching;
use MissionNext\Models\Matching\Results;
use MissionNext\Models\User\User;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\FormGroup\FormGroupRepository;

abstract class QueueMatching
{

    protected $userType,
              $forUserType,
              $matchingClass,
              $job;



    const QUERY_LIMIT = 10;
    /**
     * @return \MissionNext\Api\Auth\SecurityContext
     */
    protected  function securityContext()
    {


        return SecurityContext::getInstance();
    }

    /**
     * @param $userId
     * @param $matchingId
     * @param $config
     */
    protected function matchResult($userId, $matchingId ,$config)
    {
        try{
            $mainData = (new UserCachedRepository($this->forUserType))->mainData($userId)->getData();

        }catch (ModelNotFoundException $e){

            $this->job->delete();
            return [];
        }
        //=========

        $this->clearCache($userId, $matchingId);

        try{
            $matchingData = [(new UserCachedRepository($this->userType))->mainData($matchingId)->getData()];


        }catch (ModelNotFoundException $e){

            $this->job->delete();
            return [];
        }

        $app_id = $this->securityContext()->getApp()->id;

        $data = [
            "mainData"          => $mainData,
            "matchingData"      => $matchingData,
            "matchingClass"     => $this->matchingClass,
            "forUserType"       => $this->forUserType,
            "userType"          => $this->userType,
            "config"            => $config->toArray(),
            "userId"            => $userId,
            "app_id"            => $app_id
        ];

        Queue::push(InsertQueue::class, $data);

        $this->job->delete();
    }

    /**
     * @param $userId
     * @param $config
     * @param $last_login
     */
    protected function matchResults($userId, $config, $last_login = null)
    {

        try{
            $mainData = (new UserCachedRepository($this->forUserType))->mainData($userId)->getData();

        }catch (ModelNotFoundException $e){

            $this->job->delete();
            return [];
        }
        //=========

        $this->clearCache($userId);

        $cacheRep = new UserCachedRepository($this->userType);

        $limit = static::QUERY_LIMIT;
        $queries = ceil($cacheRep->count($last_login) / $limit);

        $app_id = $this->securityContext()->getApp()->id;

        for($i=1; $i <= $queries; ++$i) {

            $offset = ($i - 1) * $limit;
            $matchingData = $cacheRep->data($last_login)
                ->takeAndSkip($limit, $offset)
                ->get()
                ->toArray();


            $tempMatchData = [];
            foreach ($matchingData as $data) {
                if (BaseDataModel::JOB == $data['role']) {
                    $organization_id = $data['organization']['id'];
                } else {
                    $organization_id = $data['id'];
                }

                if (in_array($data['role'], [ BaseDataModel::ORGANIZATION, BaseDataModel::JOB ])) {
                    $user = User::find($organization_id);

                    if ($user->isActive() && $user->isActiveInApp($this->securityContext()->getApp())) {
                        $tempMatchData[] = $data;
                    }
                } else {
                    $tempMatchData[] = $data;
                }
            }

            $matchingData = $tempMatchData;

            $data = [
                "mainData"          => $mainData,
                "matchingData"      => $matchingData,
                "matchingClass"     => $this->matchingClass,
                "forUserType"       => $this->forUserType,
                "userType"          => $this->userType,
                "config"            => $config->toArray(),
                "userId"            => $userId,
                "app_id"            => $app_id
            ];

            Queue::push(InsertQueue::class, $data);
        }

        $this->job->delete();
    }

    /**
     * @param $userId
     * @param null $matchingId
     */
    protected function clearCache($userId, $matchingId = null)
    {
       $builder =  Results::where("for_user_id","=", $userId)
            ->where("for_user_type","=", $this->forUserType)
            ->where("user_type","=", $this->userType);

       $builder = $matchingId ? $builder->where("user_id", "=", $matchingId) : $builder;

       $builder->delete();
    }
} 