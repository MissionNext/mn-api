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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserApp extends Model
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
    public function application(): hasOne
    {
        return $this->hasOne(Applications::class);
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

