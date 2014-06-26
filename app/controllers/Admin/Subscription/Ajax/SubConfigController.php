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
                        [ "price" => 0, "level" =>'', "term" => 1  ],

                    ],

            ],
          [
            "role" => [ "key" => BaseDataModel::ORGANIZATION, "label" => "Receiving Organization"],
            "partnership" =>
            [
              [ "price" => 100, "level" => Partnership::LIMITED, "term" => 1  ],
              [ "price" => 200, "level" =>  Partnership::BASIC, "term" => 1  ],
              [ "price" => 300, "level" => Partnership::PLUS, "term" => 1  ],
            ]

          ],

          [
                "role" => [ "key" => BaseDataModel::AGENCY, "label" => "Agency"],
                "partnership" =>
                    [
                        [ "price" => 200, "level" =>'', "term" => 1  ],

                    ]

          ],
        ];

        return Response::json([ "config" => $config ]);
    }

} 