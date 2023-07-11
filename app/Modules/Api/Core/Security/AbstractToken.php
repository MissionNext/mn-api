<?php

namespace  App\Modules\Api\Core\Security;

use App\Models\Application\Application;
use App\Models\Language\LanguageModel;
use App\Models\User\User;


abstract class AbstractToken
{

    protected $roles;

    protected $app;

    /**
     * @return User
     */
    abstract public function currentUser();

    /**
     * @return LanguageModel|null
     */
    abstract public function language();

    /**
     * @return Application;
     */
    public function getApp()
    {
        return $this->app;
    }

    public function setApp(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    public function __constructor(array $roles = null)
    {
        $this->roles = $roles;
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    abstract  public function setRoles(array $roles = null);


    /**
     * @return mixed
     */
    abstract  public function getRoles();


}
