<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include __DIR__ . '/vendor/autoload.php';

$logger = new Monolog\Logger('test');
$c = new ProG\Logger\Console($logger);

$c->startMemory('Some memory based test');
$c->start('Some timed event');
$c->stop('Some timed event');

$c->stop('script_execution');
$c->stopMemory('Some memory based test');

echo (new ProG\Logger\Profiler\PrettyProfiler($c))->render();

