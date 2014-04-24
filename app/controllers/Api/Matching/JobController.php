<?php


namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Support\Facades\DB;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Config;

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

        $candidateData = DB::select("SELECT data FROM user_cached_profile WHERE user_id = ? AND type = ? ", [$candidate_id, BaseDataModel::CANDIDATE]);
        if (!empty($candidateData)) {
            $candidateData = json_decode($candidateData[0]->data, true);
            $jobData = DB::select("SELECT data FROM user_cached_profile WHERE type = ? ", [BaseDataModel::JOB]);
            $jobData = !empty($jobData) ? array_map(function ($d) {
                    return json_decode($d->data, true);
                }, $jobData) : [];


            $bannedJobIds = [];
            $configArr = $config->toArray();
            $maxMatching = 0;
            $config->each(function($c) use (&$maxMatching){
                $maxMatching += $c->weight;
            });

            //dd($configArr, $candidateData);

            foreach ($jobData as $k => $job) {
                foreach ($configArr as $conf) {
                    $jobKey = $conf['job_key'];
                    $candidateKey = $conf['candidate_key'];
                    $jobProfile = $job['profileData'];
                    $canProfile = $candidateData['profileData'];
                    if (isset($jobProfile[$jobKey])) {
                        if (isset($canProfile[$candidateKey])) {
                            $jobValue = $canProfile[$candidateKey];
                            $canValue = $jobProfile[$jobKey];
                           // dd($jobValue, $canValue);
                            if (!is_array($jobValue)){

                                $jobValue = [$jobValue];
                            }
                            if (!is_array($canValue)){

                                $canValue = [$canValue];
                            }
//                            array_walk($jobValue, function($val){
//                                dd($val);
//                            });
//                            var_dump($jobValue, $canValue);
                            if ($jobValue !== $canValue && $conf["weight"] == 5) {
                                $bannedJobIds[] = $job["id"];
                                continue;
                            }

                            if ($jobValue !== $canValue) {
                                $jobData[$k]["profileData"][$jobKey] =
                                    ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => false, "weight" => $conf["weight"]];
                            }

                            if ($jobValue === $canValue) {
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






