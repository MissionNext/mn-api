<?php

namespace MissionNext\Models\Observers;

use MissionNext\Models\Role\Role;
use MissionNext\Models\User\User;

class UserObserver extends AbstractUserObserver implements ModelObserveInterface
{
    /**
     * @var Role
     */
    private $role;


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

    /**
     * @param User $model
     */
    public function saved(User $model)
    {
       /** @var $model User */
       $model->roles()->attach($model->observer()->getRole());
    }

} 