<?php

namespace MissionNext\Api\Service\Matching\Data;


use MissionNext\Api\Service\Matching\Type\Matching;

interface MatchingDataInterface
{
    public function setMainValues(array $mainValues);

    public function setMatchingValues(array $matchingValues);

    public function setMatchingType(Matching $type);

    public function getMainValues();

    public function getMatchingValues();

    public function isMatches();
} 