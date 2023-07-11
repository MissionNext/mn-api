<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 29.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Models\My;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Roles extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'role'
    ];
}
