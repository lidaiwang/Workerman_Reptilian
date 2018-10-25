<?php
//	const REDIS_HOST = 'r-wz9644ef3ab2fbd4693.redis.rds.aliyuncs.com';
//	const REDIS_PORT = 6379;
//	const REDIS_PSW = 'V8dtFxeeYCpgVWmVbLUS9bRT343SR';
//
//	const REDIS_CONF = [
//	    //主从数据库
//	    'masters' => [
//			[
//			    'host' => 'r-wz9644ef3ab2fbd4693.redis.rds.aliyuncs.com',
//			    'port' => 6379,
//			    'password' => 'V8dtFxeeYCpgVWmVbLUS9bRT343SR'
//			],
//
//	    ],
//	    'slaves' =>[
//	        [
//			    'host' => 'r-wz9644ef3ab2fbd4693.redis.rds.aliyuncs.com',
//			    'port' => 6379,
//			    'password' => 'V8dtFxeeYCpgVWmVbLUS9bRT343SR'
//			],
//	    ]
//	];
//
//
//	//app websocket register 内网地址
////	const REGISTER_IP = '10.29.240.118';
////	const REGISTER_PORT = '1238';
//
//	//项目所在服务器内网地址
//	define('GLOBALDATA_ACCESS', 'siuhfjesuif7(*&&8sdf7');
//	define('GLOBAL_DATA_IP', '172.31.1.131');
//	define('GLOBAL_DATA_PORT', 2210);
//
//	define('BAIDUNEWS_IP', '172.31.1.131');
//	define('BAIDUNEWS_PORT', 2345);
//
//	//个人指数任务处理器端口、启动地址和连接地址
//	define('PERSONAL_INDEX_PORT', 2350);
//	define('PERSONAL_INDEX_START_IP', '127.0.0.1');
//	define('PERSONAL_INDEX_CONNECT_IP', '127.0.0.1');
//	define('PERSONAL_INDEX_BUSINESS_NUM', 5);      //处理器数量
//
//	//平台的交易对监测
//	define('MARKET_TRADE_LOOP_TIME', 300);
//	define('NOTICE_EMAIL', 'lidaiwangtime@163.com,lucaschn@qq.com,247662203@qq.com,1130866261@qq.com,960785537@qq.com,774619301@qq.com,841130117@qq.com');
//	define('MARKET_TRADE_PORT', 2455);
//	define('MARKET_TRADE_START_IP', '127.0.0.1');
//	define('MARKET_TRADE_CONNECT_IP', '127.0.0.1');
//	define('MARKET_TRADE_BUSINESS_NUM', 5);      //处理器数量
//
//	const DBCONF = [
//		'db1' => [
//			'host' 		=> 'rm-wz9144515i4mrh9gy.mysql.rds.aliyuncs.com',
//			'port' 		=> 3306,
//			'database' 	=> 'aicoin',
//			'user' 		=> 'aicoin',
//			'password' 	=> 'Jc12fwJ9zPOUeexVatRRwIxlqQt42zDY',
//		],
//		//2017-10-07注释
////		'bitrees'        => array(
////				'host'       => '56a0489a2854f.gz.cdb.myqcloud.com',
////				'port'       => '7209',
////				'database'   => 'bitrees',
////				'user' 		 => 'bifusosobtc',
////				'password' 	 => 'Fg34256JEE785hge'
////		),
//
//	];
//
//	const RONGCLOUD_APIKEY = 'z3v5yqkbvs0v0';
//	const RONGCLOUD_SECRETKEY = 'ShxeqbzdXrk';
//
//	//任务数组[文件名, 循环时间间隔（秒）]
//	static $taskConf = array(
////        array('class' => 'btc_fees', 	        'method' => 'loopRun',       'time_interval' => 5),
////        array('class' => 'coin_supply',         'method' => 'loopRun',       'time_interval' => 30),
////        //买卖点关闭[数据表不存在]
////// 			array('class' => 'oppoint', 	'method' => 'loopRun',       'time_interval' => 30),
////        array('class' => 'blockchain', 	        'method' => 'loopRun',       'time_interval' => 60),
////        array('class' => 'vip_check', 	        'method' => 'loopRun',       'time_interval' => 3600),
////        array('class' => 'rate_sina', 	        'method' => 'loopRun',       'time_interval' => 5),
////        array('class' => 'rate_okcoin',         'method' => 'loopRun',       'time_interval' => 10),
////        array('class' => 'rate_others',         'method' => 'loopRun',       'time_interval' => 0.5),
////        array('class' => 'weibo', 		        'method' => 'loopRun',       'time_interval' => 180),
////        array('class' => 'weibo', 		        'method' => 'historyToDb',   'time_interval' => 5),
////        array('class' => 'order_deal', 	        'method' => 'loopRun',       'time_interval' => 10),
////        array('class' => 'blockchain', 	        'method' => 'removeOld',     'time_interval' => 3600),
////        array('class' => 'data_monitor',        'method' => 'loopRun',       'time_interval' => 300),
////        //APP首页统计
////        array('class' => 'data_appdata',        'method' => 'loopRun',       'time_interval' => 10),
////        //APP大单小单的统计
////        array('class' => 'data_appstatistic',   'method' => 'loopRun', 'time_interval' => 30),
////        //soso国内国外指数--使用新接口后将被弃用
////        array('class' => 'data_appIndex',       'method' => 'loopRun', 'time_interval' => 5),
////        //删除已下架币种的key
////// 	       array('class' => 'data_appdelkey', 'method' => 'loopRun', 'time_interval' => 1800),
////        //统计活跃不活跃币种
////        array('class' => 'data_tradescount',    'method' => 'loopRun', 'time_interval' => 1800),
////        array('class' => 'baidunews', 	        'method' => 'loopRun', 'time_interval' => 2),
////        //统计聊天室人数
////        array('class' => 'data_countonline', 	'method' => 'loopRun', 'time_interval' => 5),
////        //soso指数
////        array('class' => 'data_continentIndex', 'method' => 'loopRun', 'time_interval' => 5),
////        array('class' => 'personal_index', 	    'method' => 'loopRun', 'time_interval' => 10),
////
////	       //个人指数统计
////	       array('class' => 'personal_index', 	'method' => 'loopRun', 'time_interval' => 5),
////
////        array('class' => 'changPicUrl', 	'method' => 'loopRun', 'time_interval' => 10),
//
//
//		/**
//		 * 2017-10-07   仅启动app首页的几个接口
//		 */
//		array('class' => 'data_appdata',        'method' => 'loopRun',       'time_interval' => 10),
//		array('class' => 'data_appstatistic',   'method' => 'loopRun', 'time_interval' => 30),
//		array('class' => 'data_continentIndex', 'method' => 'loopRun', 'time_interval' => 5),
//
//		array('class' => 'personal_index', 	    'method' => 'loopRun', 'time_interval' => 10),
//
//		array('class' => 'rate_others',         'method' => 'loopRun',       'time_interval' => 0.5),
//		//处理汇率---2017-10-16
//		array('class' => 'rate_sina', 	        'method' => 'loopRun',       'time_interval' => 5),
//		array('class' => 'rate_okcoin',         'method' => 'loopRun',       'time_interval' => 10),
//		//添加币值--2017-10-28
//		array('class' => 'coin_supply',         'method' => 'loopRun',       'time_interval' => 30),
//		//统计活跃不活跃币种 --- 2017-10-28
//		array('class' => 'data_tradescount',    'method' => 'loopRun', 'time_interval' => 1800),
//		//费率计算--2017-10-28
//		array('class' => 'btc_fees', 	        'method' => 'loopRun',       'time_interval' => 5),
//
//        //获取聊天室在线人数 -- 2017-11-08
//        array('class' => 'roomCount',         'method' => 'loopRun',       'time_interval' => 1),
//
//		//微博更新
//		array('class' => 'weibo', 		        'method' => 'loopRun',       'time_interval' => 600),
//
//		//获取bitbox的汇率
//		array('class' => 'rate_blockchain', 'method' => 'loopRun',       'time_interval' => 10),
//	    //平台的交易对监测
//	    array('class' => 'checkMarketTrading', 	'method' => 'loopRun', 'time_interval' => MARKET_TRADE_LOOP_TIME),
//
//		//统计分析--行情数据的分析
//		array('class' => 'data_staticdata', 'method' => 'loopRun',       'time_interval' => 1),
//
//        //专栏作家的分数统计
//        array('class' => 'count_writer_new', 'method' => 'loopRun',       'time_interval' => 45),
//		//行情数据-获取okex的指数价格
//		array('class' => 'price_okexIndex', 'method' => 'loopRun',       'time_interval' => 10),
//
//		//费率统计--获取币安的费率
//		array('class' => 'rate_binance', 'method' => 'loopRun',       'time_interval' => 120),
//        //获取平台的最小交易价格
//        array('class' => 'rate_market_min_trade', 'method' => 'loopRun',       'time_interval' => 300),
//
//	);