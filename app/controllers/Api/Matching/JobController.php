<?php


namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Support\Facades\DB;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Config;

class JobController extends BaseController
{

    public function getIndex($candidate_id)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config =  $configRepo->configByCandidate(BaseDataModel::JOB, $candidate_id)->get();
        if ($config->count()){
           $candidateData =  DB::select("SELECT data FROM user_cached_profile WHERE user_id = ? AND type = ? ", [$candidate_id ,BaseDataModel::CANDIDATE]);

           if (!empty($candidateData)){
               $candidateData =  json_decode( $candidateData[0]->data, true );
               $jobData = DB::select("SELECT data FROM user_cached_profile WHERE type = ? ", [BaseDataModel::JOB]);
               $jobData = !empty($jobData) ? array_map(function($d){ return json_decode($d->data, true); } , $jobData) : [];

               //dd($jobData);
               //dd($candidateData, $jobData[0], $config->toArray());
               $matcher = [];
               $bannedJobIds = [];
            // dd($config->toArray());
               $configArr = $config->toArray();

               foreach( $jobData as $k=>$data){
                   foreach($configArr as $conf){
                       if (isset($data['profileData'][$conf['job_key']])){
                           if (isset($candidateData['profileData'][$conf['candidate_key']])){
                               $jobValue =  $candidateData['profileData'][$conf['candidate_key']];
                               $canValue =  $data['profileData'][$conf['job_key']];
                               if ( $jobValue !== $canValue && $conf["weight"] == 5 ){
                                   $bannedJobIds[] = $data["id"];
                                   continue;
                               }

                               if ($jobValue !== $canValue){

                                   $jobData[$k]["profileData"][$conf['job_key']] = ["value"=>$jobValue, "matches" => "false", "weight" => $conf["weight"]];
                               }else{
                                   $jobData[$k]["profileData"][$conf['job_key']] = ["value"=>$jobValue, "matches" => "true", "weight" => $conf["weight"]];

                               }


                           }
                       }else{//@Todo if not present and must much add ids
                           dd($conf["job_key"]);
                       }
                   }
               }
               $jobData = array_values(array_filter($jobData, function($mat) use ($bannedJobIds){

                   return !in_array($mat["id"], $bannedJobIds);
               }));
              //dd($bannedJobIds);
               echo print_r($jobData); exit;



               foreach($configArr  as $key=>$conf){
                   foreach($jobData as $data){
                       if (isset($data['profileData'][$conf['job_key']])){
                           if (isset($candidateData['profileData'][$conf['candidate_key']])){
                               $jobValue =  $candidateData['profileData'][$conf['candidate_key']];
                               $canValue =  $data['profileData'][$conf['job_key']];

                               if ( $jobValue !== $canValue && $conf["weight"] == 5 ){
                                  $bannedJobIds[] = $data["id"];
                                  continue;
                               }
                               if ($jobValue !== $canValue){
                                   $configArr[$key]["matches"] = false;
                               }else{
                                   $configArr[$key]["matches"] = true;
                               }
                               $configArr[$key]["job_id"] = $data["id"];
                               $configArr[$key]["jobs"][] = $data;
                               $matcher[] =
                                       [
                                           "job_id" => $data["id"],
                                           "job" => $data,
                                       ];

                           }
                       }
                   }
               }

               //dd($configArr);
               //echo "<pre>"; print_r($matcher);
              // dd(array_unique($bannedJobIds), $matcher );
               //dd($conf,$symbolKey, $value);

               $bannedJobIds = array_unique($bannedJobIds);
               $temp = array_unique(array_fetch($matcher, "job_id"));
               $matcher = array_values(array_filter($matcher, function($mat) use ($bannedJobIds){

                   return !in_array($mat["job_id"], $bannedJobIds);
               }));

               $matcher = array_filter($matcher, function ($v) use (&$temp) {
                   if (in_array($v['job_id'], $temp)) {
                       $key = array_search($v['job_id'], $temp);
                       unset($temp[$key]);
                       return true;
                   }

                       return false;

               });

               dd($matcher);

               dd($configArr);


           }

        }

    }
}






