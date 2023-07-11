<?php
namespace App\Modules\Api\MissionNext\Controllers\Profile;


use Illuminate\Support\Facades\DB;
use App\Modules\Api\Exceptions\ProfileException;
use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Observers\UserObserver;
use App\Models\User\User as UserModel;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;
use App\Models\User\User;
use App\Repos\CachedData\UserCachedRepository;
use App\Repos\CachedData\UserCachedRepositoryInterface;
use App\Repos\User\ProfileRepositoryFactory;
use Input;
/**
 * Class UserController
 * @package App\Modules\Api\Controllers\Profile
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
     * @throws ProfileException
     */
    public function update($id)
    {
        /** @var  $request Req */
        $request = Request::instance();
        $hash = $request->request->get('profile');
        $changedFields = $request->request->get('changedData');
        $saveLater = $request->request->get('saveLater');

        /** @var  $user UserModel */
        $user = $this->userRepo()->findOrFail($id);
        $user->setObserver(new UserObserver());
        if (empty($saveLater)) {
            $user->addApp($this->getApp());
        }

        if ($files = Input::file('profile')){
            $this->checkFile($files, $hash);
        }

        if (empty($hash)) {

            throw new ProfileException("No values specified", ProfileException::ON_UPDATE);
        }
        $this->updateUserProfile($user, $hash, $changedFields, $saveLater);

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

        $filename = app_path().'/storage/uploads/'.$user->role().$id.'_'.$hash['field_name'].'.pdf';
        unlink($filename);

        $userRepo = $this->repoContainer[ProfileRepositoryFactory::KEY]->profileRepository();
        $userRepo->addUserCachedData($user);

        return new RestResponse(["status" => "success", "message" => "File deleted successfully."]);
    }

    public function checkCompletedProfile($user_id) {
        $row = DB::table('user_profile_completed')
            ->where('user_id', $user_id)
            ->where('app_id', $this->getApp()->id())->first();

        if ($row) {
            return new RestResponse(['profile_completed' => true]);
        }

        return new RestResponse(['profile_completed' => false]);
    }

    public function deleteCompletedProfilesChecks($role) {
        DB::table('user_profile_completed')
            ->where('app_id', $this->getApp()->id())
            ->where('role', $role)->delete();

        return new RestResponse(['deleted' => true]);
    }
}
