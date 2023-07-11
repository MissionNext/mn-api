<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Modules\Api\Auth\SecurityContextResolver;
use App\Modules\Api\Service\Payment\AuthorizeNet;
use App\Modules\Api\Service\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Config;

class PaymentProvider extends ServiceProvider
{
    public function register()
    {
        $api_id = Config::get('app.api_login_id');
        $key = Config::get('app.transaction_key');

        $this->app->bind(PaymentGatewayInterface::class, function(Application $app) use ($api_id, $key)
        {
            return (new SecurityContextResolver( new AuthorizeNet( new \AuthorizeNetAIM($api_id, $key), new \AuthorizeNetARB($api_id, $key), $app )))->getResolvedObject();
        });
    }
}
