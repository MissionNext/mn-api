<?php


namespace MissionNext\Api\Service\Matching\Type;


use MissionNext\Api\Service\Matching\Data\MatchingDataInterface;

class LessOrEqualMatching extends Matching
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
        $dataType = $matchingData->getDataType();

        foreach ($mainValues as $mainValue) {
            foreach ($matchingValues as $matchingValue) {
                if ( $dataType->transform($mainValue) <= $dataType->transform($matchingValue) ) {
                    return true;
                }
            }
        }
        return false;
    }
} 