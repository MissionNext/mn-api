<?php
namespace MissionNext\DB\SqlStatement;

use Illuminate\Support\Facades\DB;
use MissionNext\DB\SqlStatement\RepositoryInterface\IViewFieldRepository;
use MissionNext\Repos\AbstractRepository;

class Sql {
    /**
     * @var SqlStatement
     */
    static protected $dbStatement;

    /**
     * @param AbstractRepository $repo
     *
     * @return SqlStatement|IViewFieldRepository
     */
    public static  function  getDbStatement(AbstractRepository $repo = null)
    {
        if (static::$dbStatement instanceof SqlStatement){

            return static::$dbStatement;
        }

        $baseName = __NAMESPACE__.'\\'.class_basename($repo);

        $class = $baseName."Mysql";

        switch (DB::getDefaultConnection()) {
            case "mysql":
                $class = $baseName."Mysql";
                static::$dbStatement = new $class;
                break;
            case "pgsql":
                $class = $baseName."Postgre";
                static::$dbStatement = new $class;
                break;
            default:
                static::$dbStatement = $class;
        }

        return static::$dbStatement;
    }

}