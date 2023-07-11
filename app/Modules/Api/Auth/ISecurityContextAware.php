<?php


namespace App\Modules\Api\Auth;


interface ISecurityContextAware extends IObjectAware
{

    public function setSecurityContext(SecurityContext $securityContext);

}
