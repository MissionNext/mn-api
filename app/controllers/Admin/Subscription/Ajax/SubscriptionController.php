<?php


namespace MissionNext\Controllers\Admin\Subscription\Ajax;


use Illuminate\Support\Facades\Response;
use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Models\Subscription\Subscription;
use MissionNext\Repos\Subscription\SubscriptionRepository;
use MissionNext\Repos\Subscription\SubscriptionRepositoryInterface;

class SubscriptionController extends AdminBaseController
{
    /**
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndex($userId)
    {
        /** @var  $repo SubscriptionRepository */
        $repo = $this->repoContainer[SubscriptionRepositoryInterface::KEY];

        return Response::json($repo->userSubscriptions($userId));
    }

    public function updateIndex($subId)
    {
        $update = [];
        foreach($this->request->request->all() as $property)
        {
            $update[$property['field']] = $property['value'];
        }
        $update = Subscription::findOrFail($subId)->update($update);
        /** @var  $repo SubscriptionRepository */
        $repo = $this->repoContainer[SubscriptionRepositoryInterface::KEY];

        return Response::json(['update' => $update, 'subscription' => $repo->find($subId) ]);
    }
} 