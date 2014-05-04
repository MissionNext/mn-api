<?php

namespace MissionNext\Api\Service\Matching;


use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Api\Auth\Token;
use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\Api\Service\Matching\Type\EqualMatching;
use MissionNext\Api\Service\Matching\Type\LessMatching;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\Matching\Config;
use MissionNext\Repos\Field\Field;
use MissionNext\Api\Service\Matching\Data\Matching as MatchingData;

abstract class Matching
{

    const NO_PREFERENCE_SYMBOL = "(!)";
    /**
     * @param $matchData
     * @param $matchAgainstData
     * @param $matchConfig
     */
    public function __construct($matchData, $matchAgainstData, $matchConfig)
    {
        $this->matchData = $matchData;
        $this->matchAgainstData = $matchAgainstData;
        $this->matchConfig = $matchConfig;
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
                                            ->setMatchingValues($matchingValues);

        switch($matchingType){
            case Config::MATCHING_EQUAL:
                $matchingData->setMatchingType(new EqualMatching());
                return $matchingData->isMatches();
                break;
            case Config::MATCHING_LESS;
                $matchingData->setMatchingType(new LessMatching());
                return $matchingData->isMatches();
                return $this->lessMatching($mainValues, $matchingValues);
            default:
                return $this->equalMatching($mainValues, $matchingValues);
        }
    }

    /**
     * @param $mainValues
     * @param $matchingValues
     * @return bool
     */
    private function equalMatching($mainValues, $matchingValues)
    {
        foreach($mainValues  as $mainValue){
            if (in_array($mainValue, $matchingValues)){
                return true;
            }
        }

        return false;
    }

    /**
     * @param $mainValues
     * @param $matchingValues
     *
     * @return bool
     */
    private function lessMatching($mainValues, $matchingValues)
    {

       if ($this->isDateData($mainValues, $matchingValues)) {
           foreach ($mainValues as $mainValue) {
               foreach ($matchingValues as $matchingValue) {
                   if (new \DateTime($mainValue) < new \DateTime($matchingValue)) {
                       return true;
                   }
               }
           }
           return false;
       }elseif($this->isNumericData($mainValues, $matchingValues)){
           foreach ($mainValues as $mainValue) {
               foreach ($matchingValues as $matchingValue) {
                   if ($mainValue < $matchingValue) {
                       return true;
                   }
               }
           }
           return false;

       }else{
           return $this->equalMatching($mainValues, $matchingValues);
       }

    }

    /**
     * @param $mainValues
     * @param $matchingValues
     * @return bool
     */
    private function isNumericData($mainValues, $matchingValues)
    {
        foreach($mainValues as $value){
            if (!filter_var($value, FILTER_VALIDATE_FLOAT)){
                return false;
            }
        }

        foreach($matchingValues as $value){
            if (!filter_var($value, FILTER_VALIDATE_FLOAT)){
                return false;
            }
        }

        return true;
    }

    /**
     * @param $mainValues
     * @param $matchingValues
     * @return bool
     */
    private function isDateData($mainValues, $matchingValues){

        foreach($mainValues as $value){
            if (!filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ["regexp"=>"/\\d{4}-\\d{2}-\\d{2}/"]])){
                return false;
            }
        }

        foreach($matchingValues as $value){
            if (!filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ["regexp"=>"/\\d{4}-\\d{2}-\\d{2}/"]])){
                return false;
            }
        }

        return true;
    }

    abstract public function matchResults();

} 