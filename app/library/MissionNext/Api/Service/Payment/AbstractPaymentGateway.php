<?php

namespace MissionNext\Api\Service\Payment;


use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Api\Service\Payment;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected $paymentGateWay;



    private $securityContext;

    /**
     * @param SecurityContext $securityContext
     *
     * @return $this
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;

        return $this;
    }

    abstract public function getService();

} 