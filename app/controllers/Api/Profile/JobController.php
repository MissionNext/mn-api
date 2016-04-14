<?php


namespace MissionNext\Controllers\Api\Profile;

use Illuminate\Support\Facades\Input;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\CacheData\UserCachedDataTrans;
use MissionNext\Models\Observers\UserObserver;
use MissionNext\Models\User\User as UserModel;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;
use MissionNext\Repos\User\JobRepository;
use MissionNext\Repos\User\JobRepositoryInterface;

/**
 * Class JobController
 * @package MissionNext\Controllers\Api\Profile
 */
class JobController extends BaseController
{

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function show($id)
    {

        /** @var  $cacheData UserCachedRepository */
        $cacheData = $this->repoContainer[UserCachedRepositoryInterface::KEY];
        $cacheData->findOrFail($id);

        return new RestResponse($cacheData->transData($this->getToken()->language()));
    }


    /**
     * @param $id
     *
     * @return RestResponse
     *
     * @throws \MissionNext\Api\Exceptions\ProfileException
     */
    public function update($id)
    {
        $job = $this->jobRepo()->with('organization')->find($id);
        $job->setObserver(new UserObserver());
        $job->addApp($this->getApp());
        /** @var  $request Req */
        $request = Request::instance();
        $hash = $request->request->get('profile');
        $changedFields = $request->request->get('changedData');

        if ($files = Input::file('profile')){
            $this->checkFile($files, $hash);
        }

        if (empty($hash)) {

            throw new ProfileException("No values specified", ProfileException::ON_UPDATE);
        }
        $this->updateUserProfile($job, $hash, $changedFields);

        /** @var  $cacheData UserCachedRepository */
        $cacheData = $this->repoContainer[UserCachedRepositoryInterface::KEY];
        $cacheData->findOrFail($id);

        return new RestResponse( $cacheData->transData($this->getToken()->language()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function destroy($id)
    {
        //
    }

}