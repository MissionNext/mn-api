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

        $jobData = $this->matchAgainstData;
        $candidateData = $this->matchData;

        $selectCanFields = $this->selectFieldsOfType(BaseDataModel::CANDIDATE);
        $selectJobFields = $this->selectFieldsOfType(BaseDataModel::JOB);

        $tempJobData = $jobData;

        foreach ($jobData as $k => &$job) {
            foreach ($configArr as $conf) {
                $jobKey = $conf['job_key'];
                $candidateKey = $conf['candidate_key'];
                $jobProfile = &$job['profileData'];
                $canProfile = $candidateData['profileData'];
                if (isset($jobProfile[$jobKey]) && isset($canProfile[$candidateKey])) {

                    $jobValue = $jobProfile[$jobKey];
                    $canValue = $canProfile[$candidateKey];
                    /** convert  all values to array to compare */
                    $jobValue = (array)$jobValue;
                    $canValue =  (array)$canValue;

                    $jobValue = array_map('strtolower', $jobValue);
                    $canValue = array_map('strtolower', $canValue);

//                  if ($job['id'] == 3) {
//                      var_dump("job_key = $jobKey", "can_key = $candidateKey", "job_value =", $jobValue, "can_value=", $canValue, "weight = {$conf['weight']}");
//                  }
                    /** if value starts with (!) any value  matches */
                    if (in_array($jobKey, $selectJobFields) && $this->isNoPreference($jobValue) ) {

                            $jobProfile[$jobKey] =
                                ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                            continue;
                    }
                    /** if value starts with (!) any value  matches */
                    if (in_array($candidateKey, $selectCanFields) &&  $this->isNoPreference($canValue)) {

                            $jobProfile[$jobKey] =
                                ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                            continue;
                    }

                    /** if weight 5 (must match) and value doesn't matches remove add to banned ids */
                    if ($conf["weight"] == 5) {
                       if  (!$this->isMatches($canValue, $jobValue, $conf['matching_type'])){
                           unset($tempJobData[$k]);
                           continue;
                       }

                       $jobProfile[$jobKey] =
                            ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                    }else{
                        if (!$this->isMatches($canValue, $jobValue, $conf['matching_type'])) {
                            $jobProfile[$jobKey] =
                                ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => false, "weight" => $conf["weight"]];
                        }else{
                            $jobProfile[$jobKey] =
                                ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                        }
                    }

                }elseif( !isset($jobProfile[$jobKey]) ){
                    /** if in profile data no symbol key that is in match config and weight is 5, remove  element from match */
                    if ($conf['weight'] == 5) {
                        unset($tempJobData[$k]);
                        continue;
                    } else {
                        $jobProfile[$jobKey] =
                            ["job_value" => null, "candidate_value" => null, "matches" => false, "weight" => $conf["weight"]];
                    }
                }
            }
        }

        $jobData = array_intersect_key($jobData, $tempJobData);

        return $this->calculateMatchingPercentage($jobData);
    }




} 