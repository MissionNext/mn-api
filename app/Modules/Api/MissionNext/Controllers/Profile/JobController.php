<?php

namespace App\Modules\Api\MissionNext\Controllers\Profile;

use App\Modules\Api\Exceptions\ProfileException;
use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Observers\UserObserver;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;
use App\Repos\CachedData\UserCachedRepository;
use App\Repos\CachedData\UserCachedRepositoryInterface;
use App\Repos\User\ProfileRepositoryFactory;
use Input;
/**
 * Class JobController
 * @package App\Modules\Api\Controllers\Profile
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
     * @throws ProfileException
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
        $job = $this->jobRepo()->findOrFail($id);
        $job->setObserver(new UserObserver());
        $job->addApp($this->getApp());

        $request = Request::instance();
        $hash = $request->query->all();

        $fields = $this->fieldRepo()->modelFields()->where('symbol_key', $hash['field_name'])->get();
        $this->fieldRepo()->profileFields($job)->detach($fields[0]->id, true);

        $filename = app_path().'/storage/uploads/job'.$id.'_'.$hash['field_name'].'.pdf';
        unlink($filename);

        $jobRepo = $this->repoContainer[ProfileRepositoryFactory::KEY]->profileRepository();
        $jobRepo->addUserCachedData($job);

        return new RestResponse(["status" => "success", "message" => "File deleted successfully."]);
    }

}
