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

class BaseField extends Model
{

    public $timestamps = false;

    protected $roleType = null;

    protected $table = 'fields';

    protected $fillable = [
        'name',
        'symbol_key',
        'default_value',
        'type',
        'meta',
        'note'
        ];

    /**
     * @return BelongsTo
     */
    public function type():BelongsTo
    {

        return $this->belongsTo(FieldType::class, 'type');
    }

    /**
     * @param array $meta
     *
     * @return $this
     */
    public function setMeta(array $meta)
    {
        $this->meta = json_encode($meta);

        return $this;
    }

    /**
     * @return array
     */
    public function getMeta()
    {

        return json_decode($this->meta, true);
    }
}
