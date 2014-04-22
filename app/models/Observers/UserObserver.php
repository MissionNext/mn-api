<?php
namespace MissionNext\Models\Observers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use MissionNext\Models\ProfileInterface;
use MissionNext\Models\User\User;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;
class UserObserver extends AbstractUserObserver implements ModelObserverInterface
{

    public function saved(ProfileInterface $model)
    {
        $this->runClosures($model, static::SAVED);
    }

    public function created(ProfileInterface $model)
    {
        $this->runClosures($model, static::CREATED);
        $this->getUserRepo($model)->insertUserCachedData($model);
    }

    protected function runClosures(ProfileInterface $model, $event)
    {
        $method = "getOn".ucfirst($event);
        /** @var $model User */
        foreach ($model->$method() as $func) {
            call_user_func($func, $model);
        }
    }

} 