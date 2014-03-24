<?php

namespace MissionNext\Core\Security;

use MissionNext\Models\Application\Application;


abstract class AbstractToken
{

    protected $roles;

    protected $app;

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