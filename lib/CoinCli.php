<?php
	/**
	 * 所有钱包接口若请求失败，则返回false，否则返回对应的数据类型。
	 * 具体见各自注释标注的返回值
	 */
require_once 'CoinFactory.php';
class CoinCli
{
    
    /**
     * 所有实例的方法
     * 请求错误时必定包含error字段
     * ['error' => 'xxxxx']
     * 
     * 正常数据
     * ['data' => [...]]
     * 
     * @var array
     */
	protected static $Instances = [];
	
	const COINCONF = [
	        'btc' => [
	                'host' => '127.0.0.1',
	                'port' => '8332',
	                'user' => 'generated_by_armory',
	                'pwd' => '92Fxij7AbraYov4Kii1qRkMkDNBXQuWxJPVmEtkstwLX',
	                'encrypt' => '',
	        ],
	        'ltc' => [
	                'host' => '',
	                'port' => '',
	                'user' => '',
	                'pwd' => '',
	                'encrypt' => '',
	        ],
	];
	
	public static function instance($coin = '') {
	    if (!array_key_exists($coin, self::COINCONF)) {
	        throw new Exception("conf not set\n");
	    }
	    
		if (empty(self::$Instances[$coin])) {
			self::$Instances[$coin] = $ins = new CoinFactory(self::COINCONF[$coin]);
			return $ins;
		}
		return self::$Instances[$coin];
	}
}