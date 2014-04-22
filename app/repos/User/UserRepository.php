<?php
namespace MissionNext\Repos\User;


use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\User\User;
use MissionNext\Repos\Field\Field;

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