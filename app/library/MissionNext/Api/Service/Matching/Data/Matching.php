<?php


namespace MissionNext\Api\Service\Matching\Data;

use MissionNext\Api\Service\Matching\Data\Type\AbstractDataType;
use MissionNext\Api\Service\Matching\Data\Type\Date;
use MissionNext\Api\Service\Matching\Data\Type\Numeric;
use MissionNext\Api\Service\Matching\Data\Type\String;
use MissionNext\Api\Service\Matching\Type\Matching as MatchType;


class Matching implements MatchingDataInterface
{
    private $mainValues,
            $matchingValues;
        /** @var $matchType MatchType */
    private $matchType;

    /**
     * @param MatchType $matchType
     *
     * @return $this
     */
    public function setMatchingType(MatchType $matchType)
    {
        $this->matchType = $matchType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMatches()
    {

      return $this->matchType->isMatches($this);
    }

    /**
     * @param array $mainValues
     *
     * @return $this
     */
    public function setMainValues(array $mainValues)
    {
        $this->mainValues = $mainValues;

        return $this;
    }

    /**
     * @param array $matchValues
     *
     * @return $this
     */
    public function setMatchingValues(array $matchValues)
    {
        $this->matchingValues = $matchValues;

        return $this;
    }

    /**
     * @return []
     */
    public function getMainValues()
    {

        return $this->mainValues;
    }

    /**
     * @return []
     */
    public function getMatchingValues()
    {

        return $this->matchingValues;
    }

    /**
     * @return Date|Numeric|String
     */
    public function getDataType()
    {
        $dataTypes = [new Date($this), new Numeric($this)];
        /** @var $dateType AbstractDataType */
        foreach($dataTypes as $dateType){
           if  ($dateType->isValid()){
               return $dateType;
           }
        }

        return new String($this);
    }




} 