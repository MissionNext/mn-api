<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

DB::listen(function($sql, $bindings, $time){
    $logFile = storage_path('logs/custom_logs/query-'.date('Y-m-d').'.txt');
    $monolog = new Logger('log');
    $monolog->pushHandler(new StreamHandler($logFile), Logger::INFO);
    $monolog->info('-------------------------------------------');
    $monolog->info($sql, compact('bindings', 'time'));
});