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
	    'slaves' =>[
	        [
	            'host' => '127.0.0.1',
	            'port' => 6379,
	            'password' => null
	        ],
	    ]
	];
	
	const REGISTER_IP = '127.0.0.1';
	const REGISTER_PORT = '1238';
	
	define('GLOBALDATA_ACCESS', 'siuhfjesuif7(*&&8sdf7');
	define('GLOBAL_DATA_IP', '0.0.0.0');
	define('GLOBAL_DATA_PORT', 2210);
	
	define('BAIDUNEWS_IP', '0.0.0.0');
	define('BAIDUNEWS_PORT', 2345);
	
	//个人指数任务处理器端口、启动地址和连接地址
	define('PERSONAL_INDEX_PORT', 2350);
	define('PERSONAL_INDEX_START_IP', '127.0.0.1');
	define('PERSONAL_INDEX_CONNECT_IP', '127.0.0.1');
	define('PERSONAL_INDEX_BUSINESS_NUM', 5);      //处理器数量
	
	//平台的交易对监测
//	define('MARKET_TRADE_LOOP_TIME', 18);
	define('NOTICE_EMAIL', 'lidaiwangtime@163.com');
	define('MARKET_TRADE_PORT', 2355);
	define('MARKET_TRADE_START_IP', '127.0.0.1');
	define('MARKET_TRADE_CONNECT_IP', '127.0.0.1');
	define('MARKET_TRADE_BUSINESS_NUM', 2);      //处理器数量
	
	
// 	const DBCONF = [
// 		'db1' => [
// 			'host' => 'sosobtcdb.mysql.rds.aliyuncs.com',
// 			'port' => 3306,
// 			'database' => 'sosobtc',
// 			'user' => 'sosobtcdbaccount',
// 			'password' => 'fornew2016mysql',
// 		],
// 	];
	
	const DBCONF = [
		'db1' => [
			'host' 		=> '192.168.1.184',
			'port' 		=> 3306,
			'database' 	=> 'aicoin',
			'user' 		=> 'root',
			'password' 	=> 'root',
		],
		'bitrees'        => array(
				'host'       => '56a0489a2854f.gz.cdb.myqcloud.com',
				'port'       => '7209',
				'database'   => 'bitrees',
				'user' 		 => 'bifusosobtc',
				'password' 	 => 'Fg34256JEE785hge'
		),
	];
	
	
    const RONGCLOUD_APIKEY = 'lmxuhwagx9txd';
    const RONGCLOUD_SECRETKEY = 'zQlHYIFx7DTtVc';
	
    //任务数组[文件名, 循环时间间隔（秒）]
	static $taskConf = array(
// 	    array('class' => 'personal_index', 	'method' => 'loopRun', 'time_interval' => 10),
//         array('class' => 'changPicUrl', 	'method' => 'loopRun', 'time_interval' => 10),
	    array('class' => 'checkMarketTrading', 	'method' => 'loopRun', 'time_interval' => 3),
        array('class' => 'checkMarketTrading', 	'method' => 'loop', 'time_interval' => 15),
        array('class' => 'checkMarketTrading', 	'method' => 'loop1', 'time_interval' => 20),
//        array('class' => 'count_writer', 	'method' => 'loopRun', 'time_interval' => 10),


);
