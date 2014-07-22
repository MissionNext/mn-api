<?php

namespace MissionNext\Repos\Field;


use Illuminate\Support\Collection;
use MissionNext\Repos\TransformDataStrategy;

class FieldToArrayTransformStrategy extends TransformDataStrategy {
    /** @var  array */
    private $fields = [];

    /**
     * @param array $fields
     */
    public function __construct(array $fields)
    {
          $this->fields = $fields;
    }

    /**
     * @param \IteratorAggregate $dataCollection
     *
     * @return Collection
     */
    public function transform(\IteratorAggregate $dataCollection)
    {
        return $dataCollection->each(function ($field) {
          //  dd($field->toArray());
           foreach($this->fields as $fieldName){
               $field->{$fieldName} =
                   $field->{$fieldName} ? explode("|", $field->{$fieldName}) : null;
           }

           return $field;
        });
    }

} 