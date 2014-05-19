<?php


namespace MissionNext\Controllers\Api\Profile;

use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Observers\UserObserver;
use MissionNext\Models\User\User as UserModel;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;

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

        return new RestResponse($this->jobRepo()->profileData($this->jobRepo()->find($id)));
    }


    /**
     * @param $id
     * @return RestResponse
     * @throws \MissionNext\Api\Exceptions\ProfileException
     */
    public function update($id)
    {
        $job = $this->jobRepo()->find($id);
        $job->setObserver(new UserObserver());
        $job->addApp($this->getApp());
        /** @var  $request Req */
        $request = Request::instance();
        $hash = $request->request->all();
        if (empty($hash)) {

            throw new ProfileException("No values specified", ProfileException::ON_UPDATE);
        }
        $this->updateUserProfile($job, $hash);

        return new RestResponse( $this->jobRepo()->profileStructure($this->fieldRepo()->profileFields($job), $this->securityContext()->role()));
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