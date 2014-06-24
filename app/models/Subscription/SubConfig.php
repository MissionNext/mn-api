<?php

namespace MissionNext\Models\Subscription;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\Application\Application;

class SubConfig extends Model
{
    public $timestamps = false;

    protected $table = "subscription_configs";

    protected $fillable = ['app_id', 'role', 'partnership', 'period', 'cost'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app()
    {

        return $this->belongsTo(Application::class, 'app_id');
    }
} 