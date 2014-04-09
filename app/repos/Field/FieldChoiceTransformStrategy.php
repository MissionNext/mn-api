<?php

namespace MissionNext\Repos\Field;


use Illuminate\Support\Collection;
use MissionNext\Repos\TransformDataStrategy;

class FieldChoiceTransformStrategy extends TransformDataStrategy {

    private $transformFieldName = "choices";

    /**
     * @param $fieldName
     *
     * @return $this
     */
    public function setTransformFieldName($fieldName)
    {
        $this->transformFieldName =    $fieldName;

        return $this;
    }
    /**
     * @param Collection $dataCollection
     *
     * @return Collection
     */
    public function transform(Collection $dataCollection)
    {
        return $dataCollection->each(function ($field) {

            $field->{$this->transformFieldName} =
                 $field->{$this->transformFieldName} ? explode(",", $field->{$this->transformFieldName}) : null;

            return $field;
        });
    }

} 