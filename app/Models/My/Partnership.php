<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 26.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Models\My;

class Partnership
{
    public const LIMITED = 'limited',
        BASIC = 'basic',
        PLUS = 'plus';

    public const PERIOD_YEAR = 'year',
        PERIOD_MONTH = 'month';

    /**
     * @return array
     */
    public static function levels(): array
    {
        return [static::LIMITED, static::BASIC, static::PLUS];
    }
}
