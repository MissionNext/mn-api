<?php


namespace MissionNext\Api\Service\Matching\Data;

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
     * @return Date|Number|String
     */
    private function getDataType()
    {
        $dateType = new Date($this);
        $numericType = new Number($this);

        if ($dateType->isValid()){
           return $dateType;
        }elseif($numericType->isValid()){
            return $numericType;
        }else{
            return new String($this);
        }

    }




} 