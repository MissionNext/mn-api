<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use MissionNext\Models\Application\Application;
use MissionNext\Models\User\User;

class AjaxController extends AdminBaseController {

    public function filterBy() {


        $appId = Input::get('appId');

        if ($appId == 'all') {
//            $users = User::orderBy('id')->paginate(15);

            return Redirect::route('users');
//            return View::make('admin.user.ajax.users', array('users' => $users, 'pagination' => 'yes'));
        } else {
            $app = Application::find($appId);
            $users = $app->users;

            return View::make('admin.user.ajax.users', array('users' => $users));
        }
//
//        $appId = Input::get('appId');
//
//        if ($appId == 'all') {
//            $users = User::all();
//        } else {
//            $app = Application::find($appId);
//            $users = $app->users;
//
//
//            dd($users);
//        }
//

        if ($this->request->isMethod('post'))
        {
            $appId = Input::get('appId');

            if ($appId == 'all') {
                $users = User::all();

                return View::make('admin.user.ajax.users', array('users' => $users));
            } else {
                $app = Application::find($appId);
                $users = $app->users;

                return View::make('admin.user.ajax.users', array('users' => $users));
            }

        } else {
            $resp = 'fail';
        }


        return Response::make($resp);
    }
}