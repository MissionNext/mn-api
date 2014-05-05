<?php

namespace MissionNext\Api\Service\Matching\Type;


use MissionNext\Api\Service\Matching\Data\MatchingDataInterface;

class LikeMatching extends Matching
{
    /**
     * @param MatchingDataInterface $matchingData
     *
     * @return bool
     */
    public function isMatches(MatchingDataInterface $matchingData)
    {
        $mainValues = $matchingData->getMainValues();
        $matchingValues = $matchingData->getMatchingValues();

        foreach ($mainValues as $mainValue) {
            foreach ($matchingValues as $matchingValue) {
                if ( str_is("*".$mainValue."*", $matchingValue)  ) {
                    return true;
                }
            }
        }
        return false;
    }
} 