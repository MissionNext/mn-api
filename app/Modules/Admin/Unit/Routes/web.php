<?php

Route::group(['prefix' => 'units', 'middleware' => []], function () {
    Route::get('/', 'UnitsController@index')->name('units.index');
    Route::get('/create', 'UnitsController@create')->name('units.create');
    Route::post('/', 'UnitsController@store')->name('units.store');
    Route::get('/{unit}', 'UnitsController@show')->name('units.read');
    Route::get('/edit/{unit}', 'UnitsController@edit')->name('units.edit');
    Route::put('/{unit}', 'UnitsController@update')->name('units.update');
    Route::delete('/{unit}', 'UnitsController@destroy')->name('units.delete');
});
