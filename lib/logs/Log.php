<?php


/**
 * @file Log.php
 * @author tiger
 * @date 2014-08-12
 * @brief
 *
 **/


/**
 * Abstract log class
 * It's parent of all logging classes
 **/
abstract class Log
{
    /**
     * Log level value
     * @var int
     */
    const LOG_LEVEL_NONE    = 0x00;
    const LOG_LEVEL_FATAL   = 0x01;
    const LOG_LEVEL_WARNING = 0x02;
    const LOG_LEVEL_NOTICE  = 0x04;
    const LOG_LEVEL_TRACE   = 0x08;
    const LOG_LEVEL_DEBUG   = 0x10;
    const LOG_LEVEL_ALL     = 0xFF;

    /**
     * Log level array
     * @var array
     */
    public static $arrLogLevels = array(
        self::LOG_LEVEL_NONE    => 'NONE',
        self::LOG_LEVEL_FATAL   => 'FATAL',
        self::LOG_LEVEL_WARNING => 'WARNING',
        self::LOG_LEVEL_NOTICE  => 'NOTICE',
        self::LOG_LEVEL_TRACE   => 'TRACE',
        self::LOG_LEVEL_DEBUG   => 'DEBUG',
        self::LOG_LEVEL_ALL     => 'ALL',
    );
    /**
     * Log level value
     * @var int
     */
    protected $intLogLevel;
    /**
     * Absolute path of log file
     * @var string
     */
    protected $strLogFile;
    /**
     * Log Id
     */
    protected $intLogId;
    /**
     * start time
     */
    protected $intStartTime;


    /**
     * Write debug log
     *
     * @param string $str Self defined log string
     * @param int $errno errno to be write into log
     * @param array $arrArgs params in k/v format to be write into log
     * @param int $depth depth of the function be packaged
     */
    public function debug($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        return $this->writeLog(Log::LOG_LEVEL_DEBUG, $str, $errno, $arrArgs, $depth + 1);
    }

    /**
     * Write trace log
     *
     * @param string $str Self defined log string
     * @param int $errno errno to be write into log
     * @param array $arrArgs params in k/v format to be write into log
     * @param int $depth depth of the function be packaged
     */
    public function trace($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        return $this->writeLog(Log::LOG_LEVEL_TRACE, $str, $errno, $arrArgs, $depth + 1);
    }

    /**
     * Write notice log
     *
     * @param string $str Self defined log string
     * @param int $errno errno to be write into log
     * @param array $arrArgs params in k/v format to be write into log
     * @param int $depth depth of the function be packaged
     */
    public function notice($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        return $this->writeLog(Log::LOG_LEVEL_NOTICE, $str, $errno, $arrArgs, $depth + 1);
    }

    /**
     * Write warning log
     *
     * @param string $str Self defined log string
     * @param int $errno errno to be write into log
     * @param array $arrArgs params in k/v format to be write into log
     * @param int $depth depth of the function be packaged
     */
    public function warning($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        return $this->writeLog(Log::LOG_LEVEL_WARNING, $str, $errno, $arrArgs, $depth + 1);
    }

    /**
     * Write fatal log
     *
     * @param string $str Self defined log string
     * @param int $errno errno to be write into log
     * @param array $arrArgs params in k/v format to be write into log
     * @param int $depth depth of the function be packaged
     */
    public function fatal($str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        return $this->writeLog(Log::LOG_LEVEL_FATAL, $str, $errno, $arrArgs, $depth + 1);
    }

    /**
     * Write log
     *
     * @param int $intLogLevel log level
     * @param string $str Self defined log string
     * @param int $errno errno to be write into log
     * @param array $arrArgs params in k/v format to be write into log
     * @param int $depth depth of the function be packaged
     */
    protected abstract function writeLog($intLogLevel, $str, $errno = 0, $arrArgs = null, $depth = 0);

    /**
     * Get log string
     *
     * @param int $intLogLevel
     * @param int $intLogId
     * @param string $str
     * @param int $errno
     * @param array $arrArgs
     * @param int $depth
     * @return string
     */
    protected static function getLog($intLogLevel, $intLogId, $intStartTime, $str, $errno = 0, $arrArgs = null, $depth = 0)
    {

        $strLevel = self::$arrLogLevels[$intLogLevel];
        $trace = debug_backtrace();
        if ($depth >= count($trace)) {
            $depth = count($trace) - 1;
        }
        $file = basename($trace[$depth]['file']);
        $line = $trace[$depth]['line'];

        $strArgs = '';
        if (is_array($arrArgs) && count($arrArgs) > 0) {
            foreach ($arrArgs as $key => $value) {
                $strArgs .= $key . "[$value] ";
            }
        }

        $intTimeUsed = microtime(true) * 1000 - $intStartTime;

        $str = sprintf("%s: %s [%s:%d] errno[%d] logId[%u] time_used[%d] uri[%s] refer[%s] intranet_ip[%s] client_ip[%s] %s%s\n",
            $strLevel,
            date('Y-m-d H:i:s:', time()),
            $file,
            $line,
            $errno,
            $intLogId,
            $intTimeUsed,
            isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
            isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
//            $_SERVER['SERVER_ADDR'],
            '',
            '127.0.0.1',
//            Libs::getClientIP(),
            $strArgs,
            $str
        );
        return $str;
    }

    /**
     * @return int
     */
    public function getLogId()
    {
        return $this->intLogId;
    }
}
