<?php


/**
 * @file FileLog.class.php
 * @author tiger
 * @date 2014-08-12
 * @brief
 *
 **/

/**
 * @example:
 * <?php
 * require_once('FileLog.php');
 *
 * $arrConf = array(
 * 'strLogFile' => '/tmp/test.log',
 * 'intLevel' => 0xFF,
 * );
 * $logger = new FileLog($arrConf);
 * $str = 'test log';
 * $logger->notice($str);
 * $logger->trace($str);
 * $logger->fatal($str);
 * $logger->warning($str);
 * $logger->debug($str);
 **/
class FileLog extends Log
{
    /**
     * Constructor
     * @param array $arrLogConfig
     * {
     *         'strLogFile' => '',
     *         'intLevel' => 0xFF,
     * }
     */
    public function __construct($arrLogConfig, $intLogId, $intStartTime)
    {
        $this->intLevel = intval($arrLogConfig['intLevel']);
        $this->strLogFile = $arrLogConfig['strLogFile'];
        $this->intLogId = $intLogId;
        $this->intStartTime = $intStartTime;
    }

    /**
     * Do file logging
     * @see Log::writeLog()
     */
    public function writeLog($intLogLevel, $str, $errno = 0, $arrArgs = null, $depth = 0)
    {
        if ($intLogLevel > $this->intLevel || !isset(self::$arrLogLevels[$intLogLevel])) {
            return;
        }
        $strLogFile = $this->strLogFile;
        if (($intLogLevel & self::LOG_LEVEL_WARNING) || ($intLogLevel & self::LOG_LEVEL_FATAL)) {
            $strLogFile .= '.wf';
        }

        $log = self::getLog($intLogLevel, $this->intLogId, $this->intStartTime, $str, $errno, $arrArgs, $depth + 1);
        file_put_contents($strLogFile, "\r\n", FILE_APPEND);
        return file_put_contents($strLogFile, $log, FILE_APPEND);
    }
}

