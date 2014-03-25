<?php

class BaseSeeder extends Seeder {

    /**
     * @return \MissionNext\DB\SqlStatement\SqlStatement
     */
    protected  function  getDbStatement()
    {
        return \MissionNext\DB\SqlStatement\Sql::getDbStatement();
    }

}