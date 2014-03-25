<?php


class Postgre extends SqlStatement
{

    public function foreignKeyChecksOff()
    {

         return "SET session_replication_role = replica";
    }

    public function foreignKeyChecksOn()
    {

        return "SET session_replication_role = DEFAULT";
    }

    public function truncateTable($tableName){

        return "TRUNCATE $tableName RESTART IDENTITY CASCADE";
    }

} 