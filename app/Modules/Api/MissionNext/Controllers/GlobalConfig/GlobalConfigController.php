<?php


namespace App\Modules\Api\MissionNext\Controllers\GlobalConfig;


use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Configs\GlobalConfig;

/**
 * Class GlobalConfigController
 * @package App\Modules\Api\MissionNext\Controllers\GlobalConfig;
 */
class GlobalConfigController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function getIndex()
    {

        return new RestResponse(GlobalConfig::all());
    }

}
