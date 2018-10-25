<?php

namespace App\Http\Lib;

use Hbase\HbaseClient;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;

require_once __DIR__ . '/../Hbase/Hbase.php';
require_once __DIR__ . '/../Hbase/Types.php';

class Hbase
{

    private static $host = '';
    private static $port = '';
    private static $client = '';

    public $transport = '';

    private static $Instance = NULL;

    public static function _getInstance($config_name = 'hbase')
    {
        if (self::$Instance == NULL) {
            $config         = DBCONF[$config_name];
            self::$host     = $config['host'];
            self::$port     = $config['port'];;
            self::$Instance = new self();
        }
        return self::$client;
    }

    private function __construct()
    {
        if (self::$Instance === NULL) {

            $socket = new TSocket(self::$host, self::$port);
            $socket->setSendTimeout(10000);
            $socket->setRecvTimeout(20000);
            $this->transport = new TBufferedTransport($socket);
            $protocol        = new TBinaryProtocol($this->transport);

            self::$client = new HbaseClient($protocol);
            $this->transport->open();
        }
    }

    public function __destruct()
    {
        $this->transport->close();
    }
}