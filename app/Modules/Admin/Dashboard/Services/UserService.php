<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 28.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Services;


use App\Modules\Admin\Dashboard\Requests\UsersRequestWeb;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    public function saveWeb(UsersRequestWeb $request, Model $model) {
        $model->fill($request->only($model->getFillable()));
        $model->save();
        return $model;
    }
}
