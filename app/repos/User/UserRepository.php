<?php
namespace MissionNext\Repos\User;

use MissionNext\Repos\AbstractRepository;
use MissionNext\Models\User\User;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
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