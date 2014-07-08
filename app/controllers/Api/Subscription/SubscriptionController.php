<?php


namespace MissionNext\Controllers\Api\Subscription;


use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Api\Service\Payment\PaymentGatewayInterface;
use MissionNext\Models\Subscription\Partnership;
use MissionNext\Repos\Subscription\SubscriptionRepository;
use MissionNext\Repos\Subscription\SubscriptionRepositoryInterface;

class SubscriptionController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        //dd($this->paymentGateway->getService());
       /** authorize call */
        $readyData = $this->request->request->all();
        $subscriptions = $readyData['subscriptions'];
        /** @var  $repo SubscriptionRepository */
        $repo = $this->repoContainer[SubscriptionRepositoryInterface::KEY];

       return new RestResponse($repo->saveMany($subscriptions));
    }

} 