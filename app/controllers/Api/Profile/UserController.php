<?php
namespace MissionNext\Controllers\Api\Profile;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Queue;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\Queue\Master\ProfileUpdateMatching;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\CacheData\UserCachedDataTrans;
use MissionNext\Models\Observers\UserObserver;
use MissionNext\Models\User\User as UserModel;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;
use MissionNext\Models\User\User;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;
use MissionNext\Repos\Field\FieldRepository;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use MissionNext\Repos\User\ProfileRepositoryFactory;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;

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
        /** @var  $cacheData UserCachedRepository */
        $cacheData = $this->repoContainer[UserCachedRepositoryInterface::KEY];
        $cacheData->findOrFail($id);

        return new RestResponse($cacheData->transData($this->getToken()->language()));
    }

    /**
     * @param $id
     * @return RestResponse
     * @throws \MissionNext\Api\Exceptions\ProfileException
     */
    public function update($id)
    {
        /** @var  $user UserModel */
        $user = $this->userRepo()->findOrFail($id);
        $user->setObserver(new UserObserver());
        $user->addApp($this->getApp());

        /** @var  $request Req */
        $request = Request::instance();
        $hash = $request->request->get('profileData');
        $changedFields = $request->request->get('changedData');

        if ($files = Input::file('profileData')){
            $this->checkFile($files, $hash);
        }
        if (empty($hash)) {

            throw new ProfileException("No values specified", ProfileException::ON_UPDATE);
        }
        $this->updateUserProfile($user, $hash, $changedFields);

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
        $user = $this->userRepo()->findOrFail($id);
        $user->setObserver(new UserObserver());
        $user->addApp($this->getApp());

        $request = Request::instance();
        $hash = $request->query->all();

        $fields = $this->fieldRepo()->modelFields()->where('symbol_key', $hash['field_name'])->get();
        $this->fieldRepo()->profileFields($user)->detach($fields[0]->id, true);

        $filename = public_path().'/uploads/'.$user->role().$id.'_'.$hash['field_name'].'.pdf';
        unlink($filename);

        $userRepo = $this->repoContainer[ProfileRepositoryFactory::KEY]->profileRepository();
        $userRepo->addUserCachedData($user);

        return new RestResponse(["status" => "success", "message" => "File deleted successfully."]);
    }
}