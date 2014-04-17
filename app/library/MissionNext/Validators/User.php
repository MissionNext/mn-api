<?php
namespace MissionNext\Validators;

class User extends Validator
{
    public static $rules =  [
        "password" => "required|between:3,100",
        "email" => "required|unique:users,email|email",
        "role" => "exists:roles,role",
        "username" => "required|unique:users,username"
    ];

} 