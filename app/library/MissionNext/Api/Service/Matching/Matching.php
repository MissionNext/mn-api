<?php

namespace MissionNext\Api\Service\Matching;


use Illuminate\Support\Collection;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Api\Auth\Token;
use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\Api\Service\Matching\Type\EqualMatching;
use MissionNext\Api\Service\Matching\Type\GreaterMatching;
use MissionNext\Api\Service\Matching\Type\GreaterOrEqualMatching;
use MissionNext\Api\Service\Matching\Type\LessMatching;
use MissionNext\Api\Service\Matching\Type\LessOrEqualMatching;
use MissionNext\Api\Service\Matching\Type\LikeMatching;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\Matching\Config;
use MissionNext\Repos\Field\Field;
use MissionNext\Api\Service\Matching\Data\Matching as MatchingData;

abstract class Matching
{

    const NO_PREFERENCE_SYMBOL = "(!)";

    protected $matchingModel;

    protected $mainMatchingModel;

    protected $reverseMatching = false;

    protected $dependentFields;

    /**
     * @param $matchData
     * @param $matchAgainstData
     * @param $matchConfig
     */
    public function __construct($matchData, $matchAgainstData, $matchConfig, $dependentFields)
    {
        $this->matchData = $matchData;
        $this->matchAgainstData = $matchAgainstData;
        $this->matchConfig = $matchConfig;
        $this->dependentFields = $dependentFields;
    }

    private $selectFieldTypes = [FieldType::SELECT, FieldType::SELECT_MULTIPLE, FieldType::CHECKBOX, FieldType::RADIO];

    protected $matchData, $matchAgainstData, $matchConfig;

    /**
     * @param $type
     *
     * @return array
     *
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     */
    public  function selectFieldsOfType($type)
    {
        if (!RouteSecurityFilter::isAllowedRole($type)) {

            throw new SecurityContextException("'$type' role doesn't exists", SecurityContextException::ON_SET_ROLE);
        }

        $fieldModelName = Field::currentFieldModelName( (new SecurityContext())->setToken((new Token())->setRoles([$type]) ) );

        return
            array_fetch((new $fieldModelName)->whereIn("type",
                $this->selectFieldTypes)->get()->toArray(), 'symbol_key');
    }

    /**
     * @param $values
     *
     * @return bool
     */
    protected  function isNoPreference($values)
    {
        foreach ($values as $value) {
            if (starts_with($value, static::NO_PREFERENCE_SYMBOL)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $mainValues
     * @param $matchingValues
     * @param $matchingType
     * @return bool
     */
    protected function isMatches($mainValues, $matchingValues, $matchingType)
    {
        $matchingData = (new MatchingData())->setMainValues($mainValues)
            ->setMatchingValues($matchingValues)
            ->setReverseMatching($this->reverseMatching);

        switch($matchingType){
            case Config::MATCHING_EQUAL:
                $matchingData->setMatchingType(new EqualMatching());
                break;
            case Config::MATCHING_LESS:
                $matchingData->setMatchingType(new LessMatching());
                break;
            case Config::MATCHING_LESS_OR_EQUAL:
                $matchingData->setMatchingType(new LessOrEqualMatching());
                break;
            case Config::MATCHING_GREATER:
                $matchingData->setMatchingType(new GreaterMatching());
                break;
            case Config::MATCHING_GREATER_OR_EQUAL:
                $matchingData->setMatchingType(new GreaterOrEqualMatching());
                break;
            case Config::MATCHING_LIKE:
                $matchingData->setMatchingType(new LikeMatching());
                break;
            default:
                $matchingData->setMatchingType(new EqualMatching());

        }

        return $matchingData->isMatches();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected  function calculateMatchingPercentage(array $data)
    {

        foreach ($data as &$profileData) {
            $profileData['matching_percentage'] = 0;
            $maxMatching = 0;
            $mustMatchMultiplier = $profileData['multiplier'];
            if (isset($profileData['results'])) {

                array_map   (function($c) use (&$maxMatching){
                    if ($c['weight'] < 5) {
                        $maxMatching += $c['weight'];
                    }

                }, $profileData['results']);

                foreach ($profileData['results'] as $key=>&$prof) {
                    if ($prof['weight'] < 5) {
                        if (isset($prof['matches']) && $prof['matches']) {
                            $profileData['matching_percentage'] += $prof['weight'];
                        } elseif (!isset($prof['matches'])) {

                            $prof = [$this->matchingModel."_value" => $prof, $this->mainMatchingModel."_value" => null];
                        }
                    }
                }
            }

            if (0 < $maxMatching) {
                $profileData['matching_percentage'] = round(($profileData['matching_percentage'] / $maxMatching) * 100) * $mustMatchMultiplier;
            } else {
                $profileData['matching_percentage'] = 0;
            }

        }

        return array_filter(array_values($data), function($d){

            return  $d['matching_percentage'] != 0;
        });
    }





    abstract public function matchResults();

    protected function dependencyArray($dependentFields)
    {
        $dependencies = [];
        foreach ($dependentFields as $item) {
            if (isset($item) && !empty($item['symbol_keys'])) {
                foreach ($item['symbol_keys'] as $fieldName) {
                    $dependencies[$item['depends_on']][] = $fieldName;
                }
            }
        }

        return $dependencies;
    }

    protected function getFieldDependencyMaster($dependencyArray, $fieldName){

        foreach($dependencyArray as $key => $value) {
            if (in_array($fieldName, $value)) {
                return $key;
            }
        }

        return false;
    }

    protected function removeFromDataSet($dependencies, $matchingDataKey, $k, &$ignoreFields, &$matchingDataSet)
    {
        foreach ($dependencies[$matchingDataKey] as $item) {
            if (isset($matchingDataSet[$k]['results'])) {
                unset($matchingDataSet[$k]['results'][$item]);
            }

            if (!in_array($item, $ignoreFields)) {
                $ignoreFields[] = $item;
            }
        }
    }

    protected function addMaritalField($confArray, $one_key, $second_key){
        $exist = false;
        foreach ($confArray as $item) {
            if (in_array('marital_status', $item)) {
                $exist = true;
            }
        }

        if (!$exist) {
            $confArray[] = [
                $one_key            => 'marital_status',
                $second_key         => 'marital_status',
                'weight'            => 0,
                'matching_type'     => 1,
                'field_type'        => 11
            ];
        }

        return $confArray;
    }
} 