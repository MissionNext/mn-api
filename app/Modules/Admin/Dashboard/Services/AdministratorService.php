<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 28.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Services;

use App\Modules\Admin\Dashboard\Requests\AdministratorRequestWeb;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AdministratorService
{
    public function saveWeb(AdministratorRequestWeb $request, Model $model) {
        $model->activated = true;
        $model->username =  $request->username;
        $model->email =  $request->email;
        $model->password =  Hash::make($request->password);
        $model->fill($model->getFillable());
        $model->save();
        return $model;
    }
}
