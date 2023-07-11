<?php


namespace App\Models\Observers;


use App\Models\Configs\GlobalConfig;
use App\Models\Subscription\Subscription;

class SubscriptionObserver implements ModelObserverInterface
{

    public function updating(Subscription $model)
    {
        $absDaysLeft = abs($model->days_left);

        if ($model->days_left < 0 ){
            $gracePeriod = (new GlobalConfig())->gracePeriod();
            if ($absDaysLeft < $gracePeriod) {
                $model->status = Subscription::STATUS_GRACE;
            } elseif ($absDaysLeft > $gracePeriod) {
                $model->status = Subscription::STATUS_EXPIRED;
            }
        }else{
            $model->status = Subscription::STATUS_ACTIVE;
        }

    }

}
