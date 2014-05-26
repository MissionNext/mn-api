<?php
namespace MissionNext\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AdminUserModel extends Model {

    protected $table = 'admin_users';
    protected $guarded = array('id', 'password');

    public static $rules = array(
        'username' => 'required|min:3|max:200',
//        'email' => 'required|email',
        'password' => 'required|min:6'
    );
}