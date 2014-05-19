<?php


namespace MissionNext\Models;


use MissionNext\Models\Application\Application;
use MissionNext\Repos\User\JobRepository;
use MissionNext\Repos\User\UserRepository;

interface ProfileInterface extends ModelInterface
{
    public function appIds();

    public function hasApp(Application $app);

    public function addApp(Application $app);

    /**
     * @return UserRepository|JobRepository
     */
    public function getRepo();
} 