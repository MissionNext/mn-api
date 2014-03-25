<?php

class BaseSeeder extends Seeder {
    /**
     * @var SqlStatement
     */
    protected  $dbStatement;

    /**
     * @return SqlStatement
     */
    protected  function  getDbStatement()
    {
        switch (DB::getDefaultConnection()) {
            case "mysql":
                $this->dbStatement = new Mysql();
                break;
            case "pgsql":
                $this->dbStatement = new Postgre();
                break;
            default:
                $this->dbStatement = new Mysql();
        }

        return $this->dbStatement;
    }

}