<?php


namespace MissionNext\Models\Configs;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\ModelInterface;

class GlobalConfig extends Model implements ModelInterface
{
    const SUBSCRIPTION_DISCOUNT = 'subscriptionDiscount';

    protected $table = 'global_config';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];

} 