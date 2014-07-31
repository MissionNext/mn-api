<?php


namespace MissionNext\Validators;


class Administrator extends Validator
{
    public static $rules =  [
        "password" => "required|between:3,100|confirmed",
        "new_password" => "required|between:3,100",
        "password_confirmation" =>  "required|between:3,100",
        "email" => "required|email|unique:adminusers,email",
        "username" => "required|unique:adminusers,username"
    ];
} 