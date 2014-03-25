<?php
namespace MissionNext\DB\SqlStatement;

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

    public function truncateTable($tableName)
    {

        return "TRUNCATE $tableName RESTART IDENTITY CASCADE";
    }

    public function groupConcat($fieldName, $alias = null)
    {
        $alias = $alias ? " as $alias" : '';

        return "string_agg($fieldName, ','  ORDER BY $fieldName) $alias";
    }

} 