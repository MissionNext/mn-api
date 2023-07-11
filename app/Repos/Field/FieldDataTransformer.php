<?php

namespace App\Repos\Field;

use App\Repos\DataTransformer;

class FieldDataTransformer extends DataTransformer
{
    /**
     * @param $method
     * @param $args
     *
     * @return $this
     */
    public function __call($method, $args)
    {
        $this->builder = call_user_func_array(array($this->getQueryBuilder(), $method), $args);

        return $this;
    }

}
