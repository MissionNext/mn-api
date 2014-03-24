<?php


namespace MissionNext\Core\Security;

use MissionNext\Api\Auth\Token;

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
    protected $token;

    /**
     * @param AbstractToken $token
     */
    public function __construct(AbstractToken $token)
    {
        $this->token = $token;
    }

    /**
     * @return AbstractToken
     */
    public function getToken()
    {

        return $this->token;
    }

    /**
     * @return \MissionNext\Models\Application\Application
     */
    abstract  public function getApp();

} 