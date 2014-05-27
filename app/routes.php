<?php

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

Route::get('logout', array(
    'as' => 'logout',
    function () {

        Sentry::logout();
        return Redirect::route('login');
    }
));


Route::group(array('prefix' => 'dashboard', 'before' => 'admin_auth'), function () {

//    Route::get('/', array(
//        'as' => 'adminHomepage',
//
//        function () {
//            return View::make('admin.adminHomepage');
//        }
//    ));


});
