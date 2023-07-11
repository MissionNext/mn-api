<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Modules\Api\Exceptions\AuthorizeException;
use App\Modules\Api\Exceptions\BadDataException;
use App\Modules\Api\Exceptions\ValidationException;
use App\Modules\Api\Response\RestResponse;


class ErrorProvider extends ServiceProvider
{

    public function register()
    {
//        App::error(function (Exception $exception, $code) {
//            Log::error($exception);
//            if (App::environment('local')) {
//                dd(get_class($exception), "message = ".$exception->getMessage());
//            }
//            return (new RestResponse())->setErrorData($exception);
//        });
//
//        App::error(function (ValidationException $exception, $code) {
//            return new RestResponse($exception);
//        });
//
//        App::error(function (BadDataException $exception, $code) {
//
//            return new RestResponse($exception);
//        });
//
//        App::error(function (AuthorizeException $exception, $code) {
//
//            return new RestResponse($exception);
//        });

    }
}
