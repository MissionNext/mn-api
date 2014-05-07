<?php

namespace MissionNext\Api\Service\Matching;

use MissionNext\Models\DataModel\BaseDataModel;

class CandidateOrganizations extends Matching 
{

    protected  $matchingModel = BaseDataModel::ORGANIZATION;

    protected  $mainMatchingModel = BaseDataModel::CANDIDATE;

    /**
     * @return mixed
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     *
     */
    public function matchResults()
    {
        $configArr = $this->matchConfig->toArray();

        $matchingDataSet = $this->matchAgainstData;
        $mainData = $this->matchData;

        $selectMainDataFields = $this->selectFieldsOfType($this->mainMatchingModel);
        $selectMatchingDataFields = $this->selectFieldsOfType($this->matchingModel);

        $tempMatchingData = $matchingDataSet;
        $matchingKey = $this->matchingModel."_value";
        $mainMatchingKey = $this->mainMatchingModel."_value";

        foreach ($matchingDataSet as $k => $matchingData) {
            foreach ($configArr as $conf) {

                $matchingDataKey = $conf[$this->matchingModel.'_key'];
                $mainDataKey = $conf[$this->mainMatchingModel.'_key'];
                $matchingDataProfile = $matchingData['profileData'];
                $mainDataProfile = $mainData['profileData'];
                if (isset($matchingDataProfile[$matchingDataKey]) && isset($mainDataProfile[$mainDataKey])) {

                    $matchingDataValue = $matchingDataProfile[$matchingDataKey];
                    $mainDataValue = $mainDataProfile[$mainDataKey];
                    /** convert  all values to array to compare */
                    $matchingDataValue = (array)$matchingDataValue;
                    $mainDataValue =  (array)$mainDataValue;

                    $matchingDataValue = array_map('strtolower', $matchingDataValue);
                    $mainDataValue = array_map('strtolower', $mainDataValue);
//                  if ($matchingData['id'] == 3) {
//                      var_dump("job_key = $matchingDataKey", "can_key = $candidateKey", "job_value =", $matchingDataValue, "can_value=", $canValue, "weight = {$conf['weight']}");
//                  }
                    /** if value starts with (!) any value  matches */
                    if (in_array($matchingDataKey, $selectMatchingDataFields) && $this->isNoPreference($matchingDataValue) ) {

                        $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                            [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => true, "weight" => $conf["weight"]];
                        continue;
                    }
                    /** if value starts with (!) any value  matches */
                    if (in_array($mainDataKey, $selectMainDataFields) &&  $this->isNoPreference($mainDataValue)) {

                        $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                            [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => true, "weight" => $conf["weight"]];
                        continue;
                    }

                    /** if weight 5 (must match) and value doesn't matches remove add to banned ids */
                    if ($conf["weight"] == 5) {
                        if  (!$this->isMatches($mainDataValue, $matchingDataValue, $conf['matching_type'])){
                            unset($tempMatchingData[$k]);
                            continue;
                        }

                        $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                            [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => true, "weight" => $conf["weight"]];
                    }else{
                        if (!$this->isMatches($mainDataValue, $matchingDataValue, $conf['matching_type'])) {
                            $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                                [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => false, "weight" => $conf["weight"]];
                        }else{
                            $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                                [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => true, "weight" => $conf["weight"]];
                        }
                    }

                }elseif( !isset($matchingDataProfile[$matchingDataKey]) ){
                    /** if in profile data no symbol key that is in match config and weight is 5, remove  element from match */
                    if ($conf['weight'] == 5) {
                        unset($tempMatchingData[$k]);
                        continue;
                    } else {
                        $matchingDataSet[$k]['profileData'][$matchingDataKey] =
                            [$matchingKey => null, $mainMatchingKey => null, "matches" => false, "weight" => $conf["weight"]];
                    }
                }
            }
        }

        $matchingDataSet = array_intersect_key($matchingDataSet, $tempMatchingData);

        return $this->calculateMatchingPercentage($matchingDataSet);
    }
} 