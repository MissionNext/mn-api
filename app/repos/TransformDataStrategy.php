<?php

namespace MissionNext\Repos;

use Illuminate\Support\Collection;

abstract class TransformDataStrategy
{
    public abstract function transform(\IteratorAggregate $collection);

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

    /**
     * @param DataTransformer $transformer
     * @param $pages
     * @return Collection
     */
    public function paginate(DataTransformer $transformer, $pages)
    {

        return $this->transform($transformer->getQueryBuilder()->paginate($pages));
    }
}