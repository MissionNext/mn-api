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


Route::group(array('prefix' => '/dashboard', 'before' => 'admin_auth'), function () {

    Route::get('/', array(
        'as' => 'adminHomepage',
        function () {
            return View::make('user.profile');
        }
    ));

    Route::get('logout', array(
        'as' => 'logout',
        'uses' => 'MissionNext\Controllers\Admin\AdminController@logout'
    ));
});
