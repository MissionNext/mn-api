<?php

namespace MissionNext\Routing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use MissionNext\Controllers\Api\Matching\ConfigController;
use MissionNext\Controllers\Api\Profile\SearchController;
use MissionNext\Controllers\Api\User\UserController;
use MissionNext\Controllers\Api\Profile\UserController as UserProfileController;
use MissionNext\Controllers\Api\Field\Controller as FieldController;
use MissionNext\Controllers\Api\Form\Controller as FormController;
use MissionNext\Controllers\Api\JobController;
use MissionNext\Controllers\Api\Profile\JobController as JobProfileController;
use MissionNext\Controllers\Api\Matching\JobController as MatchJobController;

class Routing
{

    const API_PREFIX = 'api/v1';
    const RESOURCE_USER = 'user';
    const RESOURCE_PROFILE = 'profile';
    const RESOURCE_JOB_PROFILE = 'profile/job';
    const RESOURCE_JOB = 'job';
    const ROUTE_CREATE_USER = 'mission.next.user.create';
    const ROUTE_CREATE_JOB = 'mission.next.job.create';


    public function __construct()
    {

        Route::get('/', function () {

            return View::make('hello');
        });

        Route::group(array('prefix' => static::API_PREFIX), function () {

            Route::pattern('type', '[A-Za-z_-]+');
            Route::pattern('form', '[A-Za-z_-]+');
            Route::pattern('candidate_id', '\d+');

            Route::controller('{type}/field', FieldController::class, [
                'getModel' => 'model.fields.get'
            ]);
            Route::controller('{type}/matching/config', ConfigController::class, [

            ]);

            Route::resource(static::RESOURCE_JOB_PROFILE, JobProfileController::class,
              [  'only' => ['show','update', 'destroy'] ]
            );

            Route::controller('{type}/{form}/form', FormController::class);

            Route::controller('match/job/{candidate_id}', MatchJobController::class);

            Route::resource(static::RESOURCE_USER, UserController::class, [
                'names' => ['store' => static::ROUTE_CREATE_USER]
            ]);

            Route::resource(static::RESOURCE_JOB, JobController::class, [
                'names' => ['store' => static::ROUTE_CREATE_JOB], 'except' => ['create','edit']
            ]);

            Route::group(array('prefix' => static::RESOURCE_JOB), function () {
                Route::post('find', JobController::class.'@find');
            });

            Route::group(array('prefix' => static::RESOURCE_USER), function () {
                Route::post('find', UserController::class.'@find');
                Route::post('check', UserController::class.'@check');
            });



            Route::resource(static::RESOURCE_PROFILE, UserProfileController::class,
                [  'except' => ['index','create', 'store', 'edit'] ]
            );

            Route::controller('{type}/search', SearchController::class, [

            ]);

        });

    }

} 