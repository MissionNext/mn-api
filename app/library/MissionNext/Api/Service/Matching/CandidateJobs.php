<?php


namespace MissionNext\Api\Service\Matching;



use MissionNext\Models\DataModel\BaseDataModel;

class CandidateJobs extends Matching
{
    /**
     * @return mixed
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     *
     */
    public function matchResults()
    {
        $configArr = $this->matchConfig->toArray();

        $maxMatching = 0;
        $this->matchConfig->each(function ($c) use (&$maxMatching) {
            $maxMatching += $c->weight;
        });

        $jobData = $this->matchAgainstData;
        $candidateData = $this->matchData;

        $selectCanFields = $this->selectFieldsOfType(BaseDataModel::CANDIDATE);
        $selectJobFields = $this->selectFieldsOfType(BaseDataModel::JOB);

        $tempJobData = $jobData;

        foreach ($jobData as $k => $job) {
            foreach ($configArr as $conf) {
                $jobKey = $conf['job_key'];
                $candidateKey = $conf['candidate_key'];
                $jobProfile = $job['profileData'];
                $canProfile = $candidateData['profileData'];
                if (isset($jobProfile[$jobKey]) && isset($canProfile[$candidateKey])) {

                    $jobValue = $jobProfile[$jobKey];
                    $canValue = $canProfile[$candidateKey];

                    if (!is_array($jobValue)) {  /** convert  all values to array to compare */

                        $jobValue = [$jobValue];
                    }
                    if (!is_array($canValue)) {

                        $canValue = [$canValue];
                    }

                    $jobValue = array_map('strtolower', $jobValue);
                    $canValue = array_map('strtolower', $canValue);
                    $keyData = &$jobData[$k]["profileData"][$jobKey];
                    $keyData =
                        ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
//                  if ($job['id'] == 3) {
//                      var_dump("job_key = $jobKey", "can_key = $candidateKey", "job_value =", $jobValue, "can_value=", $canValue, "weight = {$conf['weight']}");
//                  }
                    /** if value starts with (!) any value  matches */
                    if (in_array($jobKey, $selectJobFields)) {
                        foreach ($jobValue as $jV) {
                            if (starts_with($jV, '(!)')) {
                                $jobData[$k]["profileData"][$jobKey] =
                                    ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                                continue 2;
                            }
                        }
                    }
                    /** if value starts with (!) any value  matches */
                    if (in_array($candidateKey, $selectCanFields)) {
                        foreach ($canValue as $cV) {
                            if (starts_with($cV, '(!)')) {
                                $jobData[$k]["profileData"][$jobKey] =
                                    ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                                continue 2;
                            }
                        }
                    }

                    /** if weight 5 (must match) and value doesn't matches remove add to banned ids */
                    $equalMatch = true;
                    if ($conf["weight"] == 5) {
                        array_walk($canValue, function($val) use ($jobValue, &$equalMatch){
                            if (!in_array($val, $jobValue)){
                                $equalMatch = false;
                            }
                        });
                        if (!$equalMatch){
                            unset($tempJobData[$k]);
                            continue;
                        }
                        $jobData[$k]["profileData"][$jobKey] =
                            ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                    }else{
                        array_walk($canValue, function($val) use ($jobValue, &$equalMatch){
                            if (!in_array($val, $jobValue)){
                                $equalMatch = false;
                            }
                        });
                        if (!$equalMatch) {
                            $jobData[$k]["profileData"][$jobKey] =
                                ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => false, "weight" => $conf["weight"]];
                        }else{
                            $jobData[$k]["profileData"][$jobKey] =
                                ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                        }

                    }

                }elseif( !isset($jobProfile[$jobKey]) ){
                    /** if in profile data no symbol key that is in match config and weight is 5, remove  element from match */
                    if ($conf['weight'] == 5) {
                        unset($tempJobData[$k]);
                        continue;
                    } else {
                        $jobData[$k]["profileData"][$jobKey] =
                            ["job_value" => null, "candidate_value" => null, "matches" => false, "weight" => $conf["weight"]];
                    }
                }
            }
        }


        $jobData = array_intersect_key($jobData, $tempJobData);

        foreach ($jobData as &$job) {
            $job['matching_percentage'] = 0;
            foreach ($job['profileData'] as $key=>&$prof) {
                //  var_dump($prof);
                if (isset($prof['matches']) && $prof['matches']) {
                    $job['matching_percentage'] += $prof['weight'];
                } elseif (!isset($prof['matches'])) {
                    //dd($key);
                    //@TODO job field not in matching config
                    $prof = ["job_value" => $prof, "candidate_value" => null];
                }
            }
            $job['matching_percentage'] = round(($job['matching_percentage'] / $maxMatching) * 100);
        }

        return $jobData;

    }

} 