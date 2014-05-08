<?php

namespace MissionNext\Provider;

use Illuminate\Support\ServiceProvider;
use MissionNext\Api\Auth\SecurityContextResolver;
use MissionNext\Repos\Field\FieldRepository;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use Illuminate\Support\Facades\App;
use MissionNext\Repos\Form\FormRepository;
use MissionNext\Repos\Form\FormRepositoryInterface;
use MissionNext\Repos\FormGroup\FormGroupRepository;
use MissionNext\Repos\FormGroup\FormGroupRepositoryInterface;
use MissionNext\Repos\Matching\ConfigRepository;
use MissionNext\Repos\Matching\ConfigRepositoryInterface;
use MissionNext\Repos\Matching\ResultsRepository;
use MissionNext\Repos\Matching\ResultsRepositoryInterface;
use MissionNext\Repos\User\JobRepository;
use MissionNext\Repos\User\JobRepositoryInterface;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;
use MissionNext\Repos\ViewField\ViewFieldRepository;
use MissionNext\Repos\ViewField\ViewFieldRepositoryInterface;

class RepositoryProvider extends ServiceProvider
{

    public function register()
    {

        App::bind(FieldRepositoryInterface::class, function () {

            //@TODO based on condition return different model Repository
            return (new SecurityContextResolver(new FieldRepository()))->getResolvedObject();
        });

        App::bind(UserRepositoryInterface::class, function () {
            return (new SecurityContextResolver(new UserRepository()))->getResolvedObject();
        });

        App::bind(ViewFieldRepositoryInterface::class, function () {

            return new ViewFieldRepository();
        });

        App::bind(FormRepositoryInterface::class, function () {

            return new FormRepository();
        });

        App::bind(FormGroupRepositoryInterface::class, function () {

            return (new SecurityContextResolver(new FormGroupRepository()))->getResolvedObject();
        });

        App::bind(JobRepositoryInterface::class, function () {

            return (new SecurityContextResolver(new JobRepository()))->getResolvedObject();
        });

        App::bind(ConfigRepositoryInterface::class, function () {

            return (new SecurityContextResolver(new ConfigRepository()))->getResolvedObject();
        });

        App::bind(ResultsRepositoryInterface::class, function () {

            return new ResultsRepository();
        });

    }

} 