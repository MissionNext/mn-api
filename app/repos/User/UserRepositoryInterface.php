<?php

namespace MissionNext\Repos\User;

use MissionNext\Models\User\User;
use MissionNext\Repos\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * @return User
     */
    public function getModel();

}