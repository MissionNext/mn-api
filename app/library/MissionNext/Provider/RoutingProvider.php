<?php


namespace MissionNext\Provider;

use Illuminate\Support\ServiceProvider;
use MissionNext\Routing\AdminRouting;
use MissionNext\Routing\Routing;

class RoutingProvider extends ServiceProvider
{

    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {
        new  Routing();
        new AdminRouting();
    }

} 