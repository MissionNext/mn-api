<?php


namespace MissionNext\Api\Service\DataTransformers;


use MissionNext\Repos\TransformDataStrategy;
use Illuminate\Support\Collection;

class UserCachedDataStrategy extends TransformDataStrategy
{

    /**
     * @param Collection $dataCollection
     *
     * @return Collection
     */
    public function transform(Collection $dataCollection)
    {
      return $dataCollection->each(function($d){
           $d->data = json_decode($d->data, true);
           $props = array_keys($d->data);
           foreach($props as $prop){
               $d->$prop = $d->data[$prop];
           }
           unset($d->data);
       });
    }
} 