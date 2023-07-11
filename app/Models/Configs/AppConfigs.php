<?php


namespace App\Models\Configs;


use Illuminate\Database\Eloquent\Model;
use App\Models\Application\Application;

class AppConfigs extends Model
{
    protected $table = 'app_config';

    public $timestamps = false;

    protected $fillable = [
        'app_id',
        'value',
        'key'
        ];


    public function app()
    {

        return $this->belongsTo(Application::class, 'app_id', 'id');
    }
}
