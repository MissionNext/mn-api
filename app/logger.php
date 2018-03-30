<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

DB::listen(function($sql, $bindings, $time){
    $logFile = storage_path('logs/custom_logs/query-'.date('Y-m-d H').'.txt');
    $monolog = new Logger('log');
    $monolog->pushHandler(new StreamHandler($logFile, Logger::DEBUG, true, 0775), Logger::INFO);
    $monolog->info('-------------------------------------------');
    $monolog->info($sql, compact('bindings', 'time'));
});