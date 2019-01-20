<?php


define('PATH', dirname(__FILE__));
date_default_timezone_set("Asia/Shanghai");
require_once __DIR__ . '/vendor/autoload.php';
require_once PATH . '/conf/config.php';




//Log::warning('1',array(1,1,1,1,33,3,33,));

var_dump(Loggers::getInstance("service")->warning("错误", -1, array(1111,1,1,3,1,1,1,1,)));

//->warning("错误", -1, '1');