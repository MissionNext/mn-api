<?php


namespace MissionNext\Models\Configs;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\ModelInterface;

class GlobalConfig extends Model implements ModelInterface
{
    const SUBSCRIPTION_DISCOUNT = 'subscriptionDiscount',
          CON_FEE = 'conFee';

    protected $table = 'global_config';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];

} 