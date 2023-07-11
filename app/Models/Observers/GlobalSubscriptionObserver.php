<?php


namespace App\Models\Observers;


use App\Models\Configs\GlobalConfig;
use App\Models\Subscription\GlobalSubscription;
use App\Models\Subscription\Subscription;

class GlobalSubscriptionObserver implements ModelObserverInterface
{

    public function updating(GlobalSubscription $model)
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
