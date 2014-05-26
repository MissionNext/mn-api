<?php
namespace MissionNext\Controllers;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\View;
use MissionNext\Models\Admin\AdminUserModel;
use MissionNext\Models\User\User;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class TestController extends Controller
{
    public function home() {

        $users = User::all();


        $username = AdminUserModel::getAdminUsername();
        $password = AdminUserModel::getAdminPassword();

        return View::make('homepage.homepage', array(
            'users' => $users,
            'u1' => $username,
            'u2' => $password
        ));
    }
}