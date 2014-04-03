<?php

namespace MissionNext\Repos\Field;


use Illuminate\Support\Collection;
use MissionNext\Repos\TransformDataStrategy;

class FieldChoiceTransformStrategy extends TransformDataStrategy {

    /**
     * @param Collection $dataCollection
     *
     * @return Collection
     */
    public function transform(Collection $dataCollection)
    {
        return $dataCollection->each(function ($field) {

            $field->choices = $field->choices ? explode(",", $field->choices) : null;

            return $field;
        });
    }

} 