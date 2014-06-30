<?php

namespace MissionNext\Controllers\Admin\Subscription\Ajax;


use Illuminate\Support\Facades\Response;
use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Subscription\Partnership;

class SubConfigController extends AdminBaseController
{
    const ROUTE_PREFIX = 'ajax.sub.config';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndex()
    {

        $config = [
            [
                "role" => [ "key" => BaseDataModel::CANDIDATE, "label" => "Candidate"],
                "partnership" =>
                    [
                        [ "price_month" => 0, "level" =>'',  "price_year" => 0  ],

                    ],

            ],
          [
            "role" => [ "key" => BaseDataModel::ORGANIZATION, "label" => "Receiving Organization"],
            "partnership" =>
            [
              [ "price_month" => 100, "level" => Partnership::LIMITED,  "price_year" => 160   ],
              [ "price_month" => 200, "level" =>  Partnership::BASIC,  "price_year" => 440   ],
              [ "price_month" => 300, "level" => Partnership::PLUS,  "price_year" => 550   ],
            ]

          ],

          [
                "role" => [ "key" => BaseDataModel::AGENCY, "label" => "Agency"],
                "partnership" =>
                    [
                        [ "price_month" => 200, "level" =>'',  "price_year" => 550   ],

                    ]

          ],
        ];

        return Response::json([ "config" => $config ]);
    }

} 