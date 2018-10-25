<?php 
class RedisCluster {
    /**
     * redis实例集合
     */
    private static $instance = [];
    private static $redis = null;
    private static $table = null;
    private static $Ins = null;
    private static $config = [];
    
    
    /**
     * 获取RedisCluster操作实例
     * 
     * @param number $table
     */
    public static function _getInstance($table = 0) {
        self::$table = $table;
        if (self::$Ins === null) {
            self::$Ins = new self();
        }
        return self::$Ins;
    }
    

    /**
     * 加载配置
     * [
     *     'masters' => [
     *         [
     *             'host' => '',
     *             'port' => '',
     *             'password' => '',
     *         ],
     *         ...
     *     ],
     *     'slaves' => [
     *         [
     *             'host' => '',
     *             'port' => '',
     *             'password' => '',
     *         ],
     *         ...
     *     ]
     * ]
     */
    public function __construct() {
        self::$config = REDIS_CONF;
    }
    
    
    /**
     * 一主一从
     * 
     * @param string $type
     * @param string $slave_random      //yes -> 从库随机， int -> 指定第几个从库 （type 为 slaves 时生效）
     * @throws Exception
     */
    public function connect($type = 'master', $slave_random = 'yes') {
        
        //已经连接
        if (isset(self::$instance[$type])) {
            self::$instance[$type]->select(self::$table);
            self::$redis = self::$instance[$type];
            return ;
        }
        
        
        if (!isset(self::$config[$type]) || empty(self::$config[$type])) {
            throw new Exception('conf not exist');
        }
        
        if ($type === SLAVES_KEY) {
            if ($slave_random === 'yes') {
                $size = count(self::$config[$type]);
                if ($size < 2) {
                    $config = self::$config[$type][0];
                }else {
                    $num = mt_rand(0, $size - 1);
                    $config = self::$config[$type][$num];
                }
            }else {
                if (!isset(self::$config[$type][$slave_random])) {
                    throw new Exception('specific conf not found');
                }
                
                $config = self::$config[$type][$num];
            }
        }else {
            $config = self::$config[$type][0];
        }
        
        //建立连接
        self::$redis = new Redis();
        self::$redis->connect($config['host'], $config['port']);
        
        //需要密码
        if (!empty($config['password'])) {
            self::$redis->auth($config['password']);
        }
        self::$redis->select(self::$table);
        self::$instance[$type] = self::$redis;
    }
    
