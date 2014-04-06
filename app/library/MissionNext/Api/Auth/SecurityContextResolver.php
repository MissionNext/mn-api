<?php


namespace MissionNext\Api\Auth;

use MissionNext\Facade\SecurityContext;

class SecurityContextResolver extends ObjectResolver
{

    /**
     * @param ISecurityContextAware $class
     * @return mixed
     */
    public function __construct(ISecurityContextAware $class)
    {
        $class->setSecurityContext(SecurityContext::getInstance());
        $this->resolvedObject = $class;
    }

} 