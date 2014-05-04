<?php


namespace MissionNext\Api\Service\Matching\Data;


class Number extends AbstractDataType
{

    public function isValid()
    {
        foreach($this->matchingData->getMainValues() as $value){
            if (!filter_var($value, FILTER_VALIDATE_FLOAT)){
                return false;
            }
        }

        foreach($this->matchingData->getMatchingValues() as $value){
            if (!filter_var($value, FILTER_VALIDATE_FLOAT)){
                return false;
            }
        }

        return true;
    }
} 