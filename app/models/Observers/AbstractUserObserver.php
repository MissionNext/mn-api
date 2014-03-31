<?php

namespace MissionNext\Models\Observers;

use MissionNext\Models\User\User;
use MissionNext\Models\Role\Role;

abstract class AbstractUserObserver
{

    abstract public function saved(User $model);

    /**
     * @var Role
     */
    public $role;

    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     *
     * @return Role
     */
    public function getRole()
    {

        return $this->role;
    }

} 