<?php

define('PATH', dirname(__FILE__));
date_default_timezone_set("Asia/Shanghai");

use Workerman\Worker;
use Workerman\Lib\Timer;

require_once PATH .'/vendor/workerman/workerman/Autoloader.php';
require_once PATH .'/conf/config.php';
require_once PATH . '/lib/Logs.php';
require_once PATH .'/lib/Gateway.php';
require_once PATH .'/lib/SendEmail.php';
require_once PATH .'/lib/Message.php';
require_once PATH .'/lib/GlobaldataClient.php';

//守护进程（或在执行命令使用 -d）
// Worker::$daemonize = true;

Worker::$logFile = PATH .'/logs/workerman.log';
Worker::$pidFile = PATH .'/logs/process.pid';

$worker = new Worker();

//主进程名称
$worker->name = 'process';

//子进程数量
$worker->count = count($taskConf);
$worker->onWorkerStart = function ($worker) {
	
	$curTask = $GLOBALS['taskConf'][$worker->id];

	//子进程名称
	$worker->name = $curTask['class'] .':'. $curTask['method'];
	
	require_once PATH .'/task/'. $curTask['class'] .'.php';
	$instance = new $curTask['class']();
	
	//此时间间隔为该方法执行结束之后开始处理（可能存在超时，无法获取而挂起）
	Timer::add($curTask['time_interval'], array($instance, $curTask['method']), array());
};


Worker::runAll();