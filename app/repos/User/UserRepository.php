<?php
namespace MissionNext\Repos\User;


use MissionNext\Models\User\User;

class UserRepository extends AbstractUserRepository implements UserRepositoryInterface
{
    protected $modelClassName = User::class;

    /**
     * @return User
     */
    public function getModel()
    {

        return $this->model;
    }

} 