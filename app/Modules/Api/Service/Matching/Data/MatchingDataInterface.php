<?php

namespace App\Modules\Api\Service\Matching\Data;


use App\Modules\Api\Service\Matching\Data\Type\Date;
use App\Modules\Api\Service\Matching\Data\Type\Numeric;
use App\Modules\Api\Service\Matching\Type\Matching as MatchingType;

interface MatchingDataInterface
{
    public function setMainValues(array $mainValues);

    public function setMatchingValues(array $matchingValues);

    public function setMatchingType(MatchingType $type);

    public function getMainValues();

    public function getMatchingValues();

    public function getReverseMatching();

    public function setReverseMatching($reverseMatching);

    public function isMatches();
    /**
     * @return Date|Numeric|String
     */
    public function getDataType();
}
