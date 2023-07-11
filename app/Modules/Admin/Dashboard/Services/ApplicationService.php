<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 26.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Services;


use App\Modules\Admin\Dashboard\Requests\ApplicationRequestWeb;
use Illuminate\Database\Eloquent\Model;

class ApplicationService
{
    public function saveWeb(ApplicationRequestWeb $request, Model $model) {
        $model->fill($request->only($model->getFillable()));
        $model->save();
        return $model;
    }
}
