<?php

namespace MissionNext\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FValidator;
use Illuminate\Support\MessageBag;

abstract class Validator
{

    protected $input;

    protected $errors;

    public static $rules;

    /** @var  Request */
    protected  $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->input = $this->getInput();

    }

    public function passes()
    {
        /** @var  $validation \Illuminate\Validation\Validator */
        $validation = FValidator::make($this->input, static::$rules);

        if ($validation->passes()) return true;

        $this->errors = $validation->messages();

        return false;
    }

    /**
     * @return MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    protected function getInput()
    {
        $request = $this->request;
        $fields = array_keys(static::$rules);
        $input = [];
        foreach($fields as $field){
            if (!$request->request->has($field)){
                continue;
            }
            $input[$field] = $request->request->get($field);
        }
        static::$rules =  array_intersect_key( static::$rules, $input );

        return $input;
    }
} 