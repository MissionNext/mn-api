<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 27.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Services;


use App\Modules\Admin\Dashboard\Requests\LanguagesRequestWeb;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Language as LanguageHelper;

class LanguagesService
{
    public function saveWeb(LanguagesRequestWeb $request, Model $model) {
        $model->name = LanguageHelper::$codes[$request->key];
        $model->fill($request->only($model->getFillable()));
        $model->save();
        return $model;
    }
}
