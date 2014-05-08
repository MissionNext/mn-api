<?php

namespace MissionNext\Api\Service\Matching\Queue;


use Illuminate\Support\Facades\Queue;
use MissionNext\Facade\SecurityContext;
use MissionNext\Api\Service\Matching\CandidateOrganizations as MatchCanOrgs;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Api\Service\Matching\Matching as ServiceMatching;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;

abstract class QueueMatching
{

    protected $userType,
              $forUserType,
              $matchingClass,
              $job;



    const QUERY_LIMIT = 3;
    /**
     * @return \MissionNext\Api\Auth\SecurityContext
     */
    protected  function securityContext()
    {


        return SecurityContext::getInstance();
    }

    protected function matchResults($userId, $config)
    {
        $mainData = (new UserCachedRepository($this->forUserType))->select('data')->findOrFail($userId);

        if (empty($mainData)) {

            $this->job->delete();

            return [];
        }

        $mainData = json_decode($mainData->data, true);

        //=========

        $this->clearCache($userId);

        $cacheRep = new UserCachedRepository($this->userType);

        $limit = static::QUERY_LIMIT;

        $queries = ceil($cacheRep->count() / $limit);

        for($i=1; $i <= $queries; ++$i) {

            $offset = ($i - 1) * $limit;
            $matchingData = $cacheRep->dataWithNotes($this->forUserType === BaseDataModel::JOB ? null : $userId )->take($limit)->skip($offset)->get();

            $matchingData = !empty($matchingData) ? array_map(function ($d) {
                $data = json_decode($d->data, true);
                $data['notes'] = $d->notes;
                $data['folder'] = $d->folder;

                return $data;
            }, $matchingData) : [];

            $data = [
                "mainData" => $mainData,
                "matchingData" => $matchingData,
                "matchingClass" =>$this->matchingClass,
                "forUserType" => $this->forUserType,
                "userType" => $this->userType,
                "config" => $config->toArray(),
                "userId" => $userId,
            ];

            Queue::push(InsertQueue::class, $data);

        }

        $this->job->delete();
    }

    protected function clearCache($userId)
    {
        Results::where("for_user_id","=", $userId)
            ->where("for_user_type","=", $this->forUserType)
            ->where("user_type","=", $this->userType)
            ->delete();
    }
} 