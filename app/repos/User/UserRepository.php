<?php
namespace MissionNext\Repos\User;

use Illuminate\Support\Facades\Hash;
use MissionNext\Models\Role\Role;
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