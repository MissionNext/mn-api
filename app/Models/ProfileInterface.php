<?php


namespace App\Models;


use Illuminate\Support\Collection;
use App\Models\Application\Application;
use App\Repos\User\JobRepository;
use App\Repos\User\UserRepository;

interface ProfileInterface extends ModelInterface
{
    public function appIds();

    /**
     * @return Collection
     */
    public function appData();

    public function hasApp(Application $app);

    public function addApp(Application $app);

    public function hasRole($check);

    public function role();

    /**
     * @return UserRepository|JobRepository
     */
    public function getRepo();
}
