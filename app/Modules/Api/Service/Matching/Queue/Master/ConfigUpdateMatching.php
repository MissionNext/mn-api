<?php

namespace App\Modules\Api\Service\Matching\Queue\Master;

use Illuminate\Support\Facades\Queue;

use App\Modules\Api\Filter\RouteSecurityFilter;
use App\Models\CacheData\UserCachedData;
use App\Models\DataModel\BaseDataModel;
use App\Modules\Api\Service\Matching\Queue\OrganizationCandidates as OrgCandidatesQueue;
use App\Modules\Api\Service\Matching\Queue\JobCandidates as JobCandidatesQueue;


class ConfigUpdateMatching extends MasterMatching
{
    protected  function match($role)
    {

        switch($role){
            case BaseDataModel::ORGANIZATION:
                return
                    function($data){
                        $this->clearCache($data['appId'], $data['userId'], 'organization', 'candidate');
                        Queue::push(OrgCandidatesQueue::class, $data);
                    };
                break;
            case BaseDataModel::JOB:
                return
                    function($data){
                        $this->clearCache($data['appId'], $data['userId'], 'job', 'candidate');
                        Queue::push(JobCandidatesQueue::class, $data);
                    };
                break;
        }

    }

    public static function run($queueData)
    {
        parent::run($queueData);
    }

    public function fire($job, $data)
    {
        $appId = $data["appId"];

        $cache = UserCachedData::table($data['role']);
        $ids = $cache->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [$appId])->orderBy('id', 'asc')->pluck("id");
        $d = ["appId" => $appId, "role" => $data['role'], "userId" => null];
        foreach($ids as $id){
            $d["userId"] = $id;
            $m = $this->match($data['role']);
            $m($d);
        }

        $job->delete();

    }
}
