<?php


namespace MissionNext\Controllers\Admin\Subscription\Ajax;


use Illuminate\Support\Facades\Response;
use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Helpers\Language;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\Language\LanguageModel;
use MissionNext\Models\User\User;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;

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

        return Response::json($repo->transData(new LanguageModel()));
    }
} 