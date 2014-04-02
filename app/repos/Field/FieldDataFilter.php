<?php
namespace MissionNext\Repos\Field;

use MissionNext\Repos\QueryDataFilter;

class FieldDataFilter extends QueryDataFilter
{
    /**
     * @param callable $func
     *
     * @return $this
     */
    public function add(\Closure $func)
    {
        $this->filters[] = $func;

        return $this;
    }
} 