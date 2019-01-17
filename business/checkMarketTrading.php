<?php

use Workerman\Worker;

$market_trade_worker = new Worker("text://" . MARKET_TRADE_CONNECT_IP . ":" . MARKET_TRADE_PORT);

$market_trade_worker->name = "market_trade_worker";

$market_trade_worker->count = MARKET_TRADE_BUSINESS_NUM;

$market_trade_worker->onWorkerStart = function ($market_trade_worker) {
    $market_trade_worker->limit = array();
    $market_trade_worker->redis = new Redis();
    $market_trade_worker->redis->connect(REDIS_HOST, REDIS_PORT);
    $market_trade_worker->redis->auth(REDIS_PSW);

};

$market_trade_worker->onMessage = function ($connection, $data) use ($market_trade_worker) {

    $redis = $market_trade_worker->redis;

    $data = json_decode($data, true);
    $postdata = array(
        'symbol' => $data['symbol'],
        'period' => $data['period'],
    );

    $redis->incr('keys');

//    Http::curlPost();

};
    
    
    
    
    
    
    
    
    
    
