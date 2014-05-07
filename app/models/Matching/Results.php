<?php


namespace MissionNext\Models\Matching;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\ModelInterface;

class Results extends Model implements ModelInterface
{
    protected $table = "matching_results";

    protected $fillable = ["user_type", "user_id", "for_user_id", "data"];
} 