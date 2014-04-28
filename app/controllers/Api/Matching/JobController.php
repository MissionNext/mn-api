<?php


namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Support\Facades\DB;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\Matching\Config;
use MissionNext\Repos\Field\Field;

class JobController extends BaseController
{

    public function getIndex($candidate_id)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByCandidate(BaseDataModel::JOB, $candidate_id)->get();
      //  dd($this->getLogQueries());
       // dd($config->toArray());

        if (!$config->count()){

            return new RestResponse([]);
        }

        $candidateData = DB::select("SELECT data FROM candidate_cached_profile WHERE id = ? ", [$candidate_id] );
        if (!empty($candidateData)) {
            $candidateData = json_decode($candidateData[0]->data, true);
            $jobData = DB::select("SELECT data FROM job_cached_profile");
            $jobData = !empty($jobData) ? array_map(function ($d) {
                    return json_decode($d->data, true);
                }, $jobData) : [];


            $bannedJobIds = [];
            $configArr = $config->toArray();
            $maxMatching = 0;
            $config->each(function($c) use (&$maxMatching){
                $maxMatching += $c->weight;
            });

            SecurityContext::getInstance()->getToken()->setRoles([BaseDataModel::CANDIDATE]);
            $candidateField = Field::currentFieldModelName(SecurityContext::getInstance());
            $selectFieldTypes =  [FieldType::SELECT, FieldType::SELECT_MULTIPLE, FieldType::CHECKBOX, FieldType::RADIO ];
            $selectCanFields = array_fetch((new $candidateField)->whereIn("type",
               $selectFieldTypes )->get()->toArray(), 'symbol_key');
            SecurityContext::getInstance()->getToken()->setRoles([BaseDataModel::JOB]);
            $jobField = Field::currentFieldModelName(SecurityContext::getInstance());
            $selectJobFields = array_fetch((new $jobField)->whereIn("type",
                $selectFieldTypes )->get()->toArray(),'symbol_key');

         //   dd($selectCanFields, $selectJobFields);




            //dd($configArr, $candidateData);

            foreach ($jobData as $k => $job) {
                foreach ($configArr as $conf) {
                    $jobKey = $conf['job_key'];
                    $candidateKey = $conf['candidate_key'];
                    $jobProfile = $job['profileData'];
                    $canProfile = $candidateData['profileData'];
                    if (isset($jobProfile[$jobKey])) {
                        if (isset($canProfile[$candidateKey])) {
                            $jobValue = $jobProfile[$jobKey];
                            $canValue = $canProfile[$candidateKey];

                            if (!is_array($jobValue)){

                                $jobValue = [$jobValue];
                            }
                            if (!is_array($canValue)){

                                $canValue = [$canValue];
                            }
//                            if ($job['id'] == 3) {
//                                var_dump("job_key = $jobKey", "can_key = $candidateKey", "job_value =", $jobValue, "can_value=", $canValue, "weight = {$conf['weight']}");
//                            }

                            if (in_array($jobKey, $selectJobFields)){
                                foreach($jobValue as $jV){
                                    if (starts_with($jV,'(!)')){
                                        $jobData[$k]["profileData"][$jobKey] =
                                            ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                                        continue 2;
                                    }
                                }
                            }

                            if (in_array($candidateKey, $selectCanFields)){
                                foreach($canValue as $cV){
                                    if (starts_with($cV,'(!)')){
                                        $jobData[$k]["profileData"][$jobKey] =
                                            ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                                        continue 2;
                                    }
                                }
                            }



//                            array_walk($jobValue, function($val){
//                                dd($val);
//                            });
//                            var_dump($jobValue, $canValue);
                            if ($canValue !== $jobValue   &&  $conf["weight"] == 5) {
                               // var_dump($jobValue, $canValue);
                                $bannedJobIds[] = $job["id"];
                                continue;
                            }

                            if ($canValue !== $jobValue  ) {
                                $jobData[$k]["profileData"][$jobKey] =
                                    ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => false, "weight" => $conf["weight"]];
                            }

                            if ($canValue === $jobValue  ) {
                                $jobData[$k]["profileData"][$jobKey] =
                                    ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                            }


                        }
                    }
                }
            }

            $jobData = array_values(array_filter($jobData, function ($job) use ($bannedJobIds) {

                return !in_array($job["id"], $bannedJobIds);
            }));

            $tempJobData = $jobData;
            foreach ($configArr as $config) {
                foreach ($jobData as $idx => $job) {
                    if (!isset($job['profileData'][$config['job_key']])) {
                        if ($config['weight'] == 5) {
                            unset($tempJobData[$idx]);
                        } else {
                            $jobData[$idx]['profileData'][$config['job_key']] =
                                ["job_value" => null, "candidate_value" => null, "matches" => false, "weight" => $config["weight"]];
                        }
                    }
                }
            }
            $jobData = array_intersect_key($jobData, $tempJobData);

            foreach ($jobData as &$job) {
                $job['matching_percentage'] = 0;
                foreach ($job['profileData'] as &$prof) {
                    //  var_dump($prof);
                    if (isset($prof['matches']) && $prof['matches']) {
                        $job['matching_percentage'] += $prof['weight'];
                    } elseif (!isset($prof['matches'])) { //@TODO job field not in matching config
                        $prof = ["job_value" => $prof, "candidate_value" => null];
                    }
                }
                $job['matching_percentage'] = round(($job['matching_percentage'] / $maxMatching) * 100);
            }

            return new RestResponse(array_values($jobData));
        }


        return new RestResponse([]);
    }

}






