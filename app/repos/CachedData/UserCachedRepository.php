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
use MissionNext\Models\CacheData\UserCachedDataTrans;
use MissionNext\Models\FolderApps\FolderApps;
use MissionNext\Models\Language\LanguageModel;
use MissionNext\Models\Notes\Notes;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Repos\RepositoryInterface;

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
     * @return int
     */
    public function count()
    {

        return $this->getModel()->count();
    }

    /**
     * @return UserCachedTransformer
     */
    public function data()
    {

        $queryBuilder =
            $this->getModel()
                ->select("data");
        //  ->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [SecurityContext::getInstance()->getApp()->id]);

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

        return $transCache ? $transCache->getData() : $this->getModel()->getData();
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