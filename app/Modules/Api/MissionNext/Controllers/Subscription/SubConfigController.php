<?php


namespace App\Modules\Api\MissionNext\Controllers\Subscription;


use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Repos\Subscription\SubConfigRepository;
use App\Repos\Subscription\SubConfigRepositoryInterface;

/**
 * Class SubConfigController
 * @package App\Modules\Api\Controllers\Subscription
 */
class SubConfigController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function getIndex()
    {
        /** @var  $repo SubConfigRepository */
        $repo = $this->repoContainer[SubConfigRepositoryInterface::KEY];

        return new RestResponse($repo->allConfigs());
    }
}
