<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use MissionNext\Models\Admin\AdminUserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AdminController extends AdminBaseController {

    public function login() {

        if($this->request->isMethod('post')) {

            Input::flash();

            $validator = Validator::make(Input::all(), AdminUserModel::$rules);

            if($validator->passes()) {

                $adminUser = AdminUserModel::where('username', Input::get('username'))->first();

                if(!is_null($adminUser)) {

                    dd('user found');

                } else {

                    Session::flash('info', 'Some field is wrong!');
                    return Redirect::route('login');
                }

            } else {

                return Redirect::route('login')->withInput()->withErrors($validator);
            }

        }

        return View::make('admin.loginForm');
    }

} 