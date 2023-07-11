<?php

namespace App\Modules\Api\Auth;


use App\Models\Application\Application;
use App\Modules\Api\Core\Security\AbstractContext;

class SecurityContext extends AbstractContext
{
    private $isAdminArea = false;

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->token->getApp();
    }

    public function isAdminArea()
    {

       return $this->isAdminArea;
    }

    /**
     * @param $boolean
     *
     * @return $this
     */
    public function setIsAdminArea($boolean)
    {
        $this->isAdminArea = $boolean;

        return $this;
    }

    /**
     * @return bool|string
     */
    public function role()
    {
        return $this->token->getRole();
    }

}
