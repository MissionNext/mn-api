<?php


namespace MissionNext\Repos\Field;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use MissionNext\Repos\QueryDataFilter;


class FieldQueryBuilder {

    /**
     * @var \MissionNext\Repos\QueryDataFilter
     */
    protected $filter;

    /** @var  QueryBuilder */
    protected $query;


    public function __construct(QueryBuilder $query, QueryDataFilter $filter = null)
    {
          $this->query = $query;
          $this->filter = $filter;
    }

    /**
     * @param array $columns
     *
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get($columns = array('*'))
    {

       return $this->filter
                     ?  $this->filter->run($this->query->get($columns))
                     :  $this->query->get($columns);

    }
} 