<?php

namespace App\Modules\Api\Service\Payment;


use App\Modules\Api\Auth\SecurityContext;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var \AuthorizeNetAIM
     */
    protected $paymentGateWay;

    /** @var  \AuthorizeNetARB */
    protected $recurringBilling;

    /**
     * @var SecurityContext
     */
    protected $securityContext;

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
