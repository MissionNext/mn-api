<?php

abstract class SqlStatement {

   abstract  public function foreignKeyChecksOff();

   abstract  public function foreignKeyChecksOn();

   abstract public function truncateTable($tableName);

} 