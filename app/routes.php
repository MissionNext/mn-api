<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
    return View::make('hello');
});

Route::group(array('prefix' => 'api/v1'), function() {

    Route::get('job/{id}', 'Api\BaseController@jobs');

    Route::resource('user', 'Api\User\Controller');
    Route::group(array('prefix' => 'user'), function() {
        Route::post('find', 'Api\User\Controller@find');
        Route::post('check', 'Api\User\Controller@check');
    });
    Route::pattern('type', '[A-Za-z_-]+');
    Route::pattern('form', '[A-Za-z_-]+');
    Route::controller('{type}/field', 'Api\Field\Controller');

    Route::controller('{type}/{form}/form', 'Api\Form\Controller');

    Route::resource('profile', 'Api\Profile\Controller');

});