<?php

namespace MissionNext\Provider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Exception;
use MissionNext\Api\Response\RestResponse;

class ErrorProvider extends ServiceProvider
{
    
    public function register()
    {
        App::error(function(Exception $exception, $code)
        {
            Log::error($exception);

            return (new RestResponse())->setErrorData($exception);
        });
    }
}
