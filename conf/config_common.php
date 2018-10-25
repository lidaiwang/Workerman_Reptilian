<?php

	const MASTER_KEY = 'masters';
	const SLAVES_KEY = 'slaves';
	//推送---迁移到阿里云，注释当前的推送配置
//	const JPUSH_APIKEY = '00e693184512a3037be8649d';
//	const JPUSH_SECRETKEY = 'e21b76e1a5ca9235b21fc35e';
	
	
	const ENCODING = 'gzip,deflate,sdch';
	const AGENTARR = array(
			'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36',
			'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36',
			 
			'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:32.0) Gecko/20100101 Firefox/45.0',
			'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:30.0) Gecko/20100101 Firefox/46.0',
			'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/42.0',
			'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/43.0',
			 
			'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
			'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36 LBBROWSER'
	);
	
	
	//比特币最佳手续费-两套API
	const BTC_FEES = array(
			'use' => 'btc.com',		//当前使用的API接口
		    'mysql_db'=> 'fee' ,
			'api' => array(
				'btc.com' => array(
						'url' => 'https://btc.com/service/fees/distribution',
						'refer' => 'https://chain.btc.com/zh-cn/stats/unconfirmed-tx',
				),
				'21.co' => array(
						'url' => 'https://bitcoinfees.21.co/api/v1/fees/recommended',
						'refer' => 'http://www.sosobtc.com/',
				)
			)
	);
	
	//币种市值
	const COIN_SUPPLY = array(
			'api' => array(
					'url' => 'https://coinmarketcap.com/all/views/all/',
					'refer' => 'https://coinmarketcap.com/',
			)
	);
	
	
	//微博配置
	const WEIBOCONF = array(
			'weibo_db' 			=> 'weibo', 		//微博mysql数据表名
			'rd_index' 			=> 3,
			'weibo_key' 		=> 'weibo',
			'last_access_key' 	=> 'weibo:last',
			'max_record' 		=> 500,				//redis保留的最大记录数
			'access' 			=> array(
				'a' => array(
						'name' 			=> 'AICoin',
						'access_key' 	=> '3469095912',
						'secret_key' 	=> '253dc9627c011d3abeb90925c2b0f1db',
						'access_token' 	=> '2.00lhNWzGs_ylmDd2af9f73b3FmkDxC',
						'next' 			=> 'b',
				),
				'b' => array(
						'name' 			=> 'aicoin_com',
						'access_key' 	=> '916259902',
						'secret_key' 	=> 'dcbbf11569dd377ae3e58072888c9976',
						'access_token' 	=> '2.00lhNWzGWDXAAB16a2c784f6pfMMRE',
						'next' 			=> 'c',
				),
//				'c' => array(
//						'name' 			=> 'sosobtc_v2_02',
//						'access_key' 	=> '704534428',
//						'secret_key' 	=> '6f73f57e8c4af497450aaca053bb91c7',
//						'access_token' 	=> '2.00bi2fYG0WaJgl45004732ee0VXdpQ',
//						'next' 			=> 'd',
//				),
//				'd' => array(
//						'name' 			=> 'sosobtc_v2_03',
//						'access_key' 	=> '2546485737',
//						'secret_key' 	=> 'adc993c232fee796bcfac22e76c375eb',
//						'access_token' 	=> '2.00bi2fYGbQn1mC7eef538eb90clPy1',
//						'next' 			=> 'e',
//				),
//				'e' => array(
//						'name' 			=> 'sosobtc_v2_04',
//						'access_key' 	=> '77161425',
//						'secret_key' 	=> 'c2f50b608a05adf3dc4ad6cfed06986d',
//						'access_token' 	=> '2.00bi2fYG0HNlNFa737c087c40L1lhb',
//						'next' 			=> 'f',
//				),
//				'f' => array(
//						'name' 			=> 'sosobtc_v2_05',
//						'access_key' 	=> '2400700922',
//						'secret_key' 	=> 'bb8e706423e2becdec9dbaf95b74fac5',
//						'access_token' 	=> '2.00bi2fYGcyFTcCb9234dcd1eHkxcrC',
//						'next' 			=> 'a',
//				),
// 				'a' => array(
// 						'name' 			=> '搜搜比特币iOS',
// 						'access_key' 	=> '317270022',
// 						'secret_key' 	=> '3978b90c02488dc04e992d3c20efd4a2',
// 						'access_token' 	=> '2.00uq1SSE077OT22fe5d9386a5h2OfB',
// 						'next' 			=> 'b',
// 				),
// 				'b' => array(
// 						'name' 			=> '比特币快播',
// 						'access_key' 	=> '275041146',
// 						'secret_key' 	=> '7483c1cc89247cdc7d628b34e5f5c7e4',
// 						'access_token' 	=> '2.00uq1SSE0WlCcS6a796d3a27xpvtcE',
// 						'next' 			=> 'c',
// 				),
// 				'c' => array(
// 						'name' 			=> 'sosobtcapp',
// 						'access_key' 	=> '415248952',
// 						'secret_key' 	=> 'e60ec404273cb86b67ca857032c3c782',
// 						'access_token' 	=> '2.00uq1SSE0kN2G913c743f505lZ3PXD',
// 						'next' 			=> 'd',
// 				),
// 				'd' => array(
// 						'name' 			=> 'sosobtc',
// 						'access_key' 	=> '1356351883',
// 						'secret_key' 	=> 'ab1845d4e7d55f8185e25d7d5ea5f78d',
// 						'access_token' 	=> '2.00uq1SSERFHnTBf2d380157e0Z9gza',
// 						'next' 			=> 'e',
// 				),
// 				'e' => array(
// 						'name' 			=> '比特币APP',
// 						'access_key' 	=> '2009355781',
// 						'secret_key' 	=> '07f984d74a80bf7402a75fb577845624',
// 						'access_token' 	=> '2.00uq1SSENODzLC526d32e056Q4L_9C',
// 						'next' 			=> 'f',
// 				),
// 				'f' => array(
// 						'name' 			=> 'sosobitcoin',
// 						'access_key' 	=> '3499296703',
// 						'secret_key' 	=> '3b8db23a2e128733561d1ba2166f9fcf',
// 						'access_token' 	=> '2.00uq1SSE6vgooD875c4969bc3cse8C',
// 						'next' 			=> 'a',
// 				),
			)
	);
	
	//买卖点
	const OPPOINTCONF = array(
	    'robot_uid' => '22076',
	    'groupIds'  => ['sosobtc569778f0a7a9c'],
	    'dbkey'     => 'op:point',
	);
	
	
	const BLOCKCHAIN = array(
	    'db' 		=> 3,
		'dbkey' 	=> 'block:btc:score',
		'dbinfo' 	=> 'block:btc',
		'max_rec' 	=> 50,
	    'api' 		=> array(
	        'url' 		=> 'https://btcapp.api.btc.com/v1/block/list',
	        'headers' 	=> array('Host: btcapp.api.btc.com', 'Accept-Language: zh-Hans-CN;q=1'),
	        'useragent' => 'BTCCOM/12 (iPhone; iOS 9.3.2; Scale/2.00)',
	    	'height' 	=> 10,
	    	'size' 		=> 10,
	    ),
		'api_v2'    => array(
			'url' 		=> 'https://chain.api.btc.com/v3/block/',
			'useragent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.1204.275 Safari/537.36'
		)
	);
	
	//汇率配置
	const RATECONF = array(
		'change_mark' 	=> 'exrate:change',
		'rate_key' 		=> 'exrate',
							//   人民币  美元   欧元   日元   印度卢布   韩元
	    'rate' 			=> array('cny', 'usd', 'eur', 'jpy', 'inr', 'krw'),
		
		//okc汇率配置
		'okc' 			=> [
				'api'      => array(
						'url' => 'https://www.okex.com/api/v1/exchange_rate.do',
				),
				'value_key'    => 'okc2cny',
		],
		
		//自定义汇率（其他币定价，中间过程转换处理）
		'custom' 		=> [
				//  mk   =>   [ coin_key => [取值平台， 取值平台的定价]
				'poloniex' 	=> [
								'btc'  => ['key' => 'ticker:btc:poloniex', 'currency' => 'usd'],
								'eth'  => ['key' => 'ticker:eth:poloniex', 'currency' => 'usd'],
								'bch'  => ['key' => 'ticker:bchbtc:poloniex', 'currency' => 'btc'],
								'etc'  => ['key' => 'ticker:etcbtc:poloniex', 'currency' => 'btc'],
								'usdt' => ['key' => 'ticker:usdt:kraken',  'currency' => 'usd'],
				],
				'bittrex' 	=> [
								'btc' => ['key' => 'ticker:btcusdt:bittrex', 'currency' => 'usd'],
								'eth' => ['key' => 'ticker:ethbtc:bittrex', 'currency' => 'btc'],
								'etc' => ['key' => 'ticker:etcbtc:bittrex', 'currency' => 'btc'],
								'bcc' => ['key' => 'ticker:bccbtc:bittrex', 'currency' => 'btc'],
								'usdt' => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
				],
				'bitmex' 	=> [
								'xbt' => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd']
				],

		        'liqui' 	=> [
								'btc' => ['key' => 'ticker:btc:liqui', 'currency' => 'usd'],
								'eth' =>  ['key' => 'ticker:eth:bitfinex', 'currency' => 'usd'],
								'usdt'=> ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],

				],
		        'binance'   => [
		                          'btc' => ['key' => 'ticker:btcusdt:binance', 'currency' => 'usdt'],
		                          'eth' => ['key' => 'ticker:ethusdt:binance',  'currency' => 'usdt'],
					              'bcc' => ['key' => 'ticker:bccbtc:binance',  'currency' => 'btc'],
								  'ltc' => ['key' => 'ticker:ltc:okcoin',  'currency' => 'cny'],
								  'usdt'=> ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
		                          'bnb'=> ['key' => 'ticker:bnbusdt:binance',  'currency' => 'usdt'],
		        ],

		        'okex'    	=> [
								'btc' => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
								'bcc' => ['key' => 'ticker:bccbtc:okex', 'currency' => 'btc'],
								'ltc' => ['key' => 'ticker:ltcbtc:okex', 'currency' => 'btc'],
								'eth' => ['key' => 'ticker:ethbtc:okex', 'currency' => 'btc'],
								'usdt'=> ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
								'bch' => ['key' => 'ticker:bchbtc:okex', 'currency' => 'btc'],

				],
		        'huobi'    	   => ['btc' => ['key' => 'ticker:btc:huobi', 'currency' => 'cny']],
				'huobipro'     => [
									'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
									'eth'  => ['key' => 'ticker:ethusdt:huobipro', 'currency' => 'usdt'],
									'usdt'=> ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
				],

				''			=> [
				    'usdt' => ['key' => 'ticker:usdt:binance', 'currency' => 'usd'],
                    'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
					'bitcny' => ['key' => 'ticker:qc:exx', 'currency' => 'cny'],
					'zby' => ['key' => 'ticker:zbusdt:zb', 'currency' => 'cny'],
                ],
				'bitfinex'	=> [
								'eth' => ['key' => 'ticker:eth:bitfinex',  'currency' => 'usd'],
								'btc' => ['key' => 'ticker:btc:bitfinex',  'currency' => 'usd'],
								'ltc' =>['key' => 'ticker:ltc:bitfinex',  'currency' => 'usd'],
								'bch' =>['key' => 'ticker:bch:bitfinex',  'currency' => 'usd'],
								'etc' =>['key' => 'ticker:etc:bitfinex',  'currency' => 'usd'],

				],
				'hitbtc'	=> [
								'btc' => ['key' => 'ticker:btc:hitbtc', 'currency' => 'usd'],
								'ltc' => ['key' => 'ticker:ltc:hitbtc', 'currency' => 'usd'],
								'bcc' => ['key' => 'ticker:bcc:hitbtc', 'currency' => 'usd'],
								'etc' => ['key' => 'ticker:etc:hitbtc', 'currency' => 'usd'],
								'eth' => ['key' => 'ticker:eth:hitbtc', 'currency' => 'usd'],

				],
				'bithumb'  => [
								'btc' =>  ['key' => 'ticker:btc:bithumb', 'currency' => 'krw'],
								'bch' =>  ['key' => 'ticker:bch:bithumb', 'currency' => 'krw'],
								'ltc' =>  ['key' => 'ticker:ltc:bithumb', 'currency' => 'krw'],
								'eth' =>  ['key' => 'ticker:eth:bithumb', 'currency' => 'krw'],
								'etc' =>  ['key' => 'ticker:etc:bithumb', 'currency' => 'krw'],

				],
				'coincheck'=> ['btc' =>  ['key' => 'ticker:btc:coincheck', 'currency' => 'jpy'],],
				'bitstamp'=> [
							 'btc' =>  ['key' => 'ticker:btc:bitstamp', 'currency' => 'usd'],
							 'ltc' =>  ['key' => 'ticker:ltc:bitstamp', 'currency' => 'usd'],
							 'eth' =>  ['key' => 'ticker:eth:bitstamp', 'currency' => 'usd'],
				],
				'bitflyer'=> [
							 'btc' =>  ['key' => 'ticker:btc:bitflyer', 'currency' => 'jpy'],
							 'bch' =>  ['key' => 'ticker:bchbtc:bitflyer', 'currency' => 'btc'],
							 'eth' =>  ['key' => 'ticker:ethbtc:bitflyer', 'currency' => 'btc'],
				],
				'kraken'=> [
							'btc' =>  ['key' => 'ticker:btc:kraken', 'currency' => 'usd'],
							'bch' =>  ['key' => 'ticker:bch:kraken', 'currency' => 'usd'],
							'ltc' =>  ['key' => 'ticker:ltc:kraken', 'currency' => 'usd'],
							'eth' =>  ['key' => 'ticker:eth:kraken', 'currency' => 'usd'],
							'etc' =>  ['key' => 'ticker:etc:kraken', 'currency' => 'usd'],
				],
				'coinbase'=> [
							'btc' =>  ['key' => 'ticker:btc:coinbase', 'currency' => 'usd'],
						    'ltc' =>  ['key' => 'ticker:ltc:coinbase', 'currency' => 'usd'],
							'eth' =>  ['key' => 'ticker:eth:coinbase', 'currency' => 'usd'],
				],
				'korbit' => [
							'btc' =>  ['key' => 'ticker:btc:korbit', 'currency' => 'krw'],
							'bch' =>  ['key' => 'ticker:bch:korbit', 'currency' => 'krw'],
							'eth' =>  ['key' => 'ticker:eth:korbit', 'currency' => 'krw'],
				],
				'itbit' => ['btc' =>  ['key' => 'ticker:btc:itbit', 'currency' => 'usd'],],
				'coinone' => [
						 'btc' =>  ['key' => 'ticker:btc:coinone', 'currency' => 'krw'],
						 'bch' =>  ['key' => 'ticker:bch:coinone', 'currency' => 'krw'],
						 'etc' =>  ['key' => 'ticker:etc:coinone', 'currency' => 'krw'],
				],
				'okcoincom'  => [
						'btc' =>  ['key' => 'ticker:btc:okcoincom', 'currency' => 'usd'],
						'ltc' =>  ['key' => 'ticker:ltc:okcoincom', 'currency' => 'usd'],
						'eth' =>  ['key' => 'ticker:eth:okcoincom', 'currency' => 'usd'],


				],
				'okcoin'  => [
					'btc' =>  ['key' => 'ticker:btc:okcoin', 'currency' => 'cny'],
					'ltc' =>  ['key' => 'ticker:ltc:okcoin', 'currency' => 'cny'],

				],

				'gemini'  => [
					'eth' =>  ['key' => 'ticker:eth:gemini', 'currency' => 'usd'],

				],

		        'bitz' => [
		            
		            'btc' =>  ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
		        ],

				'chaoex' => [
					'btc' =>  ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
					'eth' =>  ['key' => 'ticker:eth:bitfinex', 'currency' => 'usd'],
					'dlc' =>  ['key' => 'ticker:dlcbtc:chaoex', 'currency' => 'btc'],
					'code' =>  ['key' => 'ticker:code:chaoex', 'currency' => 'cny'],

				],

				'gate' => [
					'usdt'=> ['key' => 'ticker:usdtcny:gate',  'currency' => 'cny'],
					'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
					'eth'  => ['key' => 'ticker:ethusdt:gate', 'currency' => 'usdt'],
					'qtum'  => ['key' => 'ticker:qtumbtc:gate', 'currency' => 'btc'],
				],

				'aex' =>[
					'usdt' => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
					'btc'  => ['key' => 'ticker:btcbitcny:aex', 'currency' => 'bitcny'],
					'bitcny'  => ['key' => 'ticker:qc:exx', 'currency' => 'cny'],
					'bitusd'  => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
				],
			
				'bigone' =>[
					'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
					'eos'  => ['key' => 'ticker:eosbtc:bigone', 'currency' => 'btc'],
					'qtum'  => ['key' => 'ticker:qtumbtc:bigone', 'currency' => 'btc'],
					'bitcny'  => ['key' => 'ticker:qc:exx', 'currency' => 'cny'],
				],

				'zb' =>[
					'btc'  => ['key' => 'ticker:btcusdt:zb', 'currency' => 'usdt'],
					'usdt' => ['key' => 'ticker:usdt:binance',  'currency' => 'zby'],
					'qc'   => ['key' => 'ticker:qc:exx', 'currency' => 'cny'],
				],

				'kkex' => [
					'usdt' => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
					'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
				],

				'rightbtc' => [
					'usdt' => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
					'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
				],

				'coinegg' => [
					'btc'  => ['key' => 'ticker:btc:bitstamp', 'currency' => 'usd'],
					'usc'  => ['key' => 'ticker:usc:btctradeIm', 'currency' => 'usd'],
				],
				'btctradeIm' => [
					'btc'  => ['key' => 'ticker:btc:bitstamp', 'currency' => 'usd'],
				    'usc'  => ['key' => 'ticker:usc:btctradeIm', 'currency' => 'usd'],
				],

				'allcoin' =>  [
					'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
					'eth'  => ['key' => 'ticker:eth:bitfinex', 'currency' => 'usd'],
				],
    		    'cex' =>  [ 
    		        'eth'  => ['key' => 'ticker:eth:bitfinex', 'currency' => 'usd'],
    		    ],

				'bcex' =>  [
					'btc'    => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
					'ckusd'  => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
				],

    		    'exx' =>  [
    		        'usdt' => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
    		        'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
					'eth'  => ['key' => 'ticker:eth:bitfinex', 'currency' => 'usd'],
					'qc'   => ['key' => 'ticker:qc:exx', 'currency' => 'cny'],
					'qtum' => ['key' => 'ticker:qtumbtc:exx', 'currency' => 'btc'],
					'hsr' => ['key' => 'ticker:hsrusdt:okex', 'currency' => 'usdt']
    		    ],

				'coin900' => [
					'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
				],

				'bibox'  => [
					'btc'  => ['key' => 'ticker:btcusd:blockchain', 'currency' => 'usd'],
					'eth'  => ['key' => 'ticker:eth:bitfinex', 'currency' => 'usd'],
					'usdt' => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
					'dai' => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
				],

				'cryptopia' =>  [
					'btc'  => ['key' => 'ticker:btc:bitfinex', 'currency' => 'usd'],
					'ltc'  => ['key' => 'ticker:ltcbtc:okex', 'currency' => 'btc'],
					'doge'   => ['key' => 'ticker:dogeusdt:gate', 'currency' => 'usdt'],
					'usdt' => ['key' => 'ticker:usdt:binance',  'currency' => 'usd'],
				],

				'cccbtc ' => [
					'btc'    => ['key' => 'ticker:usdt:binance', 'currency' => 'usdt'],
					'ct'  	 => ['key' => 'ticker:qc:exx', 'currency' => 'cny'],
				],
        ],
		'group' => 'exrate',
	);
	
	$markets = array(
				'bitstamp' 		=> array('cn_name' => 'Bitstamp', 	'coin' => array('btc')),
				'bitfinex' 		=> array('cn_name' => 'Bitfinex', 	'coin' => array('btc', 'ltc')),
				'btce' 			=> array('cn_name' => 'BTC-E', 		'coin' => array('btc', 'ltc')),
		
				'okcoincom' 	=> array('cn_name' => 'OKCoin国际', 	'coin' => array('btc', 'ltc')),
				'okcoin' 		=> array('cn_name' => 'OKCoin中国', 	'coin' => array('btc', 'ltc')),
				'okcoinfutures' => array('cn_name' => 'OKCoin期货', 	'coin' => array('btcweek', 'ltcweek', 'btcnextweek', 'ltcnextweek', 'btcquarter', 'ltcquarter')),
		
				'huobi' 		=> array('cn_name' => '火币网', 		'coin' => array('btc', 'ltc')),
				'bitvc' 		=> array('cn_name' => 'BitVC期货', 	'coin' => array('btcweek', 'btcquarter')),
				'huobiusd' 		=> array('cn_name' => '火币美元现货', 	'coin' => array('btc')),
		
				'btcchina' 		=> array('cn_name' => '比特币中国', 	'coin' => array('btc', 'ltc')),
				'796' 			=> array('cn_name' => '796期货', 		'coin' => array('btc', 'ltc')),
				'btc38' 		=> array('cn_name' => '比特时代', 		'coin' => array('btc', 'ltc', 'bts', 'doge', 'xrp', 'str', 'nxt', 'bc', 'bils', 'btsbtc', 'dogebtc', 'tmcbtc', 'nxtbtc')),
				'cnbtc' 		=> array('cn_name' => '中国比特币', 	'coin' => array('btc', 'ltc')),
				'btctrade' 		=> array('cn_name' => 'BTCTrade', 	'coin' => array('btc', 'ltc')),
				'btc100' 		=> array('cn_name' => 'BTC100', 	'coin' => array('btc', 'ltc')),
				'bter' 			=> array('cn_name' => '比特儿', 		'coin' => array('btc', 'ltc', 'doge', 'ppc', 'nxt', 'nmc', 'pts', 'xcp', 'qrk')),
				'yunbi'			=> array('cn_name' => '云币网',		'coin' => array('btc', 'ltc')),
	);

	//zec配置
	const ZCHAIN = array(
		'db' 		=> 3,
		'dbkey' 	=> 'block:zec:score',
		'dbinfo' 	=> 'mine:info:zec',
		'dbinfo2' =>'block:zec',
		'max_rec' => 50,
		'api'    =>array(
			'url' 		=> 'https://api.zcha.in/v2/mainnet/network',
			'useragent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.1204.275 Safari/537.36'

		),
		'api_2'    =>array(
			'url' 		=> 'https://api.zcha.in/v2/mainnet/blocks?limit=1&offset=0&sort=height',
			'useragent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'

		)


	);

//eth配置
const ETHCHAIN = array(
	'db' 		=> 3,
	'dbkey' 	=> 'block:eth:score',
	'dbinfo' 	=> 'mine:info:eth',
	'max_rec' => 50,
	'api'    =>array(
		'url' 		=> 'https://etherchain.org/api/basic_stats',
		'useragent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.1204.275 Safari/537.36'

	)


);





	
  
