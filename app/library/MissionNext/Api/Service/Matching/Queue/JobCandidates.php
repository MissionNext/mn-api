<?php

namespace MissionNext\Api\Service\Matching\Queue;

use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\JobCandidates as MatchJobCandidates;
use MissionNext\Repos\Matching\ConfigRepository;

class JobCandidates extends QueueMatching
{
    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $appId = $data["appId"];

        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByJobCandidates(BaseDataModel::JOB, $userId)->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }
        $jobData = (new UserCachedRepository(BaseDataModel::JOB))->select('data')->findOrFail($userId);

        if (empty($jobData)) {

            $job->delete();
            return [];
        }

        $jobData = json_decode($jobData->data, true);

        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->dataWithNotes(0)->get();

        $candidateData = !empty($candidateData) ? array_map(function ($d) {
            $data = json_decode($d->data, true);
            $data['notes'] = $d->notes;
            $data['folder'] = $d->folder;

            return $data;
        }, $candidateData) : [];

        $Matching = new MatchJobCandidates($jobData, $candidateData, $config);

        $candidateData = $Matching->matchResults();

        if (empty($candidateData)){

            return [];
        }

        Results::where("for_user_id","=", $userId)
                ->where("for_user_type","=", BaseDataModel::JOB)
                ->where("user_type","=", BaseDataModel::CANDIDATE)
                ->delete();//TODO where user type = ?

        $dateTime = (new \DateTime())->format("Y-m-d H:i:s");

        $insertData = array_map(function($d) use ($userId, $dateTime){
            return
                [
                    "user_type" => BaseDataModel::CANDIDATE,
                    "user_id" => $d['id'],
                    "for_user_id" => $userId ,
                    "for_user_type" => BaseDataModel::JOB,
                    "matching_percentage" => $d['matching_percentage'],
                    "data" => json_encode($d),
                    "created_at" => $dateTime,
                    "updated_at" => $dateTime,
                ];

        }, $candidateData);

        Results::insert($insertData);

        $job->delete();

        return $jobData;
    }
} 