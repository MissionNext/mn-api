<?php

namespace MissionNext\Models\Folder;


use MissionNext\Models\ModelInterface;
use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\Observers\FolderObserver;

class Folder extends Model implements ModelInterface
{
    protected $table = "folders";

    protected $fillable = ["title", "role"];

    protected static function boot()
    {
        parent::boot();
        parent::observe(new FolderObserver);
    }
} 