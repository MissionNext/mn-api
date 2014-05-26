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

            $input = Input::only('username', 'password');
            $rules = array(
                'username' => 'required|min:3|max:200',
                'password' => 'required|min:6'
            );

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                Session::flash('info', 'Some field is wrong!');
                return Redirect::route('login')->withInput()->withErrors($validator);
            }

            $adminUser = AdminUserModel::where('username', Input::get('username'))->first();

            if(!is_null($adminUser)) {

                Auth::login($adminUser);
                return Redirect::route('adminHomepage');

            } else {

                Session::flash('info', 'Some field is wrong!');
                return Redirect::route('login')->withInput()->withErrors($validator);

//                dd($adminUser);
//                Auth::login($adminUser);
//                return Redirect::route('adminHomepage');
//                return View::make('admin.adminHomepage');
            }









//            if($validator->passes()) {
//
//                $adminUser = AdminUserModel::where('username', Input::get('username'))->first();
//
//                if(!is_null($adminUser)) {
//
//                    dd('user found');
//
//                } else {
//
//                    Session::flash('info', 'Some field is wrong!');
//                    return Redirect::route('login');
//                }
//
//            } else {
//
//                return Redirect::route('login')->withInput()->withErrors($validator);
//            }

        }

        return View::make('admin.loginForm');
    }

} 