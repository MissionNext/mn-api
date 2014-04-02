<?php
namespace MissionNext\Repos;


use Illuminate\Support\Collection;

abstract class QueryDataFilter
{

    protected $filters = [];

    /**
     * @param callable $func
     *
     * @return mixed
     */
    public abstract function add(\Closure $func);

    /**
     * @param Collection $collection
     *
     * @return Collection
     */
    public function run(Collection $collection)
    {
        foreach ($this->filters as $filter) {
            call_user_func($filter, $collection);
        }

        return $collection;
    }

} 