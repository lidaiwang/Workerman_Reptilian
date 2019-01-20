<?php

define('PROCESS_START_TIME', microtime(true) * 1000);
define('PUBLIC_PATH', '');
define('PUBLIC_CONF_PATH', PUBLIC_PATH . 'config/');
define('LOG_PATH', PATH . "/logs/");
require PATH . '/lib/Public.php';
require PATH . '/lib/logs/Log.php';
require PATH . '/lib/logs/FileLog.php';
require PATH . '/lib/logs/Loggers.php';

require PATH . '/lib/SendEmail.php';


const MASTER_KEY = 'masters';
const SLAVES_KEY = 'slaves';

const ENCODING = 'gzip,deflate,sdch';
const AGENTARR = array(
    'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36',
    'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36',

    'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:32.0) Gecko/20100101 Firefox/45.0',
    'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:30.0) Gecko/20100101 Firefox/46.0',
    'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/42.0',
    'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/43.0',

    'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
    'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36 LBBROWSER'
);

