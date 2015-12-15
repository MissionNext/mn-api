<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\Role\Role;
use MissionNext\Models\User\User;
use MissionNext\Models\Application\Application;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;

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
     * @return \Illuminate\View\View
     */
    public function profile($userId) {
        /** @var  $repo UserCachedRepository */
        $repo = $this->repoContainer[UserCachedRepositoryInterface::KEY];
        $user = $repo->findOrFail($userId)->getData();

        $field_keys = array_keys($user['profileData']);

        $field_keys = array_map(function($item){
            return "'{$item}'";
        }, $field_keys);

        $selected_models_id = DB::select("SELECT id FROM app_data_model WHERE type='".$user["role"]."'");

        $fields_str = implode(',', $field_keys);
        $whereIn = "WHERE symbol_key IN ({$fields_str})";

        $query = "SELECT symbol_key, name FROM form_models {$whereIn} and data_model_id = ".$selected_models_id[0]->id." ORDER BY display_order ASC";
        $ordererFields = DB::select($query);
        $sortedKeys = $fieldLabels = [];
        foreach ($ordererFields as $ordererField) {
            $sortedKeys[] = $ordererField->symbol_key;
            $fieldLabels[$ordererField->symbol_key] = $ordererField->name;
        }
        $sortedKeys = array_unique($sortedKeys);

        return $this->view->make('admin.user.profile', array(

            'user'          => $repo->findOrFail($userId)->getData(),
            'sortedKeys'    => $sortedKeys,
            'fieldLabels'   => $fieldLabels
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
        $user = User::find($id);

        if (is_null($user)) {
            Session::flash('warning', "User with ID $id not found");

            return Redirect::route('users');
        }

        $name = $user->username;
        $user->delete($id);
        Session::flash('info', "User <strong>$name</strong> successfully deleted.");

        return Redirect::route('users');
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

    public function updateProfileCache()
    {
        Artisan::call('profile:update-cache', array());

        return Response::json(['status' => 'success', 'data' => 'Profile cahce update successful']);
    }
}
