<?php

namespace App\Models\My;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'key',
        'name'
    ];

}
