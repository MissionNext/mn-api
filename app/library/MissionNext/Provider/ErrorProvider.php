<?php

namespace MissionNext\Provider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Exception;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Api\Response\RestResponse;

class ErrorProvider extends ServiceProvider
{

    public function register()
    {
        App::error(function (Exception $exception, $code) {

            Log::error($exception);

            if (App::environment('local')) {
                dd($exception);
            }

            return (new RestResponse())->setErrorData($exception);
        });

        App::error(function (ValidationException $exception, $code) {

            return new RestResponse($exception);
        });
    }
}
