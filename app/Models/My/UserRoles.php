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

class UserRoles extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'app_id'
    ];

    /**
     * @return hasOne
     */
    public function role(): hasOne
    {
        return $this->hasOne(Roles::class);
    }

    /**
     * @return hasOne
     */
    public function user(): hasOne
    {
        #foreignKey: "users.user_app_id"
        #localKey: "id"
        return $this->hasOne(User::class);
    }
}
