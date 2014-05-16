<?php

namespace MissionNext\Repos\CachedData;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\Facade\SecurityContext;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\FolderNotes\FolderNotes;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Repos\RepositoryInterface;

class UserCachedRepository extends AbstractRepository implements RepositoryInterface
{
    protected $modelClassName = UserCachedData::class;

    protected $currentType;

    public function __construct($type)
    {
        if ( ! RouteSecurityFilter::isAllowedRole($type) ){

            throw new SecurityContextException("'$type' role doesn't exists", SecurityContextException::ON_SET_ROLE);
        }
        $this->currentType = $type;
        SecurityContext::getInstance()->getToken()->setRoles([$type]);
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
     * @param $userId
     *
     * @return Builder|static
     */
    public function dataWithNotes($userId = null)
    {
        $folderNotesTable = (new FolderNotes)->getTable();
        /** @var  $queryBuilder Builder */
        $tableName = $this->currentType."_cached_profile";

        return
          $this->getModel()
            ->select("data", $folderNotesTable.'.notes', $folderNotesTable.'.folder')
            ->leftJoin($folderNotesTable,
            function($join) use ($userId, $tableName, $folderNotesTable)
            {
                if (is_null($userId)){
                    $join
                        ->on($tableName.'.id', '=', $folderNotesTable.'.user_id')
                        ->where($folderNotesTable.'.user_type', '=', $this->currentType);
                }else{
                    $join
                         ->on($tableName.'.id', '=', $folderNotesTable.'.user_id')
                         ->where($folderNotesTable.'.for_user_id', '=', $userId)
                         ->where($folderNotesTable.'.user_type', '=', $this->currentType);
                }

            })
            ->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [SecurityContext::getInstance()->getApp()->id]);
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
            ->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [SecurityContext::getInstance()->getApp()->id])
            ->firstOrFail();
    }
}