<?php

namespace MissionNext\Models\Observers;

use MissionNext\Models\ProfileInterface;
use MissionNext\Models\User\User;
use MissionNext\Models\Role\Role;
use MissionNext\Repos\RepositoryInterface;
use MissionNext\Repos\User\AbstractUserRepository;

abstract class AbstractUserObserver
{
    /** @var  AbstractUserRepository */
    private $repo;

    abstract public function saved(ProfileInterface $model);

    abstract public function created(ProfileInterface $model);

    /**
     * @param RepositoryInterface $repo
     *
     * @return $this
     */
    public function setUserRepo(RepositoryInterface $repo)
    {
       $this->repo = $repo;

       return $this;
    }

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

    /**
     * @return AbstractUserRepository
     */
    public function getRepo()
    {

        return $this->repo;
    }

    /**
     * @param ProfileInterface $user
     *
     * @return AbstractUserRepository
     */
    public function getUserRepo(ProfileInterface $user)
    {

       return $user->observer()->getRepo();
    }

}