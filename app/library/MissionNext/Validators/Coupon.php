<?php


namespace MissionNext\Validators;


class Coupon extends Validator
{
    public static $rules =  [
        "code" => "required|unique:coupons",
    ];

} 