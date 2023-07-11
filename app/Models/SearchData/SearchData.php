<?php

namespace App\Models\SearchData;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelInterface;

class SearchData extends Model implements ModelInterface
{
   protected $table = "search_data";

   protected $fillable = [ "user_type", "search_name", "search_type", "user_id", "data", "app_id" ];

    public function scopeFindData($query, $searchType, $userType, $userId)
    {

        return
            $query
            ->where("search_data.search_type","=",$searchType)
            ->where("search_data.user_type", "=", $userType)
            ->where("app_id", "=", SecurityContext::getInstance()->getApp()->id())
            ->where("search_data.user_id", "=", $userId);
    }
}
