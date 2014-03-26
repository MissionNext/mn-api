<?php
namespace MissionNext\Provider;

use Illuminate\Support\ServiceProvider;
use MissionNext\Api\Auth\Listener;
use MissionNext\Api\Auth\Manager;
use MissionNext\Api\Auth\Token;
use Illuminate\Support\Facades\Route;
use MissionNext\Filter\RouteSecurityFilter;

class SecurityProvider extends ServiceProvider
{
    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('rest.token', function () {

            return new Token();
        });

        $this->app->singleton('rest.manager', function () {

            return new Manager();
        });

        $this->app->singleton('rest.listener', function ($app) {

            return new Listener($app->make('rest.manager'));
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
        Route::filter(RouteSecurityFilter::ROLE, 'MissionNext\Filter\RouteSecurityFilter@'.RouteSecurityFilter::ROLE_M);
        Route::filter(RouteSecurityFilter::AUTHORIZE, 'MissionNext\Filter\RouteSecurityFilter@'.RouteSecurityFilter::AUTHORIZE_M);

        return $this;
    }

} 