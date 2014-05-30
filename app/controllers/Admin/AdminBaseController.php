<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
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
use Illuminate\View\Environment as View;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag as Session;
use Illuminate\Validation\Validator;
use Illuminate\Routing\Redirector as Redirect;

class AdminBaseController extends Controller {

    protected $request;
    protected $view;
    protected $session;
    protected $validator;
    protected $redirect;


    public function __construct(Request $request, View $view, Session $session, Redirect $redirect) {
        $this->request = $request;
        $this->view = $view;
        $this->session = $session;
//        $this->validator = $validator;
        $this->redirect = $redirect;
    }

    public function login() {
        $this->session->set('info', 'dd');
        if($this->request->isMethod('post')) {
//            Input::flash();
            $input = $this->request->only('username', 'password');
            $rules = array(
                'username' => 'required|min:3|max:200',
                'password' => 'required|min:6'
            );
//            $this->validator->validate($input, $rules);
//                Validator::make($input, $rules);

//            $validat
//            if ($validator->fails()) {
//            if(!$this->validator->validate($input, $rules)) {
//
//                return $this->redirect->route('login')->withInput();
////                return Redirect::route('login')->withInput()->withErrors($validator);
//            }
            try {
                $user = Sentry::authenticate($input, false);

                return $this->redirect->route('adminHomepage');
//                return Redirect::route('adminHomepage');
            } catch (LoginRequired $e) {
                $this->session->set('info', 'Login field is required.');
//                Session::flash('info', 'Login field is required.');
            } catch (PasswordRequired $e) {
                $this->session->set('info', 'Password field is required.');
//                Session::flash('info', 'Password field is required.');
            } catch (WrongPass $e) {
                $this->session->set('info', 'Wrong password, try again.');
//                Session::flash('info', 'Wrong password, try again.');
            } catch (UserNotFound $e) {
                $this->session->set('info', 'User was not found.');
//                Session::flash('info', 'User was not found.');
            } catch (UserNotActivated $e) {
                $this->session->set('info', 'User is not activated.');
//                Session::flash('info', 'User is not activated.');
            }
                // The following is only required if the throttling is enabled
            catch (UserSuspended $e) {
                $this->session->set('info', 'User is suspended.');
//                Session::flash('info', 'User is suspended.');
            } catch (UserBanned $e) {
                $this->session->set('info', 'User is banned.');
//                Session::flash('info', 'User is banned.');
            }
        }

        return $this->view->make('admin.loginForm');
//        return View::make('admin.loginForm');

    }

} 