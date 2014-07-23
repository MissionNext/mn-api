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

    const RECURRENT_DAY = 15;

    const LIMITED_YEAR = 3;

    const RENEW_TYPE_MONTHLY = 'm';
    const RENEW_TYPE_KEEP_END_DATE = 'k';
    const RENEW_TYPE_FROM_TODAY = 't';
    const RENEW_TYPE_FROM_END_DATE = 'e';

    protected $forFill = [
        'comment' => null, 'app_id' => null, 'user_id' => null, 'partnership' => null,
        'price' => null, 'is_recurrent' => null, 'start_date' => null, 'end_date' => null,
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
     * @param array $data
     *
     * @return Collection
     */
    public function saveMany(array $data)
    {
        $return = [];
        $type = static::RENEW_TYPE_FROM_TODAY;
        $startDate = Carbon::now()->toDateTimeString();
        $endDate = Carbon::now()->addYear()->toDateTimeString();
        //type e -  start date = end date current subscription, end date add year
        if (count($data)) {
            $type = $data[0]['renew_type'];
            $period = $data[0]['period'];

            switch ($type) {
                case static::RENEW_TYPE_FROM_END_DATE:
                    foreach ($this->subEndDate($data) as $sub) {
                        if ($sub) {
                            $endDate = $sub->end_date->addYear()->toDateTimeString();
                            $startDate = $sub->start_date->toDateTimeString();
                            $this->subEndDate($data)->send('stop');
                        }
                    }

                    break;
                case static::RENEW_TYPE_FROM_TODAY:
                    $startDate = Carbon::now()->toDateTimeString();
                    $endDate = Carbon::now()->addYear()->toDateTimeString();

                    break;
                case static::RENEW_TYPE_KEEP_END_DATE:
                    foreach ($this->subEndDate($data) as $sub) {
                        if ($sub) {
                            $endDate = $sub->end_date->toDateTimeString();
                            $startDate = $sub->start_date->toDateTimeString();
                            $this->subEndDate($data)->send('stop');
                        }
                    }

                    break;
                case static::RENEW_TYPE_MONTHLY:
                    $endDate = Carbon::now()->day >= static::RECURRENT_DAY
                        ? Carbon::now()->addMonth()->day(static::RECURRENT_DAY)->toDateTimeString()
                        : Carbon::now()->day(static::RECURRENT_DAY)->toDateTimeString();
                    $startDate = Carbon::now()->toDateTimeString();
                    break;
            }
        }

        foreach ($data as $subscription) {
            $partnership = $subscription['partnership'];
            $isRecurrent = (bool)$subscription['is_recurrent'];

            if ($type !== static::RENEW_TYPE_MONTHLY && $partnership === Partnership::LIMITED) {

                    $endDate = Carbon::now()->addMonths(static::LIMITED_YEAR)->toDateTimeString();
            }


            $this->forFill['app_id'] = $subscription['app_id']; //@TODO check right user, status, partnership
            $this->forFill['authorize_id'] = $subscription['authorize_id'];
            $this->forFill['start_date'] = $startDate;
            $this->forFill['end_date'] = $endDate;
            $this->forFill['status'] = Subscription::STATUS_ACTIVE;
            $this->forFill['partnership'] = $partnership;
            $this->forFill['is_recurrent'] = $isRecurrent;
            $this->forFill['user_id'] = $subscription['user_id'];
            $this->forFill['price'] = $subscription['price'];
            $this->forFill['paid'] = $subscription['price'];

            $this->updateClosed($this->forFill);

            $return[] = $this->getModel()->create($this->forFill);

        }
        $return = new Collection($return);

        if (!empty($return)) {
            $subIds = array_fetch($return->toArray(), 'id');
            $this->getModel()->whereNotIn('id', $subIds)->update(['status' => Subscription::STATUS_CLOSED]);
        }


        return $return;
    }

    /**
     * @param array $subscriptions
     *
     * @return \Generator;
     */
    private function subEndDate(array $subscriptions)
    {
        foreach ($subscriptions as $sub) {
            $cmd = (yield  $this->getModel()
                ->whereAppId($sub['app_id'])  //@TODO payment pending check
                ->whereUserId($sub['user_id'])
                ->where('status', '<>', Subscription::STATUS_CLOSED)
                ->first());

            if ($cmd === 'stop') {

                return;
            }
        }
    }

    /**
     * @param array $forFill
     *
     * @return boolean
     */
    private function updateClosed(array $forFill)
    {

        return $this->getModel()
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
        $subscriptions->each(function ($sub) {
            $sub->user->role = $sub->user->roles->first()->role;
            foreach ($sub->user->apps_statuses as $appStatus) {
                if ($appStatus->id == $sub->app->id) {
                    $sub->app->is_active = $appStatus->pivot->is_active;
                    break;
                }
            }
        });

        return $subscriptions;
    }
} 