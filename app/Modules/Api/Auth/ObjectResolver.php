<?php

namespace App\Modules\Api\Auth;


abstract class ObjectResolver
{
    /** @var  IObjectAware */
    protected  $resolvedObject;

    /**
     * @return IObjectAware
     */
    public function getResolvedObject()
    {

        return $this->resolvedObject;
    }
}
