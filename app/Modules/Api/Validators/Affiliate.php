<?php


namespace App\Modules\Api\Validators;

use Illuminate\Support\Facades\Validator as FValidator;
use App\Models\DataModel\BaseDataModel;


class Affiliate extends Validator
{
    protected  $affiliateRoles = [BaseDataModel::AGENCY, BaseDataModel::ORGANIZATION];

    public static $rules =  [
        "password" => "required|between:3,100",
        "email" => "required|unique:users,email|email",
        "role" => "exists:roles,role",
        "username" => "required|unique:users,username"
    ];



}
