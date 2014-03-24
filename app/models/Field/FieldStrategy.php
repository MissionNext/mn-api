<?php

namespace MissionNext\Models\Field;

use Illuminate\Database\Query\Builder;
use MissionNext\Facade\SecurityContext;

class FieldStrategy
{
    /**
     * @return Builder|[]
     * @throws \Exception
     */
    public static function fields()
    {
        $fieldModel = static::getModelName();

        return $fieldModel::fieldsExp();
    }

    /**
     * @return string
     */
    public static function getModelName()
    {

       return  "MissionNext\\Models\\Field\\".ucfirst(SecurityContext::role());
    }

    /**
     * @return string
     */
    public static function getModelMethod()
    {

       return SecurityContext::role()."Fields";
    }

} 