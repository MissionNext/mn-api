<?php

Route::group(['prefix' => 'leads', 'middleware' => []], function () {
    Route::get('/', 'LeadController@index')->name('leads.index');
    Route::get('/create', 'LeadController@create')->name('leads.create');
    Route::post('/', 'LeadController@store')->name('leads.store');
    Route::get('/{lead}', 'LeadController@show')->name('leads.read');
    Route::get('/edit/{lead}', 'LeadController@edit')->name('leads.edit');
    Route::put('/{lead}', 'LeadController@update')->name('leads.update');
    Route::delete('/{lead}', 'LeadController@destroy')->name('leads.delete');
});