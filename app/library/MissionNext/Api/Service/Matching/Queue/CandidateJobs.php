<?php

namespace MissionNext\Api\Service\Matching\Queue;



use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Api\Service\Matching\CandidateJobs as MatchCanJobs;
use MissionNext\Repos\Matching\ConfigRepository;

class CandidateJobs extends QueueMatching
{
    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $appId = $data["appId"];

        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByCandidateJobs(BaseDataModel::JOB, $userId)->get();

        if (!$config->count()) {

            $job->delete();
            return [];
        }
        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->select('data')->findOrFail($userId);

        if (empty($candidateData)) {

            $job->delete();
            return [];
        }

        $candidateData = json_decode($candidateData->data, true);

        $jobData = (new UserCachedRepository(BaseDataModel::JOB))->dataWithNotes($userId)->get();

        $jobData = !empty($jobData) ? array_map(function ($d) {
            $data = json_decode($d->data, true);
            $data['notes'] = $d->notes;
            $data['folder'] = $d->folder;

            return $data;
        }, $jobData) : [];

        $Matching = new MatchCanJobs($candidateData, $jobData, $config);

        $jobData = $Matching->matchResults();

        Results::where("for_user_id","=", $userId)->where("user_type","=", BaseDataModel::JOB)->delete();//TODO where user type = ?

        $dateTime = (new \DateTime())->format("Y-m-d H:i:s");

        $insertData = array_map(function($jD) use ($userId, $dateTime){
              return
                  [
                    "user_type" => BaseDataModel::JOB,
                    "user_id" => $jD['id'],
                    "for_user_id" => $userId,
                    "data" => json_encode($jD),
                    "created_at" => $dateTime,
                    "updated_at" => $dateTime,
                  ];

        }, $jobData);

        Results::insert($insertData);

        $job->delete();

        return $jobData;
    }

} 