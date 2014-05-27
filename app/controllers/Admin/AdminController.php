<?php
namespace MissionNext\Controllers\Admin;

use Cartalyst\Sentry\Users\WrongPasswordException;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\Config;
use Cartalyst\Sentry\Users\LoginRequiredException as LoginRequired;
use Cartalyst\Sentry\Users\PasswordRequiredException as PasswordRequired;
use Cartalyst\Sentry\Users\WrongPasswordException as WrongPass;
use Cartalyst\Sentry\Users\UserNotFoundException as UserNotFound;
use Cartalyst\Sentry\Users\UserNotActivatedException as UserNotActivated;
use Cartalyst\Sentry\Throttling\UserSuspendedException as UserSuspended;
use Cartalyst\Sentry\Throttling\UserBannedException as UserBanned;
use Cartalyst\Sentry\Users\UserExistsException as UserExist;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException as UserAlreadyActivated;


class AdminController extends AdminBaseController {

    public function login() {


        if($this->request->isMethod('post')) {



            Input::flash();

            $input = Input::only('username', 'password');
            $rules = array(
                'username' => 'required|min:3|max:200',
                'password' => 'required|min:6'
            );

//            $validator = Validator::make($input, $rules);
//
//            if ($validator->fails()) {
//
//                return Redirect::route('login')->withInput()->withErrors($validator);
//            }

              try {

            $credentials = array(
                'username' => Input::get('username'),
                'password' => Input::get('password'),
            );

            $user = Sentry::authenticate($credentials, false);

            return Redirect::route('adminHomepage');
              } catch(WrongPasswordException $e) {

              }
        }

        return View::make('admin.loginForm');
    }

    public function tmp() {
//
//
//
//
//
//            $adminUser = AdminUserModel::where('username', Input::get('username'))->first();
//
//            if(!is_null($adminUser)) {
//
//                if (Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password')))) {
//                    return Redirect::intended('adminHomepage');
//                }
////                Auth::login($adminUser);
////                return Redirect::route('adminHomepage');
//
//            } else {
//
//                return Redirect::route('login')->withInput()->withErrors($validator);
//
////                dd($adminUser);
////                Auth::login($adminUser);
////                return Redirect::route('adminHomepage');
////                return View::make('admin.adminHomepage');
//            }









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

} 