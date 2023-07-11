<?php

namespace App\Models\My;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applications extends Model
{
    use HasFactory;

    protected $table = 'application';

    protected $fillable = [
        'name',
        'abbrev',
        'private_key',
        'public_key',
    ];

}
