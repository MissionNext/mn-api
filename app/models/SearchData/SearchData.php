<?php

namespace MissionNext\Models\SearchData;


use Illuminate\Database\Eloquent\Model;

class SearchData extends Model
{
   protected $table = "search_data";

   protected $fillable = [ "user_type", "search_name", "search_type", "user_id", "data" ];
} 