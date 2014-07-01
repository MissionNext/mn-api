<?php

namespace MissionNext\Models\Subscription;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;

class SubConfig extends Model
{
    public $timestamps = false;

    protected $table = "subscription_configs";

    protected $fillable = ['app_id', 'role', 'partnership', 'price_year', 'price_month'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app()
    {

        return $this->belongsTo(Application::class, 'app_id');
    }

    /**
     * @return array
     */
    public static function defConfig(){

        return [

            [
                "role" => [ "key" => BaseDataModel::ORGANIZATION, "label" => "Receiving Organization"],
                "partnership" =>
                    [
                        [ "price_month" => 0, "level" => Partnership::LIMITED,  "price_year" => 0   ],
                        [ "price_month" => 0, "level" =>  Partnership::BASIC,  "price_year" => 0   ],
                        [ "price_month" => 0, "level" => Partnership::PLUS,  "price_year" => 0   ],
                    ]

            ],

            [
                "role" => [ "key" => BaseDataModel::AGENCY, "label" => "Agency"],
                "partnership" =>
                    [
                        [ "price_month" => 0, "level" =>'',  "price_year" => 0   ],

                    ]

            ],
            [
                "role" => [ "key" => BaseDataModel::CANDIDATE, "label" => "Candidate"],
                "partnership" =>
                    [
                        [ "price_month" => 0, "level" =>'',  "price_year" => 0  ],

                    ],

            ],
        ];
    }
} 