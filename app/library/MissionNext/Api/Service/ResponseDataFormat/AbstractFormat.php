<?php


namespace MissionNext\Api\Service\ResponseDataFormat;


use Illuminate\Support\Collection;

abstract class AbstractFormat
{
     abstract  public static function format(Collection $collection);
} 