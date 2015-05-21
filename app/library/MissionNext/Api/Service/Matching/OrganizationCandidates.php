<?php

namespace MissionNext\Api\Service\Matching;


use MissionNext\Models\DataModel\BaseDataModel;

class OrganizationCandidates extends Matching
{
    protected  $matchingModel = BaseDataModel::CANDIDATE;

    protected  $mainMatchingModel = BaseDataModel::ORGANIZATION;

    protected $reverseMatching = true;

    /**
     * @return mixed
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     *
     */
    public function matchResults()
    {
        $configArr = $this->matchConfig;
        $dependentFields = $this->dependentFields;
        $dependencies = $this->dependencyArray($dependentFields);

        $matchingDataSet = $this->matchAgainstData;
        $mainData = $this->matchData;

        $selectMainDataFields = $this->selectFieldsOfType($this->mainMatchingModel);
        $selectMatchingDataFields = $this->selectFieldsOfType($this->matchingModel);

        $tempMatchingData = $matchingDataSet;
        $matchingKey = $this->matchingModel."_value";
        $mainMatchingKey = $this->mainMatchingModel."_value";


        foreach ($matchingDataSet as $k => $matchingData) {
            $mustMatchMultiplier = 1;
            $ignoreFields = [];
            foreach ($configArr as $conf) {
                $matchingDataKey = $conf[$this->matchingModel.'_key'];
                $mainDataKey = $conf[$this->mainMatchingModel.'_key'];
                $matchingDataProfile = $matchingData['profileData'];
                $mainDataProfile = $mainData['profileData'];

                if (in_array($matchingDataKey, $ignoreFields) || in_array($mainDataKey, $ignoreFields)) {
                    continue;
                }

                if (isset($matchingDataProfile[$matchingDataKey]) && isset($mainDataProfile[$mainDataKey])) {

                    $matchingDataValue = $matchingDataProfile[$matchingDataKey];
                    $mainDataValue = $mainDataProfile[$mainDataKey];

                    if (11 == $conf['field_type'] && !(2 == $mainDataValue && 2 == $matchingDataValue)) {
                        if (isset($dependencies[$matchingDataKey])) {
                            foreach ($dependencies[$matchingDataKey] as $item) {
                                if (isset($matchingDataSet[$k]['results'])) {
                                    unset($matchingDataSet[$k]['results'][$item]);
                                }

                                if (!in_array($item, $ignoreFields)) {
                                    $ignoreFields[] = $item;
                                }
                            }
                        }
                    }

                    if ($mainDataValue === "" || $matchingDataValue === "") {
                        $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                        $matchingDataSet[$k]['results'][$matchingDataKey] =
                            [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => false, "weight" => $conf["weight"]];
                        continue;
                    }

                    /** convert  all values to array to compare */
                    $matchingDataValue = (array)$matchingDataValue;
                    $mainDataValue =  (array)$mainDataValue;

                    $matchingDataValue = array_map('strtolower', $matchingDataValue);
                    $mainDataValue = array_map('strtolower', $mainDataValue);

                    /** if value starts with (!) any value  matches */
                    if (
                        ( in_array($matchingDataKey, $selectMatchingDataFields) && $this->isNoPreference($matchingDataValue) )
                        ||
                        ( in_array($mainDataKey, $selectMainDataFields) &&  $this->isNoPreference($mainDataValue) )
                    )
                    {
                        $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                        $matchingDataSet[$k]['results'][$matchingDataKey] =
                            [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => true, "weight" => $conf["weight"]];
                        continue;
                    }

                    /** if weight 5 (must match) and value doesn't matches remove add to banned ids */
                    if ($conf["weight"] == 5) {
                        if  (!$this->isMatches($mainDataValue, $matchingDataValue, $conf['matching_type'])){
                            unset($tempMatchingData[$k]);
                            $mustMatchMultiplier = 0;
                            continue;
                        } else {
                            $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                            $matchingDataSet[$k]['results'][$matchingDataKey] =
                                [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => true, "weight" => $conf["weight"]];
                        }
                    }else{
                        if (!$this->isMatches($mainDataValue, $matchingDataValue, $conf['matching_type'])) {
                            $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                            $matchingDataSet[$k]['results'][$matchingDataKey] =
                                [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => false, "weight" => $conf["weight"]];
                        }else{
                            $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                            $matchingDataSet[$k]['results'][$matchingDataKey] =
                                [$matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => true, "weight" => $conf["weight"]];
                        }
                    }

                }elseif( !isset($matchingDataProfile[$matchingDataKey]) ){
                    /** if in profile data no symbol key that is in match config and weight is 5, remove  element from match */
                    if ($conf['weight'] == 5) {
                        unset($tempMatchingData[$k]);
                        $mustMatchMultiplier = 0;
                        continue;
                    } else {
                        $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                        $matchingDataSet[$k]['results'][$matchingDataKey] =
                            [$matchingKey => null, $mainMatchingKey => isset($mainDataProfile[$mainDataKey]) ? $mainDataProfile[$mainDataKey] : null, "matches" => false, "weight" => $conf["weight"]];
                    }

                    if (11 == $conf['field_type']) {
                        if (isset($dependencies[$matchingDataKey])) {
                            foreach ($dependencies[$matchingDataKey] as $item) {
                                if (isset($matchingDataSet[$k]['results'])) {
                                    unset($matchingDataSet[$k]['results'][$item]);
                                }

                                if (!in_array($item, $ignoreFields)) {
                                    $ignoreFields[] = $item;
                                }
                            }
                        }
                    }
                }
            }

            $matchingDataSet[$k]['multiplier'] = $mustMatchMultiplier;
        }

        $matchingDataSet = array_intersect_key($matchingDataSet, $tempMatchingData);

        return $this->calculateMatchingPercentage($matchingDataSet);
    }
} 