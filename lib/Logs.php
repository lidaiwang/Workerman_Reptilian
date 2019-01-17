<?php
/*
 * 日志类，采用文件日志
 *
 */
class Logs {
    /**
     * 日志记录位置
     * @param $method		//调用的方法
     * @return
     * @author xym Created at 2014-4-24
     */
    static private function _logpath($method){
        $path = PATH . '/logs/';
        self::_mkdir($path);
        $path .= $method .'_'. date('Ymd') .'.txt';
        return $path;
    }

    /**
     * 记录普通信息
     * @param string $message
     * @param array	 $data
     * @return
     */
    static public function info($message, $data = array()){
        self::log_result(self::_logpath(__FUNCTION__), $message, $data);
    }

    /**
     * 记录警告信息
     * @param string $message
     * @param array	 $data
     * @return
     */
    static public function warning($message, $data = array()){
        self::log_result(self::_logpath(__FUNCTION__), $message, $data);
    }

    /**
     * 记录错误/严重/紧急信息
     * @param string $message
     * @param array	 $data
     * @return
     */
    static public function error($message, $data = array()){
        self::log_result(self::_logpath(__FUNCTION__), $message, $data);
    }

    static private function _display(){
        $trace = debug_backtrace();
        $count = count($trace);
        if ($count <= 3) return '';
        $level = 3;
        $str = "Start trace:\n";
        for ($i = $count - 1; $i >= $level; $i--) {
            $str .= "\t";
            !empty($trace[$i]['class']) && $str .= $trace[$i]['class'] .'::';
            !empty($trace[$i]['function']) && $str .= $trace[$i]['function'];
            !empty($trace[$i]['file']) && $str .= '('. $trace[$i]['file'];
            !empty($trace[$i]['line']) && $str .= ' in line '. $trace[$i]['line'] .')';

            $str .= "\n";
// 				!empty($trace[$i]['args']) && $str .= "\targs:\n\t\t". json_encode($trace[$i]['args']) ."\n";
        }
        $str .= "End trace\n";
        return $str;
    }

    /**
     * 写日志
     * @param $logfile		//日志文件
     * @param $message		//事件描述
     * @param $data			//记录数据
     * @param array | string $data	//一维数组
     */
    static public function log_result($logfile, $message, $data) {
        $trace = self::_display();
        $fp = fopen($logfile, "a");//写入方式打开，不存在文件则尝试创建
        flock($fp, LOCK_EX);
        $word = date('Ymd H:i:s')
            ."\n-------------------------------------\n"
            ."Message: ". $message ."\n"
            ."Data: ". json_encode($data) ."\n"
            . $trace
            ."-------------------------------------\n\n";
        fwrite($fp,$word);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * 递归创建文件夹
     * @param $path			//文件夹绝对路径
     * @return boolean
     * @author xym Created at 2014-04-24
     */
    static private function _mkdir($path, $mode = 0777) {
        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
        $folderArr = explode("/", ltrim($path, "/"));
        if(substr($path, 0, 1) == "/") {
            $folderArr[0] = "/".$folderArr[0];
        }
        $num = count($folderArr);
        $cp = $folderArr[0];
        for($i = 1; $i < $num; $i++) {
            if(!is_dir($cp) && !mkdir($cp, $mode)) {
                return false;
            }
            $cp .= "/".$folderArr[$i];
        }
        if (!is_dir($path)) {
            return mkdir($path, $mode);
        }else return true;
    }
}