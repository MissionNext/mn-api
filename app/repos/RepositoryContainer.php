<?php

namespace MissionNext\Repos;


use Illuminate\Foundation\Application;
use MissionNext\Api\Auth\ISecurityContextAware;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Repos\Affiliate\AffiliateRepositoryInterface;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;
use MissionNext\Repos\Inquire\InquireRepository;
use MissionNext\Repos\Inquire\InquireRepositoryInterface;
use MissionNext\Repos\User\JobRepositoryInterface;
use MissionNext\Repos\User\UserRepositoryInterface;
use MissionNext\Repos\Languages\LanguageRepositoryInterface;

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

    public function   offsetSet (  $offset ,  $value )
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