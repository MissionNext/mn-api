<?php


namespace App\Modules\Api\Validators;


class Coupon extends Validator
{
    public static $rules =  [
        "code" => "required|unique:coupons,code",
        "value" => "required|numeric",
    ];

}
