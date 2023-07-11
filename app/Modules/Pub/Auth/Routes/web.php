<?php

use App\Modules\Admin\Dashboard\Controllers\DashboardController;
use App\Modules\Pub\Auth\Controllers\LoginController;

Route::get('profile/file/{fileName}', 'FileController@getIndex')->name('file.index');
Route::get('uploads/{fileName}', 'FileController@getFile')->name('file.get');

Route::get('/cc', function () {
    Artisan::call('cache:clear');
    echo '<script>alert("cache clear Success")</script>';
});

Route::group(['prefix' => 'auths', 'middleware' => []], function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', 'LoginController@login')->name('login.post');
    Route::get('logout', 'LoginController@logout')->name('login.logout');
});
