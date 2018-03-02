<?php

namespace MissionNext\Provider;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MissionNext\Api\Auth\SecurityContextResolver;
use MissionNext\Api\Service\Payment\AuthorizeNet;
use MissionNext\Api\Service\Payment\PaymentGatewayInterface;

class PaymentProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PaymentGatewayInterface::class, function(Application $app)
        {
            return (new SecurityContextResolver( new AuthorizeNet( new \AuthorizeNetAIM(config('app.api_login_id'), config('app.transaction_key')), new \AuthorizeNetARB(config('app.api_login_id'), config('app.transaction_key')), $app )))->getResolvedObject();
        });
    }
} 