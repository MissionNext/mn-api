<?php

namespace MissionNext\Api\Service\Matching\Data;


abstract class AbstractDataType
{
   protected $matchingData;

   public function __construct(MatchingDataInterface $matchingData)
   {
       $this->matchingData = $matchingData;
   }

   abstract  public function isValid();
} 