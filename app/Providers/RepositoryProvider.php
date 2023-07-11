<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Modules\Api\Auth\SecurityContextResolver;
use App\Repos\Affiliate\AffiliateRepository;
use App\Repos\Affiliate\AffiliateRepositoryInterface;
use App\Repos\CachedData\UserCachedRepository;
use App\Repos\CachedData\UserCachedRepositoryInterface;
use App\Repos\Field\FieldRepository;
use App\Repos\Field\FieldRepositoryInterface;
use Illuminate\Support\Facades\App;
use App\Repos\Form\FormRepository;
use App\Repos\Form\FormRepositoryInterface;
use App\Repos\FormGroup\FormGroupRepository;
use App\Repos\FormGroup\FormGroupRepositoryInterface;
use App\Repos\Inquire\InquireRepository;
use App\Repos\Inquire\InquireRepositoryInterface;
use App\Repos\Matching\ConfigRepository;
use App\Repos\Matching\ConfigRepositoryInterface;
use App\Repos\Matching\ResultsRepository;
use App\Repos\Matching\ResultsRepositoryInterface;
use App\Repos\RepositoryContainer;
use App\Repos\RepositoryContainerInterface;
use App\Repos\Subscription\SubConfigRepository;
use App\Repos\Subscription\SubConfigRepositoryInterface;
use App\Repos\Subscription\SubscriptionRepository;
use App\Repos\Subscription\SubscriptionRepositoryInterface;
use App\Repos\Subscription\TransactionRepository;
use App\Repos\Subscription\TransactionRepositoryInterface;
use App\Repos\User\JobRepository;
use App\Repos\User\JobRepositoryInterface;
use App\Repos\User\ProfileRepositoryFactory;
use App\Repos\User\UserRepository;
use App\Repos\User\UserRepositoryInterface;
use App\Repos\ViewField\ViewFieldRepository;
use App\Repos\ViewField\ViewFieldRepositoryInterface;
use App\Repos\Languages\LanguageRepositoryInterface;
use App\Repos\Languages\LanguageRepository;
use App\Repos\Translation\FieldRepositoryInterface as TransFieldRepoInterface;
use App\Repos\Translation\FieldRepository as TransFieldRepo;

class RepositoryProvider extends ServiceProvider
{

    public function register()
    {

        App::bind(FieldRepositoryInterface::class, function ($app) {

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

        App::bind(InquireRepositoryInterface::class, function () {

            return (new SecurityContextResolver(new InquireRepository()))->getResolvedObject();
        });

        App::bind(AffiliateRepositoryInterface::class, function () {

            return new AffiliateRepository();
        });

        App::bind(UserCachedRepositoryInterface::class, function () {

            return new UserCachedRepository();
        });

        App::bind(LanguageRepositoryInterface::class, function () {

            return new LanguageRepository();
        });

        App::bind(TransFieldRepoInterface::class, function () {

            return new TransFieldRepo();
        });

        App::bind(ProfileRepositoryFactory::class, function ($app) {

            return new ProfileRepositoryFactory();
        });

        App::bind(SubConfigRepositoryInterface::class, function ($app) {

            return new SubConfigRepository();
        });

        App::bind(SubscriptionRepositoryInterface::class, function ($app) {

            return new SubscriptionRepository();
        });

        App::bind(TransactionRepositoryInterface::class, function ($app) {

            return new TransactionRepository();
        });

        //REPO CONTAINER
        App::bind(RepositoryContainerInterface::class, function(Application $app)
        {

            return (new SecurityContextResolver( new RepositoryContainer($app)))->getResolvedObject();
        });

    }

}
