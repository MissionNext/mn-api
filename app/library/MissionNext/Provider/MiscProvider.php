<?php

namespace MissionNext\Provider;

use Illuminate\Support\ServiceProvider;
use MissionNext\Repos\Field\FieldRepository;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use Illuminate\Support\Facades\App;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;

class MiscProvider extends ServiceProvider
{

    public function register()
    {

        App::bind(FieldRepositoryInterface::class, function(){

            //@TODO based on condition return different model Repository
            return new FieldRepository();
        });

        App::bind(UserRepositoryInterface::class, function(){

            return new UserRepository();
        });

    }

} 