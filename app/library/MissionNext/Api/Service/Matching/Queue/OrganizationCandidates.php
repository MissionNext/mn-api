<?php

namespace MissionNext\Api\Service\Matching\Queue;

use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\OrganizationCandidates as MatchOrgCandidates;
use MissionNext\Repos\Matching\ConfigRepository;

class OrganizationCandidates extends QueueMatching
{
    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $appId = $data["appId"];


        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());


        $config = $configRepo->configByOrganizationCandidates(BaseDataModel::CANDIDATE, $userId)->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }
        $orgData = (new UserCachedRepository(BaseDataModel::ORGANIZATION))->select('data')->findOrFail($userId);

        if (empty($orgData)) {

            $job->delete();
            return [];
        }

        $orgData = json_decode($orgData->data, true);

        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->dataWithNotes($userId)->get();

        $candidateData = !empty($candidateData) ? array_map(function ($d) {
            $data = json_decode($d->data, true);
            $data['notes'] = $d->notes;
            $data['folder'] = $d->folder;

            return $data;
        }, $candidateData) : [];

        $Matching = new MatchOrgCandidates($orgData, $candidateData, $config);

        $candidateData = $Matching->matchResults();

        Results::where("for_user_id","=", $userId)->where("user_type","=", BaseDataModel::CANDIDATE)->delete();//TODO where user type = ?

        $dateTime = (new \DateTime())->format("Y-m-d H:i:s");

        $insertData = array_map(function($d) use ($userId, $dateTime){
            return
                [
                    "user_type" => BaseDataModel::CANDIDATE,
                    "user_id" => $d['id'],
                    "for_user_id" => $userId,
                    "data" => json_encode($d),
                    "created_at" => $dateTime,
                    "updated_at" => $dateTime,
                ];

        }, $candidateData);

        Results::insert($insertData);

        $job->delete();

        return $candidateData;
    }
} 