<?php

namespace MissionNext\Repos\CachedData;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\Api\Service\DataTransformers\UserCachedDataStrategy;
use MissionNext\Api\Service\DataTransformers\UserCachedTransformer;
use MissionNext\Facade\SecurityContext;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Application\Application;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\FolderApps\FolderApps;
use MissionNext\Models\Notes\Notes;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Repos\RepositoryInterface;

class UserCachedRepository extends AbstractRepository implements RepositoryInterface
{
    protected $modelClassName = UserCachedData::class;

    protected $currentType;
    /** @var  Application */
    protected $app;

    public function __construct($type)
    {
        if ( ! RouteSecurityFilter::isAllowedRole($type) ){

            throw new SecurityContextException("'$type' role doesn't exists", SecurityContextException::ON_SET_ROLE);
        }
        $this->currentType = $type;
        SecurityContext::getInstance()->getToken()->setRoles([$type]);
        $this->app = SecurityContext::getInstance()->getApp();
        parent::__construct();
    }

    /**
     * @return UserCachedData
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @return int
     */
    public function count()
    {

        return $this->getModel()->count();
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
     *
     * @return UserCachedData
     */
    public function mainData($userId)
    {

        return $this->getModel()
            ->select('data')
            ->where("id", "=", $userId)
          //  ->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [SecurityContext::getInstance()->getApp()->id()])
            ->firstOrFail();
    }
}