<?php

namespace App\Repos\User;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User\User;
use App\Repos\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{

    const KEY = "user_repo";
    /**
     * @return User
     */
    public function getModel();

    public function profileStructure(BelongsToMany $query);

}
