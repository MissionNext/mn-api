<?php
namespace MissionNext\DB\SqlStatement;

use Illuminate\Support\Facades\DB;

class Sql {
    /**
     * @var SqlStatement
     */
    static protected $dbStatement;

    /**
     * @return SqlStatement
     */
    public static  function  getDbStatement()
    {
        if (static::$dbStatement instanceof SqlStatement){

            return static::$dbStatement;
        }

        switch (DB::getDefaultConnection()) {
            case "mysql":
                static::$dbStatement = new Mysql();
                break;
            case "pgsql":
                static::$dbStatement = new Postgre();
                break;
            default:
                static::$dbStatement = new Mysql();
        }

        return static::$dbStatement;
    }

}