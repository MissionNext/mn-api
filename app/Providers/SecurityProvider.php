<?php
namespace App\Providers;

use App\Modules\Api\Filter\RoleChecker;
use App\Modules\Api\Filter\RouteSecurityFilter;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use App\Modules\Api\Auth\Listener;
use App\Modules\Api\Auth\Manager;
use App\Modules\Api\Auth\SecurityContext;
use App\Modules\Api\Auth\Token;


class SecurityProvider extends ServiceProvider
{
    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventProvider::class);

        $this->app->singleton('rest.token', function () {

            return new Token();
        });

        $this->app->singleton('rest.manager', function () {

            return new Manager();
        });

        $this->app->singleton('rest.listener', function ($app) {

            return new Listener($app->make('rest.manager'));
        });

        $this->app->bind('security_context', function($app){

            return (new SecurityContext())->setToken($app->make('rest.token'));

        });

        $this->app->bind('rest.listener.sync', function ($app) {
            $instance = $app->make('rest.listener');
            $instance->setRequest($app->make('request'));

            return $instance;
        });


        $this->filters();
    }

    /**
     * @return $this
     */
    protected function filters()
    {
//        (new \Illuminate\Routing\Route)->middleware(RouteSecurityFilter::ROLE, RouteSecurityFilter::class.'@'.RouteSecurityFilter::ROLE_M);
//
//        (new \Illuminate\Routing\Route)->middleware(RouteSecurityFilter::AUTHORIZE,  RouteSecurityFilter::class.'@'.RouteSecurityFilter::AUTHORIZE_M);
//        (new \Illuminate\Routing\Route)->middleware(RouteSecurityFilter::ROLE_ADMIN_AREA,  RouteSecurityFilter::class.'@'.RouteSecurityFilter::ROLE_ADMIN_AREA);
//        (new \Illuminate\Routing\Route)->middleware(RoleChecker::CHECK,  RoleChecker::class.'@'.RoleChecker::CHECK);

        return $this;
    }

}
