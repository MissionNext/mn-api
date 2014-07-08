<?php


namespace MissionNext\Models\Subscription;


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