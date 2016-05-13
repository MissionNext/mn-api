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
        $marital_status_key = 'marital_status';
        $configArr = $this->matchConfig;

        $matchingDataSet = $this->matchAgainstData;
        $mainData = $this->matchData;

        $selectMainDataFields = $this->selectFieldsOfType($this->mainMatchingModel);
        $selectMatchingDataFields = $this->selectFieldsOfType($this->matchingModel);

        $tempMatchingData = $matchingDataSet;
        $matchingKey = $this->matchingModel."_value";
        $mainMatchingKey = $this->mainMatchingModel."_value";

        foreach ($matchingDataSet as $k => $matchingData) {
//            $mustMatchMultiplier = 1;
            foreach ($configArr as $conf) {

                $matchingDataKey = $conf[$this->matchingModel.'_key'];
                $mainDataKey = $conf[$this->mainMatchingModel.'_key'];
                $matchingDataProfile = $matchingData['profileData'];
                $mainDataProfile = $mainData['profileData'];

                $marital_value = (isset($mainDataProfile[$marital_status_key])) ? $mainDataProfile[$marital_status_key]: null;
                $spouse_field = strpos($mainDataKey, 'spouse');
                if ("Married" != $marital_value && $spouse_field !== false) {
                    continue;
                }

                if (isset($matchingDataProfile[$matchingDataKey]) && isset($mainDataProfile[$mainDataKey])) {

                    $matchingDataValue = $matchingDataProfile[$matchingDataKey];
                    $mainDataValue = $mainDataProfile[$mainDataKey];

                    if ($mainDataValue === "" || $matchingDataValue === "") {
                        $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                        $matchingDataSet[$k]['results'][] =
                            ['matchingDataKey' => $matchingDataKey, $matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => false, "weight" => $conf["weight"]];
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
                        list ($mainIntersectValue, $matchIntersectValue) = $this->getIntersection($mainDataValue, $matchingDataValue);
                        $matchingDataSet[$k]['results'][] =
                            ['matchingDataKey' => $matchingDataKey, $matchingKey => $matchIntersectValue, $mainMatchingKey => $mainIntersectValue, "matches" => true, "weight" => $conf["weight"]];
                        continue;
                    }

                    /** if weight 5 (must match) and value doesn't matches remove add to banned ids */
                    if ($conf["weight"] == 5) {
                        if  (!$this->isMatches($mainDataValue, $matchingDataValue, $conf['matching_type'])){
                            unset($tempMatchingData[$k]);
//                            $mustMatchMultiplier = 0;
                            continue;
                        } else {
                            $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                            list ($mainIntersectValue, $matchIntersectValue) = $this->getIntersection($mainDataValue, $matchingDataValue);
                            $matchingDataSet[$k]['results'][] =
                                ['matchingDataKey' => $matchingDataKey, $matchingKey => $matchIntersectValue, $mainMatchingKey => $mainIntersectValue, "matches" => true, "weight" => $conf["weight"]];
                        }
                    }else{
                        if (!$this->isMatches($mainDataValue, $matchingDataValue, $conf['matching_type'])) {
                            $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                            $matchingDataSet[$k]['results'][] =
                                ['matchingDataKey' => $matchingDataKey, $matchingKey => $matchingDataValue, $mainMatchingKey => $mainDataValue, "matches" => false, "weight" => $conf["weight"]];
                        }else{
                            $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                            list ($mainIntersectValue, $matchIntersectValue) = $this->getIntersection($mainDataValue, $matchingDataValue);
                            $matchingDataSet[$k]['results'][] =
                                ['matchingDataKey' => $matchingDataKey, $matchingKey => $matchIntersectValue, $mainMatchingKey => $mainIntersectValue, "matches" => true, "weight" => $conf["weight"]];
                        }
                    }

                }elseif( !isset($matchingDataProfile[$matchingDataKey]) || empty($matchingDataProfile[$matchingDataKey])){
                    $matchingDataSet[$k]['profileData'] = $matchingDataProfile;
                    $matchingDataSet[$k]['results'][] =
                        ['matchingDataKey' => $matchingDataKey, $matchingKey => null, $mainMatchingKey => isset($mainDataProfile[$mainDataKey]) ? $mainDataProfile[$mainDataKey] : null, "matches" => false, "weight" => $conf["weight"]];
               }
            }
//            $matchingDataSet[$k]['multiplier'] = $mustMatchMultiplier;
        }

        $matchingDataSet = array_intersect_key($matchingDataSet, $tempMatchingData);

        return $this->calculateMatchingPercentage($matchingDataSet);
    }
} 