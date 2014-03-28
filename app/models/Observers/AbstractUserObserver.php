<?php

namespace MissionNext\Models\Observers;

use MissionNext\Models\User\User;

abstract class AbstractUserObserver
{

    abstract public function saved(User $model);

} 