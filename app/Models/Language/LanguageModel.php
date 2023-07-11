<?php
namespace App\Models\Language;

use Illuminate\Database\Eloquent\Model;

class LanguageModel extends Model {

    protected $fillable = [
        'key',
        'name'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'languages';

    public $timestamps = false;

}
