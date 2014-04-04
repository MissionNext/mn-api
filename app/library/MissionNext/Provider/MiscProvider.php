<?php

namespace MissionNext\Provider;

use Illuminate\Support\ServiceProvider;
use MissionNext\Repos\Field\FieldRepository;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use Illuminate\Support\Facades\App;
use MissionNext\Repos\Form\FormRepository;
use MissionNext\Repos\Form\FormRepositoryInterface;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;
use MissionNext\Repos\ViewField\ViewFieldRepository;
use MissionNext\Repos\ViewField\ViewFieldRepositoryInterface;

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

        App::bind(ViewFieldRepositoryInterface::class, function(){

            return new ViewFieldRepository();
        });

        App::bind(FormRepositoryInterface::class, function(){

            return new FormRepository();
        });

    }

} 