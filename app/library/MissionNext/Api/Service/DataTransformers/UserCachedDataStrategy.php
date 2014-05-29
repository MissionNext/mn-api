<?php


namespace MissionNext\Api\Service\DataTransformers;


use MissionNext\Repos\TransformDataStrategy;
use Illuminate\Support\Collection;

class UserCachedDataStrategy extends TransformDataStrategy
{
    protected  $jsonDataKeys;

    public function __construct(array $jsonDataKeys = [])
    {
        $this->jsonDataKeys = $jsonDataKeys;
    }

    /**
     * @param Collection $dataCollection
     *
     * @return Collection
     */
    public function transform(Collection $dataCollection)
    {

      return $dataCollection->each(function($d){

           foreach($this->jsonDataKeys as $jsonKey){
               if (is_array($jsonKey)){
                   if (current($jsonKey)){
                       $key = key($jsonKey);
                       $data = json_decode($d->$key, true);

                       $props = array_keys($data);

                       foreach($props as $prop){

                           $d->$prop = $data[$prop];
                       }
                       unset($d->$key);
                       continue;
                   }else{
                       $jsonKey = key($jsonKey);
                   }

               }

               $d->$jsonKey = json_decode($d->$jsonKey, true);
           }
           if (empty($this->jsonDataKeys)){
               // default key data
               $d->data = json_decode($d->data, true);
               $props = array_keys($d->data);
               foreach($props as $prop){
                   $d->$prop = $d->data[$prop];
               }
               unset($d->data);

           }

       });
    }
} 