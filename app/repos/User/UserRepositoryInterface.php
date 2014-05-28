<?php

namespace MissionNext\Repos\User;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use MissionNext\Models\User\User;
use MissionNext\Repos\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{

    const KEY = "user";
    /**
     * @return User
     */
    public function getModel();

    public function profileStructure(BelongsToMany $query);

}