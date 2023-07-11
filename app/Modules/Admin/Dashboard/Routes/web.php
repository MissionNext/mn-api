<?php
Route::group(['prefix' => 'dashboard', 'middleware' => []], function () {
    Route::get('/', 'DashboardController@index')->name('dashboards.index');

    Route::get('/application', 'ApplicationController@index')->name('dashboards.application.index');
    Route::get('/application/create', 'ApplicationController@create')->name('dashboards.application.create');
    Route::post('/application/create', 'ApplicationController@store')->name('dashboards.application.store.post');
    Route::get('/application/edit/{application}', 'ApplicationController@edit')->name('dashboards.application.edit');
    Route::post('/application/edit/{application}', 'ApplicationController@update')->name('dashboards.application.update.post');
    Route::delete('/application/delete/{application}', 'ApplicationController@destroy')->name('dashboards.application.delete.delete');

    Route::get('/languages', 'LanguageController@index')->name('dashboards.languages.index');
    Route::get('/languages/create', 'LanguageController@create')->name('dashboards.languages.create');
    Route::post('/languages/create', 'LanguageController@store')->name('dashboards.languages.store.post');
    Route::get('/languages/edit/{language}', 'LanguageController@edit')->name('dashboards.languages.edit');
    Route::post('/languages/edit/{language}', 'LanguageController@update')->name('dashboards.languages.update.post');
    Route::delete('/languages/delete/{language}', 'LanguageController@destroy')->name('dashboards.languages.delete.delete');

    Route::get('/users', 'UserController@index')->name('dashboards.users.index');
    Route::post('/users', 'UserController@search')->name('dashboards.users.search');
    Route::get('/users/{user}', 'UserController@view')->name('dashboards.users.view');
    Route::get('/users/create', 'UserController@create')->name('dashboards.users.create');
    Route::post('/users/create', 'UserController@store')->name('dashboards.users.store.post');
    Route::get('/users/edit/{user}', 'UserController@edit')->name('dashboards.users.edit');
    Route::post('/users/edit/{user}', 'UserController@update')->name('dashboards.users.update.post');
    Route::delete('/users/delete/{user}', 'UserController@destroy')->name('dashboards.users.delete.delete');

    Route::get('/administrators', 'AdministratorController@index')->name('dashboards.administrators.index');
    Route::post('/administrators', 'AdministratorController@search')->name('dashboards.administrators.search');
    Route::get('/administrators/create', 'AdministratorController@create')->name('dashboards.administrators.create');
    Route::post('/administrators/create', 'AdministratorController@store')->name('dashboards.administrators.store.post');
    Route::get('/administrators/edit/{administrator}', 'AdministratorController@edit')->name('dashboards.administrators.edit');
    Route::post('/administrators/edit/{administrator}', 'AdministratorController@update')->name('dashboards.administrators.update.post');
    Route::delete('/administrators/delete/{administrator}', 'AdministratorController@destroy')->name('dashboards.administrators.delete.delete');

    Route::get('/subscriptions/{subscription}', 'SubscriptionController@index')->name('dashboards.subscriptions.index');
    Route::post('/subscriptions/update/{application}', 'SubscriptionController@update')->name('dashboards.subscriptions.update.post');

    Route::get('/coupons', 'CouponsController@index')->name('dashboards.coupons.index');
    Route::get('/coupons/create/{generate?}', 'CouponsController@create')->name('dashboards.coupons.create');
    Route::post('/coupons/create', 'CouponsController@store')->name('dashboards.coupons.store.post');
    Route::get('/coupons/edit/{coupon}/{generate?}', 'CouponsController@edit')->name('dashboards.coupons.edit');
    Route::post('/coupons/edit/{coupon}', 'CouponsController@update')->name('dashboards.coupons.update.post');
    Route::delete('/coupons/delete/{coupon}', 'CouponsController@destroy')->name('dashboards.coupons.delete.delete');
});
