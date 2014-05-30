<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Cartalyst\Sentry\Users\LoginRequiredException as LoginRequired;
use Cartalyst\Sentry\Users\PasswordRequiredException as PasswordRequired;
use Cartalyst\Sentry\Users\WrongPasswordException as WrongPass;
use Cartalyst\Sentry\Users\UserNotFoundException as UserNotFound;
use Cartalyst\Sentry\Users\UserNotActivatedException as UserNotActivated;
use Cartalyst\Sentry\Throttling\UserSuspendedException as UserSuspended;
use Cartalyst\Sentry\Throttling\UserBannedException as UserBanned;
use Cartalyst\Sentry\Users\UserExistsException as UserExist;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException as UserAlreadyActivated;
use MissionNext\Models\Application\Application;

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

                return Redirect::route('login')->withInput()->withErrors($validator);
            }
            try {
                $user = Sentry::authenticate($input, false);

                return Redirect::route('adminHomepage');
            } catch (LoginRequired $e) {
                Session::flash('info', 'Login field is required.');
            } catch (PasswordRequired $e) {
                Session::flash('info', 'Password field is required.');
            } catch (WrongPass $e) {
                Session::flash('info', 'Wrong password, try again.');
            } catch (UserNotFound $e) {
                Session::flash('info', 'User was not found.');
            } catch (UserNotActivated $e) {
                Session::flash('info', 'User is not activated.');
            }
            // The following is only required if the throttling is enabled
            catch (UserSuspended $e) {
                Session::flash('info', 'User is suspended.');
            } catch (UserBanned $e) {
                Session::flash('info', 'User is banned.');
            }
        }

        return View::make('admin.loginForm');
    }

    public function index()
    {
        $applications = Application::orderBy('id')->paginate(5);

        return View::make('admin.application.applications', array(
            'applications' => $applications,
        ));
    }

    public function create()
    {
        if ($this->request->isMethod('post')) {

            Input::flash();
            $rules = array(
                'app_name' => 'required|min:3',
                'public_key' => 'required|min:3',
                'private_key' => 'required|min:3'
            );

            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {

                return Redirect::route('applicationCreate')->withInput()->withErrors($validator);
            }

            $newApp = new Application();
            $newApp->name = Input::get('app_name');
            $newApp->public_key = Input::get('public_key');
            $newApp->private_key = Input::get('private_key');
            $newApp->save();

            return Redirect::route('applications');
        }

        return View::make('admin.application.create');
    }

    public function edit($id) {

        $application = Application::find($id);

        if(is_null($application)) {

            return Redirect::route('applications');
        }

        if ($this->request->isMethod('post')) {
            Input::flash();
            $rules = array(
                'name' => 'required|min:3',
                'public_key' => 'required|min:3',
            );
            $validator = Validator::make(Input::only('name', 'public_key'), $rules);
            if ($validator->fails()) {

                return Redirect::route('applicationEdit', array('id'=> $id))->withInput()->withErrors($validator);
            }

            $private_key = Input::get('private_key');

            $application->name = Input::get('name');
            $application->public_key = Input::get('public_key');
            ($private_key == '') ? : $application->private_key = $private_key;
            $application->save();

            return Redirect::route('applications');
        }

        return View::make('admin.application.edit', array('application' => $application));
    }

    public function delete($id) {
        if($this->request->isMethod('delete')) {

            $application = Application::find($id);
            $application->delete();

            return Redirect::route('applications');
        } else {

            return Redirect::route('applications');
        }
    }

}