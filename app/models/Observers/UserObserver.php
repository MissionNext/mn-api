<?php
namespace MissionNext\Models\Observers;

use MissionNext\Models\User\User;

class UserObserver extends AbstractUserObserver implements ModelObserverInterface
{
    public function saved(User $model){
        /** @var $model User */
        foreach ($model->getOnSaved() as $func) {
            call_user_func($func, $model);
        }
    }

} 