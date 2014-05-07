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
    public function fire($job, $data)
    {
        $userId = $data["userId"];
        $appId = $data["appId"];

        $this->securityContext()->getToken()->setApp(Application::find($appId));

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());

        $config = $configRepo->configByCandidateOrganizations(BaseDataModel::ORGANIZATION, $userId)->get();

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
        //=========

        Results::where("for_user_id","=", $userId)
                ->where("for_user_type","=", BaseDataModel::CANDIDATE)
                ->where("user_type","=", BaseDataModel::ORGANIZATION)
                ->delete();//TODO where user type = ?

        $organizationCacheRep = new UserCachedRepository(BaseDataModel::ORGANIZATION);

        $limit = 3;
        $queries = ceil($organizationCacheRep->count() / $limit);

        for($i=1; $i <= $queries; ++$i){

            $offset = ($i - 1) * $limit;
            $orgData = $organizationCacheRep->dataWithNotes($userId)->take($limit)->skip($offset)->get();

            $orgData = !empty($orgData) ? array_map(function ($d) {
                $data = json_decode($d->data, true);
                $data['notes'] = $d->notes;
                $data['folder'] = $d->folder;

                return $data;
            }, $orgData) : [];
            //Queue::push(TestQueue::class, [$i]);
            $Matching = new MatchCanOrgs($candidateData, $orgData, $config);

            $orgData = $Matching->matchResults();
            $dateTime = (new \DateTime())->format("Y-m-d H:i:s");

            if (!empty($orgData)){
                $insertData = array_map(function($d) use ($userId, $dateTime){
                    return
                        [
                            "user_type" => BaseDataModel::ORGANIZATION,
                            "user_id" => $d['id'],
                            "for_user_id" => $userId ,
                            "for_user_type" => BaseDataModel::CANDIDATE,
                            "matching_percentage" => $d['matching_percentage'],
                            "data" => json_encode($d),
                            "created_at" => $dateTime,
                            "updated_at" => $dateTime,
                        ];

                }, $orgData);

                Results::insert($insertData);
            }
        }

        $job->delete();

    }
} 