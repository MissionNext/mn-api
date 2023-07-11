<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 28.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Models\My\UserApp;
use App\Models\My\UserRoles as Roles;
use App\Models\Subscription\Subscription;
use App\Models\User\ExtendedUser;
use App\Models\User\User;
use App\Modules\Admin\BaseController;
use App\Modules\Admin\Dashboard\Requests\UsersRequestWeb;
use App\Modules\Admin\Dashboard\Services\UserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\DeclareDeclare;


class UserController extends BaseController
{
    /**
     * UserController constructor.
     */
    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->service = $userService;
    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $this->title = 'Dashboard.Users list';

        $search = trim(strip_tags(addslashes($request->search)));
        $apps_select = $request->apps_select;
        $role_select = $request->role_select;
        $status_select = $request->status_select;
        $sub_status_select = $request->sub_status_select;
        $username = $request->username;
        $created_at = $request->created_at;
        $updated_at = $request->updated_at;
        $active = null;
        $usersIds = [];
        if (!isset($username, $created_at, $updated_at)) {
            $users = User::query()
                ->orderBy('created_at', 'DESC')
                ->paginate(15);
        }

        if (!empty($search)) {
            $users = User::query()
                ->where('username', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate(15);
        }
        if (!empty($username)) {
            $users = User::query()
                ->orderBy('username', $username)
                ->paginate(15)
                ->appends(['username' => $username]);
            if ($username === 'ASC') {
                $username = 'DESC';
            } else {
                $username = 'ASC';
            }
            $active = 'username';
        }
        if (!empty($created_at)) {
            $users = User::query()
                ->orderBy('created_at', $created_at)
                ->paginate(15)
                ->appends(['created_at' => $created_at]);
            if ($created_at === 'ASC') {
                $created_at = 'DESC';
            } else {
                $created_at = 'ASC';
            }
            $active = 'created_at';
        }
        if (!empty($updated_at)) {
            $users = User::query()
                ->orderBy('updated_at', $updated_at)
                ->paginate(15)
                ->appends(['updated_at' => $updated_at]);
            if ($updated_at === 'ASC') {
                $updated_at = 'DESC';
            } else {
                $updated_at = 'ASC';
            }
            $active = 'updated_at';
        }

        if (!empty($apps_select)) {
            $userApps = UserApp::query()
                ->where('app_id', $apps_select)
                ->orderBy('app_id', 'DESC')->get();

            foreach ($userApps as $userApp) {
                $usersIds[$userApp->user_id] = $userApp->user_id;
            }
            $apps_select = array_combine($apps_select, $apps_select);
        }

        if (!empty($role_select)) {
            $userApps = Roles::query()
                ->where('role_id', $role_select)
                ->orderBy('role_id', 'DESC')->get();
            $usersIds = [];
            foreach ($userApps as $userApp) {
                $usersIds[$userApp->user_id] = $userApp->user_id;
            }
            $role_select = array_combine($role_select, $role_select);
        }

        if (!empty($sub_status_select)) {
            $userApps = Subscription::query()
                ->where('status', $sub_status_select)->get();
            $usersIds = [];
            foreach ($userApps as $userApp) {
                $usersIds[$userApp->user_id] = $userApp->user_id;
            }
            $sub_status_select = array_combine($sub_status_select, $sub_status_select);
        }

        if (!empty($usersIds)) {
            $users = User::query()
                ->whereIn('id', array_values($usersIds))
                ->orderBy('created_at', 'DESC')
                ->paginate(15);
        }

        if (!empty($status_select)) {
            $customQuery = null;
            if (in_array(1, $status_select)) {
                $customQuery = User::query()->where(function ($sq) {
                    $sq->where('users.status', '=', 1)
                        ->where('users.is_active', '=', false);

                });
            }
            if (in_array(2, $status_select,)) {
                if (isset($customQuery)) {
                    $customQuery = $customQuery->orWhere(function ($sq) {
                        $sq->orWhere('users.status', '=', 0)
                            ->where('users.is_active', '=', true);
                    });
                } else {
                    $customQuery = User::query()->where(function ($sq) {
                        $sq->where('users.status', '=', 0)
                            ->where('users.is_active', '=', true);
                    });
                }
            }
            if (in_array(3, $status_select)) {
                if (isset($customQuery)) {
                    $customQuery = $customQuery->orWhere(function ($sq) {
                        $sq->orWhere('users.status', '=', 0)
                            ->where('users.is_active', '=', false);
                    });
                } else {
                    $customQuery = User::query()->where(function ($sq) {
                        $sq->where('users.status', '=', 0)
                            ->where('users.is_active', '=', false);
                    });
                }
            }
            $users = $customQuery->paginate(15);
            $status_select = array_combine($status_select, $status_select);
        }


        $json['pagination']['total'] = $users->total();
        $json['pagination']['per_page'] = $users->perPage();
        $json['pagination']['on_first_page'] = $users->onFirstPage();
        $json['pagination']['current_page'] = $users->currentPage();
        $json['pagination']['last_page'] = $users->lastPage();
        $json['pagination']['first_page_url'] = $users->url(1);
        $json['pagination']['last_page_url'] = $users->url($users->lastPage());
        $json['pagination']['next_page_url'] = $users->nextPageUrl();
        $json['pagination']['prev_page_url'] = $users->previousPageUrl();
        $json['pagination']['range_page_url'] = $users->getUrlRange($users->currentPage() - 1, $users->currentPage() + 1);

        $this->content = view('Admin::Users.users')->with([
            'title' => $this->title,
            'users' => $users,
            'username' => $username ?? 'ASC',
            'created_at' => $created_at ?? 'ASC',
            'updated_at' => $updated_at ?? 'ASC',
            'active' => $active,
            'pagination' => $json['pagination'],
            'applications' => $this->sidebar,
            'roles' => Roles::all(),
            'search' => $search ?? null,
            'apps_select' => $apps_select ?? null,
            'role_select' => $role_select ?? null,
            'status_select' => $status_select ?? null,
            'sub_status_select' => $sub_status_select ?? null,
        ])->render();
        return $this->renderOutput();
    }


