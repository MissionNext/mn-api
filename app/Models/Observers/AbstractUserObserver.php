<?php

namespace App\Models\Observers;

use App\Models\ProfileInterface;


abstract class AbstractUserObserver
{

    abstract public function saved(ProfileInterface $model);

    abstract public function created(ProfileInterface $model);

}
