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

            return (new SecurityContextResolver( new AuthorizeNet( new \AuthorizeNetAIM('6W9w2XnmkkRj','938c4CBqpj84w9Cs'), $app )))->getResolvedObject();
        });
    }
} 