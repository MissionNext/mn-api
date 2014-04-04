<?php

namespace MissionNext\Repos\User;

use Illuminate\Support\Collection;
use MissionNext\Models\User\User;
use MissionNext\Repos\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * @return User
     */
    public function getModel();

    public function profileStructure(Collection $query);

}