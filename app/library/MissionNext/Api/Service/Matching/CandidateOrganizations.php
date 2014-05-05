<?php

namespace MissionNext\Api\Service\Matching;

use MissionNext\Models\DataModel\BaseDataModel;

class CandidateOrganizations extends Matching 
{

    protected  $matchingModel = BaseDataModel::ORGANIZATION;

    /**
     * @return mixed
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     *
     */
    public function matchResults()
    {
        $configArr = $this->matchConfig->toArray();

        $matchingDataSet = $this->matchAgainstData;
        $candidateData = $this->matchData;

        $selectCanFields = $this->selectFieldsOfType(BaseDataModel::CANDIDATE);
        $selectMatchingFields = $this->selectFieldsOfType(BaseDataModel::ORGANIZATION);

        $tempMainData = $matchingDataSet;
        $matchingKey = BaseDataModel::ORGANIZATION."_value";
        foreach ($matchingDataSet as $k => $matchingData) {
            foreach ($configArr as $conf) {
                $matchingDataKey = $conf[$this->matchingModel.'_key'];
                $candidateKey = $conf['candidate_key'];
                $matchingDataProfile = $matchingData['profileData'];
                $canProfile = $candidateData['profileData'];
                if (isset($matchingDataProfile[$matchingDataKey]) && isset($canProfile[$candidateKey])) {

                    $matchingDataValue = $matchingDataProfile[$matchingDataKey];
                    $canValue = $canProfile[$candidateKey];
                    /** convert  all values to array to compare */
                    $matchingDataValue = (array)$matchingDataValue;
                    $canValue =  (array)$canValue;
                    $matchingDataValue = array_map('strtolower', $matchingDataValue);
                    $canValue = array_map('strtolower', $canValue);

//                  if ($matchingData['id'] == 3) {
//                      var_dump("job_key = $matchingDataKey", "can_key = $candidateKey", "job_value =", $matchingDataValue, "can_value=", $canValue, "weight = {$conf['weight']}");
//                  }
                    /** if value starts with (!) any value  matches */
                    if (in_array($matchingDataKey, $selectMatchingFields) && $this->isNoPreference($matchingDataValue) ) {

                        $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                            [$matchingKey => $matchingDataValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                        continue;
                    }
                    /** if value starts with (!) any value  matches */
                    if (in_array($candidateKey, $selectCanFields) &&  $this->isNoPreference($canValue)) {

                        $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                            [$matchingKey => $matchingDataValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                        continue;
                    }

                    /** if weight 5 (must match) and value doesn't matches remove add to banned ids */
                    if ($conf["weight"] == 5) {
                        if  (!$this->isMatches($canValue, $matchingDataValue, $conf['matching_type'])){
                            unset($tempMainData[$k]);
                            continue;
                        }

                        $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                            [$matchingKey => $matchingDataValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                    }else{
                        if (!$this->isMatches($canValue, $matchingDataValue, $conf['matching_type'])) {
                            $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                                [$matchingKey => $matchingDataValue, "candidate_value" => $canValue, "matches" => false, "weight" => $conf["weight"]];
                        }else{
                            $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                                [$matchingKey => $matchingDataValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                        }
                    }

                }elseif( !isset($matchingDataProfile[$matchingDataKey]) ){
                    /** if in profile data no symbol key that is in match config and weight is 5, remove  element from match */
                    if ($conf['weight'] == 5) {
                        unset($tempMainData[$k]);
                        continue;
                    } else {
                        $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                            [$matchingKey => null, "candidate_value" => null, "matches" => false, "weight" => $conf["weight"]];
                    }
                }
            }
        }

        $matchingDataSet = array_intersect_key($matchingDataSet, $tempMainData);

        return $this->calculateMatchingPercentage($matchingDataSet);
    }
} 