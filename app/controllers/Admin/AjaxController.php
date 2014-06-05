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
            $id = Input::get('appId');
            $take = Input::get('take');
            $filter = Input::get('filter');

            switch ($filter) {
                case 'app':
                    $app = Application::find($id);
                    $usersCount = $app->users;
                    $users = $this->getFilteredByAppUsers($id, 0, $take);

                    return View::make('admin.user.ajax.filteredUsers', array('users' => $users, 'count' => count($usersCount)));
                    break;
                case 'role':
                    $role = Role::find($id);
                    $usersCount = $role->users;
                    $users = $this->getFilteredByRoleUsers($id, 0, $take);

                    return View::make('admin.user.ajax.filteredUsers', array('users' => $users, 'count' => count($usersCount)));
                    break;
            }

        } else {
            $resp = 'fail';
        }

        return Response::make($resp);
    }

    public function filterByApps() {

        $appId = Input::get('appId');
        $take = Input::get('take');
        $skip = Input::get('skip');
        $filter = Input::get('filter');

        switch($filter) {
            case 'app':
                $users = $this->getFilteredByAppUsers($appId, $skip, $take);
                break;
            case 'role':
                $users = $this->getFilteredByRoleUsers($appId, $skip, $take);
                break;
        }


        return View::make('admin.user.ajax.filteredSliceUsers', array('users' => $users));
    }

    private function getFilteredByAppUsers($appId, $skip = 0, $take = 15) {
        $users = DB::table('users')
            ->join('user_apps', 'users.id', '=', 'user_apps.user_id')
            ->where('user_apps.app_id', '=', $appId)
            ->orderBy('users.id')
            ->skip($skip)
            ->take($take)
            ->get();

        return $users;
    }

    private function getFilteredByRoleUsers($roleId, $skip = 0, $take = 15) {
        $users = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', '=', $roleId)
            ->orderBy('users.id')
            ->skip($skip)
            ->take($take)
            ->get();

        return $users;
    }

}