<?php


namespace App\Modules\Api\MissionNext\Controllers\User;


use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Admin\AdminUserModel;

/**
 * Class AdminController
 * @package App\Modules\Api\Controllers\User
 */
class AdminController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function getEmails()
    {
        return new RestResponse(AdminUserModel::all()->pluck('email'));
    }
}
