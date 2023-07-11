<?php

namespace App\Repos;


use Illuminate\Foundation\Application;
use App\Modules\Api\Auth\ISecurityContextAware;
use App\Modules\Api\Auth\SecurityContext;
use App\Repos\Affiliate\AffiliateRepositoryInterface;
use App\Repos\CachedData\UserCachedRepositoryInterface;
use App\Repos\Field\FieldRepositoryInterface;
use App\Repos\Form\FormRepositoryInterface;
use App\Repos\Inquire\InquireRepositoryInterface;
use App\Repos\Subscription\SubConfigRepositoryInterface;
use App\Repos\Subscription\SubscriptionRepositoryInterface;
use App\Repos\Subscription\TransactionRepositoryInterface;
use App\Repos\User\JobRepositoryInterface;
use App\Repos\User\ProfileRepositoryFactory;
use App\Repos\User\UserRepositoryInterface;
use App\Repos\Languages\LanguageRepositoryInterface;
use App\Repos\Translation\FieldRepositoryInterface as TransFieldRepoInterface;


class RepositoryContainer implements \ArrayAccess, ISecurityContextAware, RepositoryContainerInterface
{
    /** @var  SecurityContext */
    private $securityContext;

    private $container = [];

    /**
     * @param SecurityContext $securityContext
     *
     * @return $this
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
       $this->securityContext = $securityContext;

       return $this;
    }

    /**
     * @return SecurityContext
     */
    public function securityContext()
    {

        return $this->securityContext;
    }

    public function __construct(Application $app)
    {
        $this->container = [
            InquireRepositoryInterface::KEY   => $app->make(InquireRepositoryInterface::class)->setRepoContainer($this),
            JobRepositoryInterface::KEY   => $app->make(JobRepositoryInterface::class)->setRepoContainer($this),
            UserRepositoryInterface::KEY   => $app->make(UserRepositoryInterface::class)->setRepoContainer($this),
            AffiliateRepositoryInterface::KEY   => $app->make(AffiliateRepositoryInterface::class)->setRepoContainer($this),
            UserCachedRepositoryInterface::KEY   => $app->make(UserCachedRepositoryInterface::class)->setRepoContainer($this),
            LanguageRepositoryInterface::KEY => $app->make(LanguageRepositoryInterface::class)->setRepoContainer($this),
            TransFieldRepoInterface::KEY => $app->make(TransFieldRepoInterface::class)->setRepoContainer($this),
            FieldRepositoryInterface::KEY => $app->make(FieldRepositoryInterface::class)->setRepoContainer($this),
            FormRepositoryInterface::KEY => $app->make(FormRepositoryInterface::class)->setRepoContainer($this),
            ProfileRepositoryFactory::KEY => $app->make(ProfileRepositoryFactory::class)->setRepoContainer($this),
            SubConfigRepositoryInterface::KEY => $app->make(SubConfigRepositoryInterface::class)->setRepoContainer($this),
            SubscriptionRepositoryInterface::KEY => $app->make(SubscriptionRepositoryInterface::class)->setRepoContainer($this),
            TransactionRepositoryInterface::KEY => $app->make(TransactionRepositoryInterface::class)->setRepoContainer($this),
        ];
    }

    public function offsetExists ( $offset )
    {
        return isset($this->container[$offset]);
    }

    public function offsetGet (  $offset )
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function  offsetSet (  $offset ,  $value )
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function  offsetUnset (  $offset )
    {
        unset($this->container[$offset]);
    }
}
