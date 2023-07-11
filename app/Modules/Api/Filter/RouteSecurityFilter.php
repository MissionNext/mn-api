<?php

namespace App\Modules\Api\Filter;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as Router;
use App\Modules\Api\Exceptions\SecurityContextException;
// use App\Modules\Api\Auth\SecurityContext;
use App\Modules\Api\Facade\SecurityContext;
use App\Models\DataModel\BaseDataModel;
use App\Models\User\User as UserModel;
use Illuminate\Http\Request as LRequest;
use App\Routing\Routing;
use Input;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class RouteSecurityFilter
{

    const AUTHORIZE = 'authorize';
    const AUTHORIZE_M = 'authorize';
    const ROLE = 'role';
    const ROLE_ADMIN_AREA = 'adminAreaRole';
    const ROLE_M = 'role';

    static public $ALLOWED_ROLES = [BaseDataModel::AGENCY, BaseDataModel::CANDIDATE, BaseDataModel::ORGANIZATION, BaseDataModel::JOB];

    /**
     * @param Router $route
     * @param LRequest $request
     * @throws SecurityContextException
     */
    public function role(Router $route, LRequest $request)
    {
        $symfRequest = $request->request;
        if ( $symfRequest->has("_method") && strtoupper($symfRequest->get("_method")) === "PUT" ){
             $symfRequest->remove("_method");
        }

        $user_id = Route::input('user', Route::input('profile', null));
        $role = Route::input('type');
        $job_id = Route::input('job');

        $user = $user_id ? UserModel::findOrFail($user_id) : null;
        if ($user) {
            $role = $user->role();
        }

        $role = $route->getName() ===  Routing::ROUTE_CREATE_USER ? $request->request->get("role") : $role;
        $role = $route->getName() === Routing::ROUTE_CREATE_JOB ? BaseDataModel::JOB : $role;
        $role = $job_id ? BaseDataModel::JOB : $role;

        if ($role) {
            if (static::isAllowedRole($role)) {
                SecurityContext::getToken()->setRoles([$role]);

                $newRole = SecurityContext::getToken()->getRole();

                $view_log = new Logger('View Logs');
                $view_log->pushHandler(new StreamHandler(storage_path().'/logs/custom_logs/debug/'. date('Y-m-d').'.txt', Logger::INFO));
                $view_log->info('');
                $view_log->info('');
                $view_log->info('Role: '. $newRole);
                $view_log->info('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
            } else {

                throw new SecurityContextException("'$role' role doesn't exists", SecurityContextException::ON_SET_ROLE);
            }
        }
    }

    /**
     * @param Router $route
     * @param LRequest $request
     * @throws SecurityContextException
     */
    public function adminAreaRole(Router $route, LRequest $request)
    {
        $this->role($route, $request);
        SecurityContext::getFacadeRoot()->setIsAdminArea(true);
    }

    public function authorize(Router $route, $request)
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
