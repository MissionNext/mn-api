<?php

namespace MissionNext\Routing;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use MissionNext\Controllers\Admin\Subscription\CouponController;
use MissionNext\Controllers\Admin\Subscription\SubConfigController;

class AdminRouting
{
    /** @var  Router */
    private  $router;
    public function __construct(Application $App)
    {
        $this->router = $App->make('router');
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

        Route::group(array('prefix' => 'dashboard', "before" => ["admin_auth"] ), function () {
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
            // ================ SUBSCRIPTIONS ===========================
            Route::group(array('prefix' => 'subscription'), function(){
                $this->router->controller('config', SubConfigController::class,
                    [
                        'getIndex' =>  'sub.config.list',
                        'getCreate' => 'sub.config.create',
                        'postIndex' => 'sub.config.new',
                        'getEdit'   => 'sub.config.edit',
                        'postEdit'  => 'sub.config.update',
                        'deleteIndex' => 'sub.config.delete',

                    ]);

                $this->router->controller('coupon', CouponController::class,
                    [
                        'getIndex' =>  'sub.coupon.list',
                        'getCreate' => 'sub.coupon.create',
                        'postIndex' => 'sub.coupon.new',
                        'getEdit'   => 'sub.coupon.edit',
                        'postEdit'  => 'sub.coupon.update',
                        'deleteIndex' => 'sub.coupon.delete',

                    ]);
            });



            // ================ END SUBSCRIPTIONS ========================



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
            Route::match(array('POST'), '/user/searching', array(
                'as' => 'userSearching',
                'uses' => 'MissionNext\Controllers\Admin\UserController@searching'
            ));
            Route::match(array('GET'), '/user/{searchText}/search', array(
                'as' => 'search',
                'uses' => 'MissionNext\Controllers\Admin\UserController@search'
            ));

            // -------------------------------------------------
            // ------------------- Language --------------------
            Route::get('/language', array(
                'as' => 'languages',
                'uses' => 'MissionNext\Controllers\Admin\LanguageController@index'
            ));
            Route::match(array('GET', 'POST'), '/language/create', array(
                'as' => 'languageCreate',
                'uses' => 'MissionNext\Controllers\Admin\LanguageController@create'
            ));
            Route::match(array('GET', 'POST'), '/language/{id}/edit', array(
                'as' => 'languageEdit',
                'uses' => 'MissionNext\Controllers\Admin\LanguageController@edit'
            ));
            Route::match(array('GET', 'DELETE'), '/language/{id}/delete', array(
                'as' => 'languageDelete',
                'uses' => 'MissionNext\Controllers\Admin\LanguageController@delete'
            ));
            // -------------------------------------------------
            // --------------- Filters -------------------------
            Route::match(array('POST'), '/user/filterBy', array(
                'as' => 'userFilters',
                'uses' => 'MissionNext\Controllers\Admin\AjaxController@filterBy'
            ));
            Route::match(array('POST'), '/user/filterByApps', array(
                'as' => 'filteredUsersByEth',
                'uses' => 'MissionNext\Controllers\Admin\AjaxController@filterByShowMore'
            ));
            Route::match(array('POST'), '/getrolesapps', array(
                'as' => 'getRoles',
                'uses' => 'MissionNext\Controllers\Admin\AjaxController@getRolesApps'
            ));
            // -------------------------------------------------


        });  // end group dashboard

    }  // end construct
} 