<?php


namespace MissionNext\Models\Subscription;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\Application\Application;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\User\User;

class Subscription extends Model implements ModelInterface
{
    const STATUS_ACTIVE = 'active',
          STATUS_EXPIRED = 'expired',
          STATUS_GRACE = 'grace';

    public $timestamps = false;

    protected $appends = ['statuses', 'days_left', 'partnership_levels'];

    public function getDates()
    {
        return ['start_date', 'end_date'];
    }

    /**
     * @return array
     */
    public function getPartnershipLevelsAttribute()
    {

        return Partnership::levels();
    }

    /**
     * @return array
     */
    public function getStatusesAttribute()
    {

        return [static::STATUS_ACTIVE, static::STATUS_EXPIRED, static::STATUS_GRACE];
    }

    /**
     * @return mixed
     */
    public function getDaysLeftAttribute()
    {

        return $this->end_date->diffInDays(Carbon::now());
    }


    /**
     * @return array
     */
    public static function statuses()
    {

        return [static::STATUS_ACTIVE, static::STATUS_EXPIRED, static::STATUS_GRACE];
    }


    protected $table = 'subscriptions';

    protected $fillable =
        ['comment', 'app_id', 'user_id', 'partnership', 'price', 'is_recurrent',
             'start_date', 'end_date', 'authorize_id', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app()
    {

        return $this->belongsTo(Application::class, 'app_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {

        return $this->belongsTo(User::class, 'user_id');
    }
} 