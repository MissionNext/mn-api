<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 27.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Services;

use App\Modules\Admin\Dashboard\Requests\CouponsRequestWeb;
use Illuminate\Database\Eloquent\Model;

class CouponsService
{
    public function saveWeb(CouponsRequestWeb $request, Model $model) {
        $model->fill($request->only($model->getFillable()));
        $model->save();
        return $model;
    }
}
