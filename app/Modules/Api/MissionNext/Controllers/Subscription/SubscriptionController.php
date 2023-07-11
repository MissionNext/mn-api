<?php

namespace App\Modules\Api\MissionNext\Controllers\Subscription;

use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Repos\Subscription\SubscriptionRepository;
use App\Repos\Subscription\SubscriptionRepositoryInterface;
use App\Repos\Subscription\TransactionRepository;
use App\Repos\Subscription\TransactionRepositoryInterface;

/**
 * Class SubscriptionController
 * @package App\Modules\Api\Controllers\Subscription
 */
class SubscriptionController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        $service = $this->paymentGateway;

        $readyData = $service->processRequest($this->request->request->all());
        //$readyData = $this->request->request->all();
       /** authorize call */
        $subscriptions = $readyData['subscriptions'];
        $transactionData = $readyData['transaction'] ?: [];
        /** @var  $repo SubscriptionRepository */
        $repo = $this->repoContainer[SubscriptionRepositoryInterface::KEY];

        /** @var  $transactionRepo TransactionRepository */
        $transactionRepo = $this->repoContainer[TransactionRepositoryInterface::KEY];

        return new RestResponse($transactionRepo
                           ->syncWithSubscriptions($repo->saveMany($subscriptions), $transactionData)
                          );
    }

    /**
     * @return RestResponse
     */
    public function postAdd()
    {
        $service = $this->paymentGateway;
        $readyData = $service->processRequest($this->request->request->all());
        $subscriptions = $readyData['subscriptions'];
        $transactionData = $readyData['transaction'] ?: [];
        /** @var  $repo SubscriptionRepository */
        $repo = $this->repoContainer[SubscriptionRepositoryInterface::KEY];

        /** @var  $transactionRepo TransactionRepository */
        $transactionRepo = $this->repoContainer[TransactionRepositoryInterface::KEY];

        return new RestResponse($transactionRepo
            ->syncWithSubscriptions($repo->addSubscription($subscriptions), $transactionData)
        );
    }


    public function getFor($userId)
    {
        /** @var  $repo SubscriptionRepository */
        $repo = $this->repoContainer[SubscriptionRepositoryInterface::KEY];

        return new RestResponse($repo->userSubscriptions($userId));
    }

}
