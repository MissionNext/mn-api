<?php

namespace App\Modules\Api\Service\Matching\Data\Type;


class String extends AbstractDataType
{
    /**
     * @return bool
     */
    public function isValid()
    {

        return true;
    }

    /**
     * @param $value
     *
     * @return float
     */
    public function transform($value)
    {

        return  $value;
    }
}
