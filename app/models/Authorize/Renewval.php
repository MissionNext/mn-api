<?php


namespace MissionNext\Models\Authorize;



class Renewval
{
    /** @var \ArrayObject  */
    private $container;

    public function __construct(array $input)
    {
        $this->container = new \ArrayObject($input, 2);
        $this->subscription_id = $this->container->x_subscription_id;
        $this->api = $this->container->api;
        $this->amount = $this->container->x_amount;
        $this->description = $this->container->x_description;
        $this->response_code = $this->container->x_response_code;
        $this->subscription_paynum = $this->container->x_subscription_paynum;
        $this->trans_id = $this->container->x_trans_id;
    }

    const APPROVED = 1,
          DECLINED = 2,
          ERROR    = 3;

    public $subscription_id,
           $api,
           $trans_id,
           $amount,
           $description,
           $response_code,
           $subscription_paynum;


    /**
     * @return bool
     */
    public function isApproved()
    {

        return $this->container->offsetGet('x_response_code') == static::APPROVED;
    }


} 