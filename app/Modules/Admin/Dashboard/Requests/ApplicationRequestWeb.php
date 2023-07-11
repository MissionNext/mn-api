<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 26.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequestWeb extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>'required|min:3',
            'private_key'=>'required|min:3',
            'public_key'=>'required|min:3'
        ];
    }
}
