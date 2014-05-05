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
     * @param $userId
     *
     * @return Builder|static
     */
    public function dataWithNotes($userId)
    {
        $folderNotesTable = (new FolderNotes)->getTable();
        /** @var  $queryBuilder Builder */
        $queryBuilder = DB::table($this->currentType."_cached_profile AS jc");

        return
          $queryBuilder
            ->select(DB::raw("jc.data, fn.notes, fn.folder"))
            ->leftJoin(DB::raw($folderNotesTable." as fn"),
            function($join) use ($userId)
            {
                $join->on('jc.id', '=', 'fn.user_id')
                    ->where('fn.for_user_id', '=', $userId)
                    ->where('fn.user_type', '=', $this->currentType);
            });
    }
}