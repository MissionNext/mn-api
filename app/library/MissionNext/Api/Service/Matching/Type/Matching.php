<?php


namespace MissionNext\Api\Service\Matching\Type;


use MissionNext\Api\Service\Matching\Data\MatchingDataInterface;

abstract class Matching
{

   abstract public function isMatches(MatchingDataInterface $matchingDataInterface);
} 