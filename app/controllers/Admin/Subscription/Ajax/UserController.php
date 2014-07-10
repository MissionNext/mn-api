<?php


namespace MissionNext\Controllers\Admin\Subscription\Ajax;


use Illuminate\Support\Facades\Response;
use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Helpers\Language;
use MissionNext\Models\Application\Application;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\Language\LanguageModel;
use MissionNext\Models\User\User;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;
use MissionNext\Repos\User\ProfileRepositoryFactory;
use MissionNext\Repos\User\UserRepository;

class UserController extends AdminBaseController
{
    /**
     * @return \Illuminate\View\View
     */
    public function getList()
    {
        $users = User::orderBy('id')->paginate(static::PAGINATE);

        return $this->view->make('admin.user.ajax.list', array(
            'users' => $users,
        ));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndex($id)
    {
        /** @var  $repo UserCachedRepository */
        $repo = $this->repoContainer[UserCachedRepositoryInterface::KEY];
        $repo->findOrFail($id);

        return Response::json(["user" => $repo->transData(new LanguageModel())]);
    }

    /**
     * @param $isActive
     * @param $userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus($isActive, $userId)
    {
        $user = User::findOrFail($userId);
        $isActive = $isActive  === 'enable' ? true : false;
        $user->is_active = $isActive;
        $user->status = 0;
        $user->save();

        /** @var  $userRepo UserRepository */
        $userRepo = $this->repoContainer[ProfileRepositoryFactory::KEY]->profileRepository();
        $userRepo->addUserCachedData($user);

        return Response::json(["is_active" => $isActive, "status" => 0 ]);
    }

    /**
     * @param $isActive
     * @param $userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAppStatus($isActive, $userId, $appId)
    {
        $isActive = $isActive  === 'enable' ? true : false;
        User::find($userId)->appsStatuses()->updateExistingPivot($appId, ['is_active' => $isActive]);


        return Response::json(["is_active" =>  User::find($userId)->isActiveInApp(Application::find($appId)) ]);
    }
} 