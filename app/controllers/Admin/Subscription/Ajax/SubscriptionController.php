<?php


namespace MissionNext\Controllers\Admin\Subscription\Ajax;


use Illuminate\Support\Facades\Response;
use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Models\Subscription\Subscription;
use MissionNext\Models\User\User;
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

    /**
     * @param $userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactions($userId)
    {

        return Response::json((new User)->transactions($userId));
    }

    /**
     * @param $subId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIndex($subId)
    {
        $update = [];
        $forceClose = false;
        foreach($this->request->request->all() as $property)
        {
            $update[$property['field']] = $property['value'];
            $forceClose = isset($property['forceClose']) ? $property['forceClose'] : false;
        }

        $subscription = Subscription::findOrFail($subId);
        $subscription->force_close = $forceClose;
        $authorizeCode = null;
        if ($forceClose){
            if ($subscription->is_recurrent && $subscription->authorize_id){
                $response = $this->paymentGateway->getRecurringBilling()->cancelSubscription($subscription->authorize_id);
                $authorizeCode = strip_tags($response->xpath('messages/message')[0]->code->asXML());
                dd($response->getMessageCode());
            }

        }
//   E00003     object(AuthorizeNetARB_Response)#696 (3) {
//        ["xml"]=>
//  object(SimpleXMLElement)#693 (1) {
//  ["messages"]=>
//    object(SimpleXMLElement)#691 (2) {
//    ["resultCode"]=>
//      string(2) "Ok"
//    ["message"]=>
//      object(SimpleXMLElement)#690 (2) {
//      ["code"]=>
//        string(6) "I00001"
//    ["text"]=>
//        string(11) "Successful."
//      }
//}  ["code"]=>
 //       string(6) "I00002"
  //  ["text"]=>
   //     string(43) "The subscription has already been canceled."
//}
//["response"]=>
//  ["xpath_xml"]=>
//  object(SimpleXMLElement)#697 (1) {
//  ["messages"]=>
//    object(SimpleXMLElement)#691 (2) {
//    ["resultCode"]=>
//      string(2) "Ok"
//["message"]=>
//      object(SimpleXMLElement)#690 (2) {
//      ["code"]=>
//        string(6) "I00001"
//["text"]=>
//        string(11) "Successful."
//      }
//    }
//  }
//}
        $subscription->update($update);
        /** @var  $repo SubscriptionRepository */
        $repo = $this->repoContainer[SubscriptionRepositoryInterface::KEY];

        return Response::json(['update' => $update, 'subscription' => $repo->find($subId), 'authorizeCancelCode' => $authorizeCancelResultCode ]);
    }
} 