<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 04.10.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Models\My;

use Illuminate\Database\Eloquent\Model;
use App\Models\GlobalConfig;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class GlobalSubscription extends Model
{
    protected $table = 'global_subscriptions';

    protected $fillable = [
        'user_id',
        'is_recurrent',
        'authorize_id',
        'start_date',
        'end_date',
        'status'];

    public const STATUS_ACTIVE = 'active',
        STATUS_EXPIRED = 'expired',
        STATUS_GRACE = 'grace',
        STATUS_CLOSED = 'closed';

    public $timestamps = false;

    protected $appends = [
        'statuses',
        'days_left'
    ];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return array
     */
    public function getDates(): array
    {
        return [
            'start_date',
            'end_date'
        ];
    }

    /**
     * @return array
     */
    public function getStatusesAttribute(): array
    {

        return [
            static::STATUS_ACTIVE,
            static::STATUS_EXPIRED,
            static::STATUS_GRACE
        ];
    }

    /**
     * @return mixed
     */
    public function getDaysLeftAttribute()
    {
        return $this->end_date->diffInDays(Carbon::now(), false) * -1;
    }

    /**
     * @return array
     */
    public static function statuses():array
    {

        return [
            static::STATUS_ACTIVE,
            static::STATUS_EXPIRED,
            static::STATUS_GRACE
        ];
    }
}
