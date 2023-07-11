<?php


namespace App\Modules\Api\Traits;

use Illuminate\Support\Facades\DB;
use  App\Modules\Api\Auth\Token;
use App\Modules\Api\Facade\SecurityContext as FSecurityContext;
use App\Modules\Api\Auth\SecurityContext;
use App\Models\Application\Application;

trait Controller
{
    /**
     * @return SecurityContext
     */
    protected function securityContext()
    {

        return FSecurityContext::getInstance();
    }

    /**
     * @return Token
     */
    protected function getToken()
    {

        return $this->securityContext()->getToken();
    }

    /**
     * @return \App\Models\User\User
     */
    protected function getUser()
    {

        return $this->getToken()->currentUser();
    }

    /**
     * @return Application
     */
    protected function getApp()
    {

        return $this->getToken()->getApp();
    }

    /**
     * @return  []
     */
    protected function getLogQueries()
    {

        return DB::getQueryLog();
    }
}
