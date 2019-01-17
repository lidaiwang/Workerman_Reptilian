<?php
/**
 * @filesource 第三方库配置文件及日志配置文件
 * @author tiger
 * @date 2014-08-12
 * @brief
 */


//define('PROCESS_START_TIME', microtime(true) * 1000);
//$HOME_PATH = dirname(dirname(__FILE__));
//require_once $HOME_PATH . "/conf/conf.php";

/**
 * Register user defined class into phplib's autoloader
 * @param string $className Name of user defined class
 * @param string $classPath File path of user defined class
 */
function RegisterMyClassName($className, $classPath)
{
    global $arrPublicClassName;
    $arrPublicClassName[$className] = $classPath;
}

/**
 * Register User defined classes into phplib's autoloader
 * @param array $classes Class infos, use format: array(classname => class file path, ...)
 */
function RegisterMyClasses(array $classes)
{
    global $arrPublicClassName;
    $arrPublicClassName = array_merge($arrPublicClassName, $classes);
}

function PublicLibAutoLoader($className)
{
    global $arrPublicClassName;
    if (array_key_exists($className, $arrPublicClassName)) {
        require_once($arrPublicClassName[$className]);
    }
}

//define('PUBLIC_PATH', '');
//define('PUBLIC_CONF_PATH', PUBLIC_PATH . 'config/');
//define('LOG_PATH', HOME_PATH . "/logs/");

$GLOBALS['arrPublicClassName'] = array(
    'Loggers'              => PUBLIC_PATH . 'logs/Loggers.php',
    'Log'                  => PUBLIC_PATH . 'logs/Log.php',
    'FileLog'              => PUBLIC_PATH . 'logs/FileLog.php',
);

$GLOBALS['LOG'] = array(
    'default'       => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'default.log',
        'bolDefault' => true
    ),
    'run'           => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'run.log',
        'bolDefault' => true
    ),
    'service'       => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'service.log',
        'bolDefault' => true
    ),
    'wxlog'         => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'wx.log',
        'bolDefault' => true
    ),
    'wx_login'      => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'wx_login.log',
        'bolDefault' => true
    ),
    'cm'            => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'command.log',
        'bolDefault' => true
    ),
    'gm'            => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'gearman.log',
        'bolDefault' => true
    ),
    'rabbitmq_task' => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'rabbitmq_task.log',
        'bolDefault' => true
    ),
    'event'         => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'event.log',
        'bolDefault' => true
    ),
    'cache'         => array(
        'intLevel'   => 0xFF,
        'strLogFile' => LOG_PATH . 'cache.log',
        'bolDefault' => true
    )
);


