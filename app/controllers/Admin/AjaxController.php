<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use MissionNext\Models\Application\Application;
use MissionNext\Models\Role\Role;
use MissionNext\Models\User\User;
use Illuminate\Support\Facades\DB;

class AjaxController extends AdminBaseController {

    public function filterBy() {

        if ($this->request->isMethod('post'))
        {
            $idArray = explode(',', Input::get('appId'));
            $take = Input::get('take');
            $filter = Input::get('filter');

            switch ($filter) {
                case 'app':
                    $usersCount = $this->getCountByAppUsers($idArray);
                    $users = $this->getFilteredByAppUsers($idArray, 0, $take);

                    return View::make('admin.user.ajax.filteredUsers', array('users' => $users, 'count' => count($usersCount)));
                    break;
                case 'role':
                    $usersCount = $this->getCountByRoleUsers($idArray);
                    $users = $this->getFilteredByRoleUsers($idArray, 0, $take);

                    return View::make('admin.user.ajax.filteredUsers', array('users' => $users, 'count' => count($usersCount)));
                    break;
            }

        } else {
            $resp = 'fail';
        }

        return Response::make($resp);
    }

    public function filterByEth() {

        $idArray = explode(',', Input::get('appId'));
        $take = Input::get('take');
        $skip = Input::get('skip');
        $filter = Input::get('filter');

        switch($filter) {
            case 'app':
                $users = $this->getFilteredByAppUsers($idArray, $skip, $take);
                break;
            case 'role':
                $users = $this->getFilteredByRoleUsers($idArray, $skip, $take);
                break;
        }

        return View::make('admin.user.ajax.filteredSliceUsers', array('users' => $users));
    }

    public function roles() {
        $roles = Role::all()->toArray();

        return Response::json($roles);
    }

    public function apps() {
        $apps = Application::all()->toArray();

        return Response::json($apps);
    }

    private function getFilteredByAppUsers($appId, $skip = 0, $take = 15) {
        $users = DB::table('users')
            ->join('user_apps', 'users.id', '=', 'user_apps.user_id')
            ->whereIn('user_apps.app_id', $appId)
            ->orderBy('users.id')
            ->skip($skip)
            ->take($take)
            ->get();

        return $users;
    }

    private function getCountByAppUsers($appId) {
        $users = DB::table('users')
            ->join('user_apps', 'users.id', '=', 'user_apps.user_id')
            ->whereIn('user_apps.app_id', $appId)
            ->orderBy('users.id')
            ->get();

        return $users;
    }

    private function getFilteredByRoleUsers($roleId, $skip = 0, $take = 15) {
        $users = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->whereIn('user_roles.role_id', $roleId)
            ->orderBy('users.id')
            ->skip($skip)
            ->take($take)
            ->get();

        return $users;
    }

    private function getCountByRoleUsers($roleId) {
        $users = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->whereIn('user_roles.role_id', $roleId)
            ->orderBy('users.id')
            ->get();

        return $users;
    }
}
