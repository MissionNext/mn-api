<?php

namespace MissionNext\Api\Service\Matching\Queue;


use MissionNext\Facade\SecurityContext;

abstract class QueueMatching
{
    /**
     * @return \MissionNext\Api\Auth\SecurityContext
     */
    protected  function securityContext()
    {


        return SecurityContext::getInstance();
    }
} 