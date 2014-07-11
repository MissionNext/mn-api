<?php

namespace MissionNext\Repos\Subscription;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use MissionNext\Models\Subscription\Partnership;
use MissionNext\Models\Subscription\Subscription;
use MissionNext\Models\Subscription\Transaction;
use MissionNext\Repos\AbstractRepository;

class SubscriptionRepository extends AbstractRepository implements SubscriptionRepositoryInterface
{
    protected $modelClassName = Subscription::class;

    protected  $forFill = [
        'comment' => null, 'app_id' => null, 'user_id' => null, 'partnership' => null,
        'price' => null,   'is_recurrent' => null,  'start_date' => null, 'end_date' => null,
        'authorize_id' => null, 'status' => null, 'paid' => null,
    ];
    /**
     * @return Subscription
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @param array $subscriptions
     *
     * @return Collection
     */
    public function saveMany(array $subscriptions)
    {
        $return = [];

        foreach($subscriptions as $subscription){
            $period = $subscription['period'];
            $partnership = $subscription['partnership'];

            $startDate = Carbon::now()->toDateTimeString();
            $endDate = Carbon::now()->addMonth()->toDateTimeString();
            if ($period === Partnership::PERIOD_YEAR && $partnership === Partnership::LIMITED){
                $endDate  = Carbon::now()->addMonths(3)->toDateTimeString();
            }elseif($period === Partnership::PERIOD_YEAR && $partnership !== Partnership::LIMITED){
                $endDate = Carbon::now()->addYear()->toDateTimeString();
            }
            $this->forFill['app_id'] = $subscription['app_id']; //@TODO check right user, status, partnership
            $this->forFill['authorize_id'] = $subscription['authorize_id'];
            $this->forFill['start_date'] = $startDate;
            $this->forFill['end_date'] = $endDate;
            $this->forFill['status'] = Subscription::STATUS_ACTIVE;
            $this->forFill['partnership'] = $partnership;
            $this->forFill['is_recurrent'] = (bool)$subscription['is_recurrent'];
            $this->forFill['user_id'] = $subscription['user_id'];
            $this->forFill['price'] = $subscription['price'];
            $this->forFill['paid'] = $subscription['price'];

            $this->updateClosed($this->forFill);

            $return[] = $this->getModel()->create($this->forFill);
        }

        return new Collection($return);
    }

    private function updateClosed(array $forFill)
    {

      return  $this->getModel()
            ->whereAppId($forFill['app_id'])
            ->whereUserId($forFill['user_id'])
            ->update(['status' => Subscription::STATUS_CLOSED]);
    }

    /**
     * @param $userId
     *
     * @return Collection
     */
    public function userSubscriptions($userId)
    {
        $subscriptions = $this->with('app')
                              ->with('user.appsStatuses')
                              ->with('user.roles')
                              ->whereUserId($userId)
                              ->where('status', '<>', Subscription::STATUS_CLOSED)
                              ->get();
        $subscriptions->each(function($sub){
            $sub->user->role = $sub->user->roles->first()->role;
            foreach($sub->user->apps_statuses as $appStatus){
                if ($appStatus->id == $sub->app->id){
                    $sub->app->is_active = $appStatus->pivot->is_active;
                    break;
                }
            }
        });

        return $subscriptions;
    }
} 