<?php
use Workerman\Worker;

define('PATH', dirname(__FILE__));
date_default_timezone_set("Asia/Shanghai");
require_once __DIR__ . '/vendor/autoload.php';
require_once PATH .'/conf/config.php';
//任务处理器
require_once __DIR__ . '/business/checkMarketTrading.php';

Worker::runAll();