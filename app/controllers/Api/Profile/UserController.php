<?php
namespace MissionNext\Controllers\Api\Profile;


use Illuminate\Support\Facades\Input;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Observers\UserObserver;
use MissionNext\Models\User\User as UserModel;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;

/**
 * Class UserController
 * @package MissionNext\Controllers\Api\Profile
 */
class UserController extends BaseController
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
        $user = $this->userRepo()->find($id);

        return new RestResponse($this->userRepo()->profileData($user));
    }

    /**
     * @param $id
     * @return RestResponse
     * @throws \MissionNext\Api\Exceptions\ProfileException
     */
    public function update($id)
    {
        /** @var  $user UserModel */
        $user = $this->userRepo()->find($id);
        $user->setObserver(new UserObserver());
        $user->addApp($this->getApp());

        /** @var  $request Req */
        $request = Request::instance();
        $hash = $request->request->all();
        $files = Input::file();

        if (!empty($files)){
            foreach($files as $symbolKey => $file){
                $hash[$symbolKey] = $file;
            }
        }

        if (empty($hash)) {

            throw new ProfileException("No values specified", ProfileException::ON_UPDATE);
        }
        $this->updateUserProfile($user, $hash);


        return new RestResponse( $this->userRepo()->profileStructure($this->fieldRepo()->profileFields($user), $this->securityContext()->role()));
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