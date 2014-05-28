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
        Route::get('login', array(
            'as' => 'login',
            'uses' => 'MissionNext\Controllers\Admin\AdminController@login'
        ));
        Route::post('login', array(
            'as' => 'login',
            'uses' => 'MissionNext\Controllers\Admin\AdminController@login'
        ));


        Route::group(array('prefix' => 'dashboard', "before" => "admin_auth" ), function () {

            Route::get('logout', array(
                'as' => 'logout',
                function () {

                    Sentry::logout();
                    return Redirect::route('login');
                }
            ));

            Route::get('linkadm2', array(
                'as' => 'adm2',
                function() {
                    return View::make('admin.adm2');
                }
            ));

//    Route::get('/', array(
//        'as' => 'adminHomepage',
//
//        function () {
//            return View::make('admin.adminHomepage');
//        }
//    ));


        });

    }
} 