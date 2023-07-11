<?php


namespace App\Modules\Api\Core\Security;

use App\Models\Application\Application;
use  App\Modules\Api\Auth\Token;

abstract class AbstractContext
{

    /**
     * @return $this
     */
    public function getInstance()
    {

        return $this;
    }

    /**
     * @var Token
     */
    public $token;

    /**
     * @return AbstractToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Application
     */
    abstract  public function getApp();

}
