<?php

namespace App\Repos\CachedData;


use Illuminate\Database\Query\Builder;
use App\Modules\Api\Exceptions\SecurityContextException;
use App\Modules\Api\Service\DataTransformers\UserCachedDataStrategy;
use App\Modules\Api\Service\DataTransformers\UserCachedTransformer;
use App\Modules\Api\Facade\SecurityContext;
use App\Modules\Api\Filter\RouteSecurityFilter;
use App\Models\Application\Application;
use App\Models\CacheData\UserCachedData;
use App\Models\CacheData\UserCachedDataTrans;
use App\Models\DataModel\BaseDataModel;
use App\Models\FolderApps\FolderApps;
use App\Models\Language\LanguageModel;
use App\Models\Notes\Notes;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Repos\AbstractRepository;
use App\Repos\RepositoryInterface;

class UserCachedRepository extends AbstractRepository implements UserCachedRepositoryInterface
{


    protected $modelClassName = UserCachedData::class;

    protected $currentType;
    /** @var  Application */
    protected $app;

    public function __construct($type = null)
    {
        if (is_null($type)){
            parent::__construct();
        }else {
            if (!RouteSecurityFilter::isAllowedRole($type)) {

                throw new SecurityContextException("'$type' role doesn't exists", SecurityContextException::ON_SET_ROLE);
            }
            $this->currentType = $type;
            $this->app =  SecurityContext::getInstance()->getApp(); //@TODO  repoContainer not working in matching
            $this->model = UserCachedData::table($type);
        }
    }

    /**
     * @param $type
     *
     * @return UserCachedRepository
     */
    public function dataOfType($type)
    {

        return new self($type);
    }


    /**
     * @return UserCachedData
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @param int $last_login
     *
     * @return int
     */
    public function count($last_login = null)
    {
        if($last_login) {
            $last_login .= '-01-01 00:00:00';

            return $this->getModel()->leftJoin("users", "users.id", "=", $this->currentType . '_cached_profile.id')
                ->where('users.last_login', '>=', $last_login)->count();
        }

        return $this->getModel()->count();
    }

    /**
     * @param int $last_login
     *
     * @return UserCachedTransformer
     */
    public function data($last_login = null, $timelimit = null)
    {

        $queryBuilder =
            $this->getModel()
                ->select("data")
                ->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [SecurityContext::getInstance()->getApp()->id]);

        if ($timelimit) {
            $queryBuilder->where('updated_at', '>=', date('Y-m-d', $timelimit));
        }

        if($last_login) {
            $last_login .= '-01-01 00:00:00';
            $queryBuilder->leftJoin("users", "users.id", "=", $this->currentType . '_cached_profile.id')
                ->where('users.last_login', '>=', $last_login);
        }

        $queryBuilder->orderBy($this->currentType . '_cached_profile.id', 'ASC');

        return
            new UserCachedTransformer($queryBuilder, new UserCachedDataStrategy());
    }

    /**
     * @param LanguageModel $languageModel
     *
     * @return array
     */
    public function transData(LanguageModel $languageModel)
    {
        $role = $this->repoContainer->securityContext()->role();
        /** @var  $transCache UserCachedDataTrans */
        $transCache =  UserCachedDataTrans::table($role)->whereLangId($languageModel->id)
        ->whereId($this->getModel()->id)->get()->first();

        $transData = $transCache ? $transCache->getData() : $this->getModel()->getData();

        if ($role !== BaseDataModel::JOB && !$this->repoContainer->securityContext()->isAdminArea()) {
            $isAppActive = User::findOrFail($this->getModel()->id)->isActiveInApp($this->repoContainer->securityContext()->getApp());
            $subscription = Subscription::whereUserId($this->getModel()->id)
                                         ->whereAppId($this->repoContainer->securityContext()->getApp()->id())
                                         ->where('status', '<>', Subscription::STATUS_CLOSED)
                                         ->first();

            $transData['is_active_app'] = $isAppActive;
            $transData['subscription'] = $subscription;
       }


        return $transData;
    }


    /**
     * @param null $userId
     *
     * @return UserCachedTransformer
     */
    public function dataWithNotes($userId = null)
    {
        $notesTable = (new Notes())->getTable();
        $folderTable = (new FolderApps())->getTable();
        /** @var  $queryBuilder Builder */
        $tableName = $this->currentType."_cached_profile";

        $queryBuilder =
          $this->getModel()
            ->select("data", $notesTable.'.notes', $folderTable.'.folder')
            ->leftJoin($notesTable,
            function($join) use ($userId, $tableName, $notesTable)
            {
                if (is_null($userId)){
                    $join
                        ->on($tableName.'.id', '=', $notesTable.'.user_id')
                        ->where($notesTable.'.user_type', '=', $this->currentType);
                }else{
                    $join
                         ->on($tableName.'.id', '=', $notesTable.'.user_id')
                         ->where($notesTable.'.for_user_id', '=', $userId)
                         ->where($notesTable.'.user_type', '=', $this->currentType);
                }

            })
            ->leftJoin($folderTable,
                function($join) use ($userId, $tableName, $folderTable)
                {
                    if (is_null($userId)){
                        $join
                            ->on($tableName.'.id', '=', $folderTable.'.user_id')
                            ->where($folderTable.'.user_type', '=', $this->currentType)
                            ->where($folderTable.'.app_id', '=', $this->app->id());
                    }else{
                        $join
                            ->on($tableName.'.id', '=', $folderTable.'.user_id')
                            ->where($folderTable.'.for_user_id', '=', $userId)
                            ->where($folderTable.'.user_type', '=', $this->currentType)
                            ->where($folderTable.'.app_id', '=', $this->app->id());

                    }

                }
                );
          //  ->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [SecurityContext::getInstance()->getApp()->id]);

        return
            new UserCachedTransformer($queryBuilder, new UserCachedDataStrategy());
    }

    /**
     * @param $userId
     * @param $last_login
     *
     * @return UserCachedData
     */
    public function mainData($userId)
    {
        return $this->getModel()->select('data')->where("id", "=", $userId)->firstOrFail();
    }

}
