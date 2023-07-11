<?php
use Illuminate\Support\Facades\Route;

Route::post('authorize/callback', 'AuthorizeController@index')->name('authorize.callback.post');
