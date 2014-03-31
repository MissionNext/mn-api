<?php

namespace MissionNext\Routing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Api\User\Controller as UserController;
use Api\Profile\Controller as ProfileController;
use Api\Field\Controller as FieldController;
use Api\Form\Controller as FormController;

class Routing
{

    const API_PREFIX = 'api/v1';
    const RESOURCE_USER = 'user';
    const RESOURCE_PROFILE = 'profile';
    const ROUTE_CREATE_USER = 'mission.next.user.create';


    public function __construct()
    {

        Route::get('/', function () {

            return View::make('hello');
        });

        Route::group(array('prefix' => static::API_PREFIX), function () {

            Route::resource(static::RESOURCE_USER, UserController::class, [
                'names' => ['store' => static::ROUTE_CREATE_USER]
            ]);

            Route::group(array('prefix' => static::RESOURCE_USER), function () {
                Route::post('find', UserController::class.'@find');
                Route::post('check', UserController::class.'@check');
            });

            Route::pattern('type', '[A-Za-z_-]+');
            Route::pattern('form', '[A-Za-z_-]+');
            Route::controller('{type}/field', FieldController::class);

            Route::controller('{type}/{form}/form', FormController::class);

            Route::resource(static::RESOURCE_PROFILE, ProfileController::class);

        });

    }

} 