    /**
     * @param User $user
     * @return Application|Factory|View
     */
    public function view(User $user)
    {
        $this->title = 'Dashboard. User view';
        $role = $user->role();
        $cached_profile = DB::table($role . '_cached_profile')->where(['id' => $user->id])->get()->first();
        $data = json_decode($cached_profile->data, false);
        $this->content = view('Admin::Users.view')->with([
            'title' => $this->title,
            'data' => $data
        ])->render();
        return $this->renderOutput();
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $this->title = 'Dashboard. User new language';
        $this->content = view('Admin::Users.create')->with([
            'title' => $this->title
        ])->render();
        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UsersRequestWeb $request
     * @return Response
     */
    public function store(UsersRequestWeb $request)
    {
        $model = $this->service->saveWeb($request, new User());
        $name = $model->username;
        return \Redirect::route('dashboards.users.index')->with([
            'message' => "Success! User {$name} successfully created"
        ]);
    }

    /**
     * @param User $user
     * @return Application|Factory|View
     */
    public function edit(User $user)
    {
        $this->title = 'Dashboard. Editing user';
        $role = $user->role();
        $cached_profile = DB::table($role . '_cached_profile')->where(['id' => $user->id])->get()->first();
        $data = json_decode($cached_profile->data, false);
        if ($user->status === 0 && $user->is_active === true) {
            $status = [
                'name' => 'ACCESS GRANTED',
                'value' => 1
            ];
        } elseif ($user->status === 0 && $user->is_active === false) {
            $status = [
                'name' => 'DENY GRANTED',
                'value' => 2
            ];
        } else {
            $status = [
                'name' => 'PENDING APPROVAL',
                'value' => 0
            ];
        }
      //  dd($data);
        $this->content = view('Admin::Users.edit')->
        with([
            'title' => $this->title,
            'item' => $user,
            'status' => $status,
            'data' => $data
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UsersRequestWeb $request
     * @param User $user
     * @return Response
     */
    public function update(UsersRequestWeb $request, User $user)
    {
        $grandButton = $request->grandButton;
        $denyButton = $request->denyButton;
      //  dd($request->all());
        $model = $this->service->saveWeb($request, $user);
        $name = $model->username;
        return \Redirect::route('dashboards.users.index')->with([
            'message' => "Success! User {$name} successfully updated"
        ]);
    }

    /**
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        $name = $user->username;
        $user->delete();
        return \Redirect::route('dashboards.languages.index')->with([
            'alert' => "Success! User {$name} successfully deleted"
        ]);
    }

}
