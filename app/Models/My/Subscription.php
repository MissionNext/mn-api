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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subscription extends Model
{

    protected $table = 'subscriptions';

    protected $fillable = [
        'comment',
        'app_id',
        'user_id',
        'partnership',
        'price',
        'is_recurrent',
        'start_date',
        'end_date',
        'authorize_id',
        'status',
        'paid'
    ];

    public const STATUS_ACTIVE = 'active',
        STATUS_EXPIRED = 'expired',
        STATUS_GRACE = 'grace',
        STATUS_CLOSED = 'closed',
        STATUS_PAYMENT_FAILED = 'payment_failed';

    public $timestamps = false;

    protected $appends = [
        'statuses',
        'days_left',
        'partnership_levels'
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            /** @var  $user User */
            $user = User::find($model->user_id);
            $user->appsStatuses()->detach($model->app_id);
            $user->appsStatuses()->attach($model->app_id, ['is_active' => true]);
        });
    }

    /**
     * @return array
     */
    public function getDates(): array
    {
        return ['start_date', 'end_date'];
    }

    /**
     * @return BelongsTo
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(Applications::class, 'app_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transactions::class, 'transactions_subscriptions', 'subscription_id', 'transaction_id');
    }
}
