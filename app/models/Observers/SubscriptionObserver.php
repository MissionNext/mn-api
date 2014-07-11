<?php


namespace MissionNext\Models\Observers;


use MissionNext\Models\Subscription\Subscription;
use MissionNext\Models\User\User;

class SubscriptionObserver implements ModelObserverInterface
{
    public function created(Subscription $model)
    {
        /** @var  $user User */
        $user = User::find( $model->user_id );
        $user->appsStatuses()->detach( $model->app_id );
        $user->appsStatuses()->attach( $model->app_id, ['is_active' => true] );
    }

} 