<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use MissionNext\Models\Application\Application;
use MissionNext\Models\User\User;
use Illuminate\Support\Facades\DB;

class AjaxController extends AdminBaseController {

    public function filterBy() {

        if ($this->request->isMethod('post'))
        {
            $appId = Input::get('appId');
            $take = Input::get('take');

            if ($appId == 'all') {
                $usersCount = User::all();
                $users = DB::table('users')
                    ->orderBy('id')
                    ->get();

                return View::make('admin.user.ajax.filteredUsers', array('users' => $users, 'count' => count($usersCount)));
            } else {
                $app = Application::find($appId);
                $usersCount = $app->users;
                $users = $this->getFilteredByAppUsers($appId, 0, $take);

                return View::make('admin.user.ajax.filteredUsers', array('users' => $users, 'count' => count($usersCount)));
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
        $users = $this->getFilteredByAppUsers($appId, $skip, $take);

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
}