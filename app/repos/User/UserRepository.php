<?php
namespace MissionNext\Repos\User;


use MissionNext\Models\Application\Application;
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

    /**
     * @param Application $app
     *
     * @return bool
     */
    public function addApp(Application $app)
    {
        if (!$this->getModel()->hasApp($app)) {
            $this->getModel()->apps()->attach($app->id);

            return true;
        }

        return false;
    }

} 