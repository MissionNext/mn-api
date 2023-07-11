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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transactions extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'transaction_id',
        'comment',
        'amount'
    ];

    /**
     * @return BelongsToMany
     */
    public function subscriptions():BelongsToMany
    {
        return $this->belongsToMany(Subscription::class, 'transactions_subscriptions', 'transaction_id', 'subscription_id');
    }
}
