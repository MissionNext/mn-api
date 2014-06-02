<?php

namespace MissionNext\Routing;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class AdminRouting
{
    public function __construct()
    {
        Route::get('/', array(
            'as' => 'homepage',
            function () {
                return Redirect::route('login');
            }
        ));

        Route::match(array('GET', 'POST'), 'login', array(
            'as' => 'login',
            'uses' => 'MissionNext\Controllers\Admin\AdminBaseController@login'
        ));

        Route::group(array('prefix' => 'dashboard', "before" => "admin_auth" ), function () {
            Route::get('logout', array(
                'as' => 'logout',
                function () {
                    Sentry::logout();
                    return Redirect::route('login');
                }
            ));
            Route::get('/', array(
                'as' => 'adminHomepage',
                function () {
                    return View::make('admin.adminHomepage');
                }
            ));
            // -----------   Applications ----------------------
            Route::get('/application', array(
                'as' => 'applications',
                'uses' => 'MissionNext\Controllers\Admin\ApplicationController@index'
            ));
            Route::match(array('GET', 'POST'), '/application/create', array(
                'as' => 'applicationCreate',
                'uses' => 'MissionNext\Controllers\Admin\ApplicationController@create'
            ));
            Route::match(array('GET', 'POST'), '/application/{id}/edit', array(
                'as' => 'applicationEdit',
                'uses' => 'MissionNext\Controllers\Admin\ApplicationController@edit'
            ));
            Route::match(array('GET', 'DELETE'), '/application/{id}/delete', array(
                'as' => 'applicationDelete',
                'uses' => 'MissionNext\Controllers\Admin\ApplicationController@delete'
            ));
            // -------------------------------------------------

            // ------------------- Users -----------------------
            Route::get('/user', array(
                'as' => 'users',
                'uses' => 'MissionNext\Controllers\Admin\UserController@index'
            ));
            Route::match(array('GET', 'POST'), '/user/create', array(
                'as' => 'userCreate',
                'uses' => 'MissionNext\Controllers\Admin\UserController@create'
            ));
            Route::match(array('GET', 'POST'), '/user/{id}/edit', array(
                'as' => 'userEdit',
                'uses' => 'MissionNext\Controllers\Admin\UserController@edit'
            ));
            Route::match(array('GET', 'DELETE'), '/user/{id}/delete', array(
                'as' => 'userDelete',
                'uses' => 'MissionNext\Controllers\Admin\UserController@delete'
            ));
            // -------------------------------------------------

            // --------------- Filters -------------------------
            Route::match(array('GET', 'POST'), '/user/filterBy', array(
                'as' => 'userFilters',
                'uses' => 'MissionNext\Controllers\Admin\AjaxController@filterBy'
            ));
            // -------------------------------------------------






        });  // end group dashboard

    }  // end construct
} 