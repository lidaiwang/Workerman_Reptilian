<?php
use Workerman\Worker;

$market_trade_worker = new Worker("text://" . MARKET_TRADE_CONNECT_IP . ":" . MARKET_TRADE_PORT);

$market_trade_worker->name = "market_trade_worker";

$market_trade_worker->count = MARKET_TRADE_BUSINESS_NUM;

$market_trade_worker->onWorkerStart = function ($market_trade_worker) {
    $market_trade_worker->limit = array();
//    $market_trade_worker -> redis = new Redis();
//    $market_trade_worker -> redis -> connect(REDIS_HOST, REDIS_PORT);
//    $market_trade_worker -> redis -> auth(REDIS_PSW);
};

$market_trade_worker->onMessage = function ($connection, $data) use ($market_trade_worker) {
    $data = json_decode($data, true);
    $cycle = $data['cycle'] * 60;

    $limit = $market_trade_worker->limit;
    // 判断时间
    if (!empty($limit[$cycle]) && time() - $limit[$cycle] < ($cycle/4)) {
        return false;
    }
    $limit[$cycle] = time();
    $market_trade_worker->limit = $limit;

    echo date('Y-m-d H:i:s') . ' - ' . $cycle . "\n";

    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->select(1);
    $redis->set('okex_flag', date('Y-m-d H:i:s'));

    require_once PATH . '/lib/Http.php';
    $http = Http::_getInstance();

    $url = 'https://www.aicoin.net.cn/chart/api/data/period?symbol=okcoinfuturesbtcweekusd&step=' . $cycle;
    $header[] = 'Referer: https://www.aicoin.net.cn/chart/499C88CC';
    $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36';

    $result = $http->curlGet($url, array(), 3, $header);
    $content = json_decode($result['content'], true);

    if (!is_array($content)){
        echo date('Y-m-d H:i:s', time()) . ' _  ' . $data['cycle'] . '请求失败' . "\n";
        return false;
    }

    $content_ = $content['data'];
    array_pop($content_);

    $time_cycle = $cycle / 60;
    foreach ($content_ as $k => $v) {
        $pipe = $redis->multi(Redis::PIPELINE);
        $time_ = $v[0];
        $str = implode('|', $v);
        $pipe->zAdd('okexbtc:' . $time_cycle, $time_, $str);
        $pipe->exec();
    }


//    print_r($result);
};
    
    
    
    
    
    
    
    
    
    
