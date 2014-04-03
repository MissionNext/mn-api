<?php

namespace MissionNext\Repos;

use Illuminate\Support\Collection;

abstract class TransformDataStrategy
{
    public abstract function transform(Collection $collection);

    /**
     * @param DataTransformer $transformer
     * @param array $columns
     *
     * @return Collection
     */
    public function get(DataTransformer $transformer, $columns = array('*'))
    {

        return $this->transform($transformer->getQueryBuilder()->get($columns));
    }
}