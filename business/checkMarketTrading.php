<?php

use Workerman\Worker;

$market_trade_worker = new Worker("text://" . MARKET_TRADE_CONNECT_IP . ":" . MARKET_TRADE_PORT);

$market_trade_worker->name = "market_trade_worker";

$market_trade_worker->count = MARKET_TRADE_BUSINESS_NUM;

$market_trade_worker->onWorkerStart = function ($market_trade_worker) {
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->auth(REDIS_PSW);
    if (CONTINUOUS_FLAG == false) {
        $redis->flushdb();
    }
    $market_trade_worker->redis = $redis;

    $db = new MysqliDb (DB_HOST, DB_NAME, DB_PSW, DB_BASE);
    $market_trade_worker->db = $db;

    require_once PATH . '/lib/Http.php';
    require_once PATH . '/event/line.php';
};

$market_trade_worker->onMessage = function ($connection, $data) use ($market_trade_worker) {
    Loggers::getInstance("service")->warning($data);
    $redis = $market_trade_worker->redis;
    $redis->set('run_incr_time', date('Y-m-d H:i:s'));
    $db = $market_trade_worker->db;
    $data = json_decode($data, true);
    $period = $data['symbol'];
    $symbol = $data['period'];
    $postdata = array(
        'symbol' => $period,
        'period' => $symbol,
    );

    $url = "https://www.aicoin.net.cn/api/chart/kline/data/period";
    $queryparas = array();
    $header = array(
        'referer: https://www.aicoin.net.cn',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36',
    );
    $timeout = 5;

    $result = Http::curlPost($url, $queryparas, $postdata, $header, $timeout);

    if (isset($result['status_code']) && $result['status_code'] == 200 && !empty($result['body']) && is_array($result['body']['data']['kline_data'])) {
        $kline_data = $result['body']['data']['kline_data'];
        $keys = 'origin:' . $symbol . ':' . $period;
        $table_name = "k_line_origin";
        $msqRecord = array();

        $last_block_redis = $redis->zRange($keys, -1, -1);
        $last_time_redis = 0;
        if (!empty($last_block_redis)) {
            $last_time_redis = json_decode($last_block_redis[0], true)[0];
        }

        $last_block_mysql = $db
            ->where('symbol', $symbol)
            ->where('period', $period)
            ->orderBy("id", "Desc")
            ->getOne($table_name);
        $last_time_mysql = $last_block_mysql['time'];

        $pipe = $redis->multi(Redis::PIPELINE);
        foreach ($kline_data as $k => $v) {
            $time = $v[0];
            $date = date("Y-m-d H:i:s", $time);
            $v[] = $date;
            $v_str = json_encode($v);
            $kline_data[$k] = $v;
            if ($k < count($kline_data) - 1) {
                //去掉最后一个单元  最后一个单元的开高低收是在实时变化的
                if ($time > $last_time_mysql) {
                    $msqRecord[] = array(
                        'symbol' => $symbol,
                        'period' => $period,
                        'time' => $time,
                        'open' => $v[1],
                        'high' => $v[2],
                        'low' => $v[3],
                        'receive' => $v[4],
                        'date' => $date,
                        'add_time' => date("Y-m-d H:i:s"),
                    );
                }

                if ($time > $last_time_redis) {
                    $pipe->zAdd($keys, $time, $v_str);
                }
            }
        }

        $db->insertMulti($table_name, $msqRecord);
        $pipe->exec();
        return array_reverse($kline_data);
    } else {
        Loggers::getInstance("error")->warning("错误", -1, $result);
        return $result;
    }

    new Line($symbol, $period);
};
    
    
    
    
    
    
    
    
    
    
