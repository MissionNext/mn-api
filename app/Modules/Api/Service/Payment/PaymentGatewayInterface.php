<?php

namespace App\Modules\Api\Service\Payment;


interface PaymentGatewayInterface
{
    public function getService();

    public function getRecurringBilling();
}
