<?php


/**
 * @file 日志打印
 * @author tiger
 * @date 2014-08-12
 * @brief
 **/

/**
 * @example:
 *
 * require_once('Loggers.php');
 *
 * $GLOBALS['LOG'] = array (
 * 'request'            => array(
 * 'intLevel' => 0xFF,
 * 'strLogFile' => 'd:/request.log',
 * ),
 * );
 *
 * $str = 'test log';
 * $log = Loggers::getLogger('request');
 * $log->notice($str);
 * $log->trace($str);
 * $log->fatal($str);
 * $log->warning($str);
 * $log->debug($str);
 **/
class Loggers
{
    /**
     * module => obj log
     * @var array
     */
    protected static $arrLogs = array();
    protected static $strDefaultLogger = '';


    /**
     * Get a class instance of Logger
     * @return FileLog
     */
    public static function getInstance($strLogger = '')
    {
        return self::getLogger($strLogger);
    }

    public static function getLogger($strLogger = '')
    {
        if (self::$arrLogs == null) {
            $intLogId = self::_logId();
            $intStartTime = defined('PROCESS_START_TIME') ? PROCESS_START_TIME : microtime(true) *
                1000;
            $bolDefault = false;
            if (is_array($GLOBALS['LOG'])) {
                foreach ($GLOBALS['LOG'] as $strModule => $arrConf) {
                    if ($bolDefault === false && $arrConf['bolDefault'] === true) {
                        self::$strDefaultLogger = $strModule;
                        $bolDefault = true;
                    }
                    self::$arrLogs[$strModule] = new FileLog($arrConf, $intLogId, $intStartTime);
                }
            }
            if ($bolDefault === false) {
                self::$strDefaultLogger = "default";
            }
        }
        if (strlen($strLogger) <= 0) {
            $strLogger = self::$strDefaultLogger;
        }
        if (isset(self::$arrLogs[$strLogger])) {
            return self::$arrLogs[$strLogger];
        }
        return false;
    }

    /**
     * Get log id
     * @return int
     */
    private static function _logId()
    {
        $arr = gettimeofday();
        return ((($arr['sec'] * 100000 + $arr['usec'] / 10) & 0x7FFFFFFF) | 0x80000000);
    }
}
