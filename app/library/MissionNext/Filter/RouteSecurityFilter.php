<?php

namespace MissionNext\Filter;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\User\User as UserModel;

class RouteSecurityFilter
{

    const AUTHORIZE = 'authorize';
    const AUTHORIZE_M = 'authorize';
    const ROLE = 'role';
    const ROLE_M = 'role';

    static public $ALLOWED_ROLES = [BaseDataModel::AGENCY, BaseDataModel::CANDIDATE, BaseDataModel::ORGANIZATION];

    public function role($route)
    {
        $user_id = Route::input('user', Route::input('profile', null));
        $role = Route::input('type');

        $role = $user_id ? UserModel::find($user_id)->roles()->first()->role : $role;

        if (static::isAllowedRole($role)){

            SecurityContext::getToken()->setRoles([$role]);
        }
    }

    public function authorize($route, $request)
    {
        App::make("rest.listener.sync")->handle();
    }

    /**
     * @param $role
     *
     * @return bool
     */
    public static function isAllowedRole($role)
    {

        return in_array($role, static::$ALLOWED_ROLES);
    }

} 