    /**
     * 切换db
     * @param number $db
     */
    public function select($db = 0) {
        self::$table = $db;
        return self::$Ins;
    }

    
    public function get($key) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->get($key);
    }

    /**
     * 优化处理，返回键值对数组
     * @param array $keys
     * @return array | bool
     */
    public function mget($keys) {
        if (!is_array($keys)) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        $values = self::$redis->mget($keys);
        $result = array_combine($keys, $values);
        
        return $result;
    }
    
    /**
     * 与mget相同
     * @param array $keys
     */
    public function getMultiple ($keys) {
        return $this->mget($keys);
    }


    /**
     * 
     * @param unknown $key
     * @param unknown $value
     * @param number $seconds    //过期时间（秒）
     */
    public function set($key, $value, $seconds = 0) {
        if (! $key) {
            return false;
        }
        if ($seconds) {
            return $this->setex($key, $seconds, $value);
        } else {
            $this->connect(MASTER_KEY);
            
            return self::$redis->set($key, $value);
        }
    }
    
    
    /**
     * 设置字符串及其有效期(秒)
     * 
     * @param string $key
     * @param number $seconds       //生存时间（秒）
     * @param string $value
     */
    public function setex($key, $seconds, $value) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->setex($key, $seconds, $value);
    }
    
    
    /**
     * 设置字符串及其有效期(毫秒)
     * 
     * @param string $key
     * @param number $microseconds       //生存时间（毫秒）， 1000ms = 1s
     * @param string $value
     */
    public function psetex($key, $microseconds, $value) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->psetex($key, $microseconds, $value);
    }
    
    

    /**
     * 删除缓存
     * @param string || array 多个key作为参数，或者key数组
     * @return int 删除的健的数量
     */

    public function delete($key) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->delete($key);
    }

    /**
     * 设置多个字符串数据
     * （1）所有字段设置相同的生存时间
     * ['key1' => 'val1', 'key2' => 'val2', ...]
     * 
     * （2）每个字段设置不同的生存时间
     * ['key1' => ['val' => 'xx', 'ttl' => 10], ...]
     * 
     * @param array $arr
     *
     * int -> 相同的生存时间， 其他值，则生存时间在第一个参数数组里面
     * @param number $seconds
     * @return boolean
     */
    public function setmulti($arr, $seconds = 0) {
        if(!is_array($arr)) {
            return false;
        }
        
        if (is_numeric($seconds)) {
            foreach($arr as $key => $v) {
                $this->set($key, $v, $seconds);
            }
        }else {
            foreach ($arr as $key => $info) {
                $this->set($key, $info['val'], empty($info['ttl']) ? 0 : $info['ttl']);
            }
        }
        return true;
    }

    /**
     * 自增ID
     * 
     * @param string $key
     * @param number $step
     * @return boolean
     */
    public function incr($key, $step = 1) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->incr($key, $step);
    }

    /**
     * 递减
     * 
     * @param string $key
     * @param number $step    //步长
     * @return boolean
     */
    public function decr($key, $step = 1) {
        if (! $key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->decr($key, $step);
    }

    /**
     * 获取旧内容，并写入新的内容
     * 
     * @param string $key
     * @param string $value
     * @return boolean | string
     */
    public function getSet($key, $value) {
        if (! $key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->getSet($key, $value);
    }

    /**
     * 增加集合成员(支持多个value)
     * 
     * @param string $key
     * @param string $value
     * @return false | int
     */
    public function sAdd($key, $value) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return call_user_func_array(array(self::$redis, 'sAdd'), func_get_args());
    }

    /**
     * 移除集合成员(支持多个value)
     * 
     * @param string $key
     * @param string $value
     * @return int
     */
    public function sRemove($key, $value) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return call_user_func_array(array(self::$redis, 'sRemove'), func_get_args());
    }

    /**
     * 获取集合成员
     * 
     * @param string $key
     * @return boolean
     */
    public function sMembers($key) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->sMembers($key);
    }

    /**
     * 检查集合是否存在该成员
     * 
     * @param string $key
     * @param string $member
     * @return boolean
     */
    public function sismember($key, $member) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->sismember($key, $member);
    }
    
    /**
     * 检查集合是否存在该成员(sismember)
     * 
     * @param string $key
     * @param string $member
     * @return boolean
     */
    public function sContains ($key, $member) {
        return $this->sismember($key, $member);
    }

    
    /**
     * 查找
     * 
     * @param string $key
     * @return boolean
     */
    public function keys($key) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->keys($key);
    }

    /**
     * 设置键的过期时间
     * 
     * @param string $key
     * @param string $second
     * @return boolean
     */
    public function expire($key, $seconds) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->expire($key, $seconds);
    }

    
    /**
     * 获取集合元素个数
     * 
     * @param string $key
     * @return int
     */
    public function scard($key) {
        if (! $key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->scard($key);
    }


    /**
     * 添加hash 单个键值信息
     * 
     * @param string $key
     * @param string $field
     * @param string $value
     * @return boolean
     */
    public function hSet($key, $field, $value) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->hSet($key, $field, $value);
    }
    
    /**
     * 添加hash 多个键值信息
     * 
     * @param string $key
     * @param array $arr     //['field1' => 'v1', 'field2' => 'v2', ...]
     * @return boolean
     */
    public function hMset($key, $arr) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->hMset($key, $arr);
    }

    /**
     * 获取hash单个键值信息
     *
     * @param string $hash
     * @param string $field
     * @return boolean
     */
    public function hGet($key, $field) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
    
        return self::$redis->hGet($key, $field);
    }
    
    /**
     * 获取hash 多个键值信息
     * @param string $key
     * @param array $arr    //多个field ['field1', 'field2', ...]
     * @return array | bool
     */
    public function hMget($key, $arr) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->hMget($key, $arr);
    }
    
    
    /**
     * 获取hash中所有的元素
     * 
     * @param string $key
     * @return boolean
     */
    public function hGetAll($key) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->hGetAll($key);
    }
    
    
    /**
     * 删除hash指定 field
     * 
     * @param string $key
     * @param string $field      accept one or more fields
     * @return boolean | int  false -> key not hash, 0 -> key not exist
     */
    public function hDel($key, $field, $_ = null) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return call_user_func_array([self::$redis, 'hDel'], func_get_args());
    }


    /**
     * hash 元素个数
     * 
     * @param string $hash
     * @return int
     */
    public function hLen($key) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->hLen($key);
    }


    /**
     * 返回hash所有值(非关联数组)
     * 
     * @param string $key
     * @return array
     */
    public function hVals($key) {
        if (!$key) {
            return false;
        }
        $this->connect(SLAVES_KEY);
        
        return self::$redis->hVals($key);
    }


    /**
     * 增加hash里面field 的数值
     * 若key或field原来不存在，则number作为初始值，
     * 否则在原基础上增加number
     * 
     * @param string $hash
     * @param string $field
     * @param string $number    //要增加的数量
     * @return boolean
     */
    public function hIncrBy($key, $field, $number) {
        if (!$key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$instance[self::$current_conf]->hIncrBy($key, $field, $number);
    }

    /**
     * 排序 list, set, sorted set
     * 
     * @param string $key
     * @param array $opt     //可选
     * @return boolean
     */
    public function sort($key, $opt) {
        if (! $key) {
            return false;
        }
        $this->connect(MASTER_KEY);
        
        return self::$redis->sort($key, $opt);
    }

    public function exists($key) {
        if (! $key) {
            return false;
        }
        return self::$instance[self::$current_conf]->exists($key);
    }

    public function clear() {
        return self::$instance[self::$current_conf]->flushAll();
    }

}