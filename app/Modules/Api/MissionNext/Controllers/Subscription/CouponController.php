<?php


namespace App\Modules\Api\MissionNext\Controllers\Subscription;


use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Coupon\Coupon;

/**
 * Class CouponController
 * @package App\Modules\Api\MissionNext\Controllers\Subscription;
 */
class CouponController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function postCode()
    {

        return new RestResponse(Coupon::whereCode($this->request->request->get('code'))->first());
    }
}
