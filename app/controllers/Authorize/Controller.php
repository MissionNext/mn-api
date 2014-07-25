<?php


namespace MissionNext\Controllers\Authorize;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Input;
use MissionNext\Models\Authorize\Renewval;
use MissionNext\Models\Subscription\Subscription;
use MissionNext\Models\Subscription\Transaction;

class Controller extends \Illuminate\Routing\Controller
{
    public function postIndex()
    {
        $renewal =  new Renewval(Input::all());

        $subscriptionsQuery = Subscription::where('authorize_id', '=', $renewal->subscription_id)
                                     ->where('status', '<>', Subscription::STATUS_CLOSED);

        $oldSubs = $subscriptionsQuery->get();

        if ($oldSubs->count()) {
            Subscription::whereIn('id', $oldSubs->lists('id'))->update(['status' => Subscription::STATUS_CLOSED]);
        }


        if ($renewal->isApproved()){
            $subs = [];

            $oldSubs->each(function($sub) use (&$subs){

                $notChangedData = array_only($sub->toArray(), ['partnership', 'price','user_id', 'app_id', 'comment', 'authorize_id', 'paid', 'is_recurrent']);
                $changedData = [
                    'start_date' => Carbon::now()->toDateTimeString(),
                    'end_date' => Carbon::now()->addMonth()->toDateTimeString(),
                    'status' => Subscription::STATUS_ACTIVE,
                ];
                $subData = array_merge($notChangedData, $changedData);
                $subs[] = Subscription::create($subData);

            });

            $subs = new Collection($subs);
            $transaction = new Transaction();
            $transactionData = [
               'amount' => $renewal->amount,
               'transaction_id' => $renewal->trans_id,
               'comment' => $renewal->description,
            ];
            $transaction = $transaction->create($transactionData);
            $transaction->subscriptions()->sync($subs->lists('id'));
        }

    }
} 