<?php

namespace App\Modules\Api\Facade;

use Illuminate\Support\Facades\Facade;

class SecurityContext extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'security_context';
    }

}
