<?php


class Mysql extends SqlStatement
{

    public function foreignKeyChecksOff()
    {

        return "SET foreign_key_checks = 0";
    }

    public function foreignKeyChecksOn()
    {

        return "SET foreign_key_checks = 1";
    }

    public function truncateTable($tableName){

        return "TRUNCATE $tableName";
    }

} 