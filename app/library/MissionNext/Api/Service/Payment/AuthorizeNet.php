<?php

namespace MissionNext\Api\Service\Payment;


use MissionNext\Api\Auth\ISecurityContextAware;

class AuthorizeNet extends AbstractPaymentGateway implements ISecurityContextAware
{
    public  function __construct(\AuthorizeNetRequest $authorizeNet)
    {
        $this->paymentGateWay = $authorizeNet;
    }

    /**
     * @return \AuthorizeNetAIM
     */
    public function getService()
    {

        return $this->paymentGateWay;
    }

} 