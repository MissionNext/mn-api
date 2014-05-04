<?php


namespace MissionNext\Api\Service\Matching\Type;


use MissionNext\Api\Service\Matching\Data\MatchingDataInterface;

class EqualMatching extends Matching
{
    /**
     * @param MatchingDataInterface $matchingData
     *
     * @return bool
     */
    public  function isMatches(MatchingDataInterface $matchingData)
    {
        $mainValues = $matchingData->getMainValues();
        $matchingValues = $matchingData->getMatchingValues();

        foreach($mainValues  as $mainValue){
            if (in_array($mainValue, $matchingValues)){
                return true;
            }
        }

        return false;
    }
} 