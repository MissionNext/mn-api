<?php

namespace MissionNext\Repos\Subscription;


use MissionNext\Models\Subscription\Subscription;
use MissionNext\Repos\AbstractRepository;

class SubscriptionRepository extends AbstractRepository implements SubscriptionRepositoryInterface
{
    protected $modelClassName = Subscription::class;
    /**
     * @return Subscription
     */
    public function getModel()
    {

        return $this->model;
    }
} 