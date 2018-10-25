<?php
const REDIS_HOST = '127.0.0.1';
const REDIS_PORT = 6379;
const REDIS_PSW = '';

const REDIS_CONF = [
    //主从数据库
    'masters' => [
        [
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => null
        ],

    ],
    'slaves' => [
        [
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => null
        ],
    ]
];

const REGISTER_IP = '127.0.0.1';
const REGISTER_PORT = '1238';

const DBCONF = [
    'db1' => [
        'host' => '192.168.1.184',
        'port' => 3306,
        'database' => 'db1',
        'user' => 'root',
        'password' => 'root',
    ],
    'db2' => array(
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'db2',
        'user' => 'root',
        'password' => 'root'
    ),
];

//任务数组[文件名, 循环时间间隔（秒）]
static $taskConf = array(
    array('class' => 'checkMarketTrading', 'method' => 'loopRun', 'time_interval' => 3),
    array('class' => 'checkMarketTrading', 'method' => 'loop', 'time_interval' => 15),
    array('class' => 'checkMarketTrading', 'method' => 'loop1', 'time_interval' => 20),
);
