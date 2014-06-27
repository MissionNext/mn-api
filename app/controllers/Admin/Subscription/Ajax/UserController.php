<?php


namespace MissionNext\Controllers\Admin\Subscription\Ajax;


use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Models\User\User;

class UserController extends AdminBaseController
{
    /**
     * @return \Illuminate\View\View
     */
    public function getList()
    {
        $users = User::orderBy('id')->paginate(static::PAGINATE);

        return $this->view->make('admin.user.ajax.list', array(
            'users' => $users,
        ));
    }
} 