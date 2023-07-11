<?php

namespace App\Modules\Api\Validators;

use Illuminate\Support\Facades\Validator;

class ValidatorResolver {


    public function __construct(){

        Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new DateValidator($translator, $data, $rules, $messages);
        });
    }

}
