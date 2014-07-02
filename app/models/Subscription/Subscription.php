<?php


namespace MissionNext\Models\Subscription;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\Application\Application;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\User\User;

class Subscription extends Model implements ModelInterface
{
    public $timestamps = false;

    protected $table = 'subscriptions';

    protected $fillable = ['app_id', 'user_id', 'partnership', 'price', 'is_recurrent', 'is_active', 'start_date', 'end_date'];

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