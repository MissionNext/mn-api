<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use MissionNext\Models\Role\Role;
use MissionNext\Models\User\User;
use MissionNext\Models\Application\Application;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class UserController extends AdminBaseController {

    /**
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        $users = User::orderBy('id')->paginate(AdminBaseController::PAGINATE);

        return View::make('admin.user.users', array(
            'users' => $users,
        ));
    }

    /**
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        $roles = Role::all()->toArray();

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

            $role = Role::find(Input::get('role'));

            $user = new User();
            $user->username = Input::get('username');
            $user->email = Input::get('email');
            $user->password = Input::get('password');
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();
            $user->roles()->attach($role->id);
            $name = $user->username;
            Session::flash('info', "user <strong>$name</strong> successfully created");

            return Redirect::route('users');
        }

        return View::make('admin.user.create', array('roles' => $roles,));
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

    public function searching() {

        $searchText = trim(strip_tags(addslashes(Input::get('search'))));
        return $searchText == '' ? Redirect::route('users') : Redirect::route('search', array('searchText' => $searchText));
    }

    public function search($searchText) {

        $searchText = trim(strip_tags(addslashes($searchText)));
        $users = DB::table('users')
            ->where('username','like', '%'.$searchText.'%')
            ->orWhere('email','like', '%'.$searchText.'%')
            ->orderBy('id')
            ->paginate(AdminBaseController::PAGINATE);

        return View::make('admin.user.users', array(
            'users' => $users,
        ));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getList()
    {
        $users = User::orderBy('id')->paginate(AdminBaseController::PAGINATE);

        if ($this->request->query->has('page')){
            return $this->view->make('admin.user.ajax.list', array(
                'users' => $users,
            ));

        }
        return $this->view->make('admin.user.ajax.list', array(
            'users' => $users,
        ));
    }
}
