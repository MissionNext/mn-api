<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use MissionNext\Models\User\User;
use MissionNext\Models\Application\Application;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

class UserController extends AdminBaseController {

    /**
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        $users = User::orderBy('id')->paginate(15);

        $apps = Application::all()->toArray();
        $arrRez = array();
        foreach($apps as $app) {
            $arrRez = array_add($arrRez, $app['id'], $app['name']);
        }

        return View::make('admin.user.users', array(
            'users' => $users,
            'apps'  => $apps,
        ));
    }

    /**
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        if ($this->request->isMethod('post')) {
            Input::flash();
            $rules = array(
                'username' => 'required|min:3',
                'email' => 'required|email',
                'password' => 'required|min:3'
            );

            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {

                return Redirect::route('userCreate')->withInput()->withErrors($validator);
            }

            $user = new User();
            $user->username = Input::get('username');
            $user->email = Input::get('email');
            $user->password = Input::get('password');
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();
            $name = $user->username;
            Session::flash('info', "user <strong>$name</strong> successfully created");

            return Redirect::route('users');
        }

        return View::make('admin.user.create');
    }

    /**
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $user = User::find($id);

        if(is_null($user)) {
            Session::flash('warning', "user with ID $id not found");

            return Redirect::route('users');
        }
        if ($this->request->isMethod('post')) {
            Input::flash();
            $rules = array(
                'username' => 'required|min:3',
                'email' => 'required|email',
            );
            $validator = Validator::make(Input::only('username', 'email'), $rules);
            if ($validator->fails()) {

                return Redirect::route('userEdit', array('id'=> $id))->withInput()->withErrors($validator);
            }

            $password = Input::get('password');

            $user->username = Input::get('username');
            $user->email = Input::get('email');
            ($password == '') ? : $user->password = $password;
            $user->save();
            $name = $user->username;
            Session::flash('info', "user <strong>$name</strong> successfully updated");

            return Redirect::route('users');
        }

        return View::make('admin.user.edit', array('user' => $user));
    }

    public function delete($id) {
        if($this->request->isMethod('delete')) {

            $user = User::find($id);
            $name = $user->username;
            $user->delete();
            Session::flash('info', "user <strong>$name</strong> successfully deleted");

            return Redirect::route('users');
        } else {

            return Redirect::route('users');
        }
    }
}
