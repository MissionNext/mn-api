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

class GlobalConfig extends Model
{
    const SUBSCRIPTION_DISCOUNT = 'subscriptionDiscount',
        CON_FEE = 'conFee',
        GRACE_PERIOD = 'gracePeriod';

    protected $table = 'global_config';

    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    /**
     * @return int
     */
    public function gracePeriod()
    {
        $config = static::whereKey(static::GRACE_PERIOD)->first();

        return $config ? intval($config->value) : 10;
    }

    /**
     * @return int
     */
    public function subscriptionDiscount()
    {
        $config = static::whereKey(static::SUBSCRIPTION_DISCOUNT)->first();

        return $config ? intval($config->value) : 0;
    }

    /**
     * @return int
     */
    public function conFee()
    {
        $config = static::whereKey(static::CON_FEE)->first();

        return $config ? intval($config->value) : 0;
    }

}
