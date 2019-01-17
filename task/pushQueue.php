<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/14
 * Time: 22:45
 */


class pushQueue
{
    //配置文件
    public $conf = array();
    //临时存储文件
    public $data = array();
    public $redis;
    public $db;

    public function __construct()
    {
        $this->setConf();

        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        if (REDIS_PSW) {
            $redis->auth(REDIS_PSW);
        }
        $this->redis = $redis;
    }

    public function setConf()
    {
        $this->conf = array(
            array(
                'symbol' => 'btc',
                //分钟
                'period_list' => array(
                    1,
                    3,
                    5,
                    10,
                    15,
                    30,
                    60,
                    120,
                    240,
                    360,
                    720,
                    1440,
                ),
            ),

            array(
                'symbol' => 'eos',
                //分钟
                'period_list' => array(
                    1,
                    3,
                    5,
                    10,
                    15,
                    30,
                    60,
                    120,
                    240,
                    360,
                    720,
                    1440,
                ),
            ),

        );
    }


    /*
     * 1s运行一次  判断分发加上计算任务
     * 计算任务 防止计算堆砌  需要加锁
     */
    public function loopRun()
    {

        $key = 'redis_Queue';
        $queue_leng = 1000;

        $now_time = time();
        $redis = $this->redis;
        $data = $this->data;
        $conf = $this->conf;
        $redis_key_leng = $redis->zCard($key);

        //添加队列
        if ($redis_key_leng < $queue_leng) {
            $time_block = $redis->zRange($key, $redis_key_leng - 1, $redis_key_leng - 1, true);
            $time = $now_time;
            if (!empty($time_block)) {
                $time = array_values($time_block)[0];
            }

            $pipe = $redis->multi(Redis::PIPELINE);
            foreach ($conf as $k => $v) {
                $symbol = $v['symbol'];
                foreach ($v['period_list'] as $kk => $vv) {
                    //队列时间  25 - 55s
                    $time_interval = 15 + mt_rand(20, 45);
                    $time = $time + $time_interval;

                    $postdata = array(
                        'symbol' => $symbol,
                        'period' => $vv,
                        'time' => $time,
                    );

                    $pipe->zAdd($key, $time, json_encode($postdata));
                }
            }
            $pipe->exec();
        }

        //出队列
        $i = 1;
        $task_data = array();
        $flag = false;
        $point = 1;
        while ($i > 0) {
            $i++;
            if ($i > 10) {
                $point = 2;
                break;
            }

            $time_block = $redis->zRange($key, -1, -1, true);
            if (empty($time_block)) {
                $point = 3;
                break;
            }
            $time = array_values($time_block)[0];
            $value = array_keys($time_block)[0];

            //最后一个单元时间小于当前时间
            if ($now_time > $time) {
                $point = 4;
                break;
            } else {
                $redis->zRem($key, $value);
            }

            $value = json_decode($value, 1);
            $symbol = $value['symbol'];
            $period = $value['period'];
            $key = $symbol . $period;


            //  当前时间  减去  上次请求时间   大于周期的10%  1分钟=6s
            if (!isset($data[$key]) || (isset($data[$key]) && $now_time - $data[$key]) > $period * 60 / 10) {
                $data[$key] = $now_time;
                $task_data = $value;
                $flag = true;
                $point = 5;
                break;
            }

            Loggers::getInstance("service")->warning(json_encode(array('$data' => $data, '$value' => $value, 'key' => $key, '$now_time' => $now_time)));
        }

        //添加任务
        if ($flag) {
            $task_data['point'] = $point;

            $str = '{';
            foreach ($task_data as $k => $v) {
                $str .= '"' . $k . '": ' . '"' . $v . '", ';
            }
            $str = substr($str, 0, -2);
            $str .= "}\n";

            $client = stream_socket_client("tcp://" . MARKET_TRADE_CONNECT_IP . ":" . MARKET_TRADE_PORT);
            fwrite($client, $str);

            Loggers::getInstance("service")->warning(json_encode($task_data));
        }

        $this->data = $data;

        $this->aa();
    }

    //计算
    public function aa()
    {
    }

    //加锁
    public function lock($key = 'compute')
    {
        $redis = $this->redis;
        $redis->set($key, 1);
    }

    //去锁
    public function unlock($key = 'compute')
    {
        $redis = $this->redis;
        $redis->del($key);
    }

    //判读加锁
    public function isLock($key = 'compute')
    {
        $redis = $this->redis;
        $value = $redis->get($key);
        if ($value) {
            return true;
        }
        return false;
    }


}

