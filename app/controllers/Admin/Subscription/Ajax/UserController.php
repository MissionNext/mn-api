<?php


namespace MissionNext\Controllers\Admin\Subscription\Ajax;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Response;
use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Helpers\Language;
use MissionNext\Models\Application\Application;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\Language\LanguageModel;
use MissionNext\Models\User\ExtendedUser;
use MissionNext\Models\User\User;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;
use MissionNext\Repos\User\ProfileRepositoryFactory;
use MissionNext\Repos\User\UserRepository;

class UserController extends AdminBaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $filters = $this->request->query->get('filters');
        $profileFilter = $filters['profile'];
        $appFilter = $filters['app'];
        $roleFilter = $filters['role'];

        if ($appFilter){
            $appFilter = explode('|',$appFilter);
        }

        if ($roleFilter){
            $roleFilter = explode('|',$roleFilter);
        }



        /** @var  $users Paginator */
        $usersQuery =   ExtendedUser::where( function($query) use ($profileFilter){
                 $query->where('username', 'LIKE', '%'.$profileFilter.'%' )
                       ->orWhere('email', 'LIKE', '%'.$profileFilter.'%');
             });

        $usersQuery = $appFilter ?  $usersQuery->leftJoin('user_apps', 'user_apps.user_id','=', 'users.id')
            ->whereIn('user_apps.app_id', $appFilter) : $usersQuery;

        $usersQuery = $roleFilter ?  $usersQuery->leftJoin('user_roles', 'user_roles.user_id','=', 'users.id')
            ->whereIn('user_roles.role_id', $roleFilter) : $usersQuery;

        $totalCount = $usersQuery->get()->count();
        //dd($this->getLogQueries());

        $users =  $usersQuery->orderBy('id')->paginate(static::PAGINATE);


        return Response::json(["users" => $users->toArray(), 'totalUsers' => $totalCount, 'itemsPerPage' => static::PAGINATE ]);
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

        return Response::json(["user" => $repo->getModel()->getData()]);
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
       // dd($isActive, $userId, $appId);
        User::find($userId)->appsStatuses()->updateExistingPivot($appId, ['is_active' => $isActive]);


       // return Response::json(User::find($userId)->appsStatuses()->get());
        return Response::json(["is_active" =>  User::find($userId)->isActiveInApp(Application::find($appId)) ]);
    }
} 