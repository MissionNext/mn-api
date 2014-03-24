<?php

namespace MissionNext\Api\Auth;


use MissionNext\Core\Security\AbstractContext;

class SecurityContext extends AbstractContext
{

    /**
     * @return \MissionNext\Models\Application\Application
     */
    public function getApp()
    {

        return $this->token->getApp();
    }

    /**
     * @return bool|string
     */
    public function role()
    {

        return $this->token->getRole();
    }

} 