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
    //分钟
    public $period_list = array(
//        1,
//        3,
        5,
        10,
//        15,
        30,
        60,
        120,
        240,
        360,
        720,
        1440,
    );

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
//            array(
//                'name' => 'btc',
//                'symbol' => 'btcquarter:okcoinfutures',
//                'period_list' => $this->period_list,
//            ),
            array(
                'name' => 'eos',
                'symbol' => 'eosquarter:okex',
                'period_list' => $this->period_list,
            ),
//            array(
//                'name' => 'eth',
//                'symbol' => 'ethquarter:okex',
//                'period_list' => $this->period_list,
//            ),
        );
    }

    /*
     * 1s运行一次  判断分发加上计算任务
     * 计算任务 防止计算堆砌  需要加锁
     */
    public function loopRun()
    {
        $key = 'redis_queue';
        $redis_pull_push = 'redis_pull_push';
        $queue_leng = 20;

        $now_time = time();
        $now_date = date("Y-m-d H:i:s", $now_time);
        $redis = $this->redis;
//        $data = $this->data;
        $conf = $this->conf;
        $redis_key_leng = $redis->zCard($key);
        $pull_push_data = $redis->get($redis_pull_push);
        $pull_push_data = json_decode($pull_push_data, true);
        if (empty($pull_push_data) || !is_array($pull_push_data)) {
            $pull_push_data = array();
        }
        $data = (isset($pull_push_data['pull']) && is_array($pull_push_data['pull'])) ? $pull_push_data['pull'] : array();
        $push_data = (isset($pull_push_data['push']) && is_array($pull_push_data['push'])) ? $pull_push_data['push'] : array();

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
                    $period = $vv;
                    $key_str = $symbol . $period;
                    $key_min_time = $period * 60 / 20;

                    if ((!isset($push_data[$key_str]) || (isset($push_data[$key_str]) && ($now_time - $push_data[$key_str]) > $key_min_time))
                        && (1 == 1)) {
                        $push_data[$key_str] = $now_time;

                        //队列时间
                        mt_srand();
                        $time_interval = 25 + mt_rand(12, 77);
                        $time = $time + $time_interval;

                        $postdata = array(
                            'symbol' => $symbol,
                            'period' => $period,
                            'time' => $time,
                            'date' => date("Y-m-d H:i:s", $time),
                        );
                        $pipe->zAdd($key, $time, json_encode($postdata));
                    }
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
//                Loggers::getInstance("service")->warning('规定数量内没有达到条件');
                $point = 2;
                break;
            }

            $time_block = $redis->zRange($key, 0, 0, true);
            if (empty($time_block)) {
//                Loggers::getInstance("service")->warning('队列为空 || ' );
                $point = 3;
                break;
            }
            $time = array_values($time_block)[0];
            $value = array_keys($time_block)[0];

            //当前时间大于最后一个单元时间
            if ($now_time < $time) {
//                Loggers::getInstance("service")->warning('队列尾部时间大于当前时间');
                $point = 4;
                break;
            } else {
                $redis->zRem($key, $value);
            }

            $value = json_decode($value, 1);
            $symbol = $value['symbol'];
            $period = $value['period'];
            $key_str = $symbol . $period;

            //  当前时间  减去  上次请求时间   大于周期的%  1分钟=6s
            // 最小间隔时间45s
            $min_time = 45;
            $key_min_time = $period * 60 / 15;
            if ((!isset($data[$key_str]) || (isset($data[$key_str]) && ($now_time - $data[$key_str]) > $key_min_time))
                && (!isset($data['last_time']) || (isset($data['last_time']) && ($now_time - $data['last_time'] > $min_time)))) {

                $task_data = $value;
                $task_data['last_time'] = isset($data['last_time']) ? $data['last_time'] : $now_time;
                $task_data[$key_str] = isset($data[$key_str]) ? $data[$key_str] : $now_time;

                $data[$key_str] = $now_time;
                $data['last_time'] = $now_time;

                $flag = true;
                $point = 5;
                break;
            } else {
                $key_value = isset($data[$key_str]) ? $data[$key_str] : $now_time;
                $remark = $now_time - $key_value;
                $remark .= ' | ';
                $remark .= $period * 60 / 10;
//                Loggers::getInstance("service")->warning('间隔时间未达到要求 || ' . json_encode(array('$data' => $data, '$value' => $value, 'key' => $key, '$now_time' => $now_time, '$now_date' => $now_date, 'remark' => $remark)));
            }
        }

        //添加任务
        if ($flag) {
            $task_data['point'] = $point;
            $task_data['$now_time'] = $now_time;
            $task_data['$now_date'] = $now_date;

            $str = '{';
            foreach ($task_data as $k => $v) {
                $str .= '"' . $k . '": ' . '"' . $v . '", ';
            }
            $str = substr($str, 0, -2);
            $str .= "}\n";

            $client = stream_socket_client("tcp://" . MARKET_TRADE_CONNECT_IP . ":" . MARKET_TRADE_PORT);
            fwrite($client, $str);

//            Loggers::getInstance("service")->warning('达到要求-数据 ||  ' . json_encode($task_data));
        }

        $pull_push_data['pull'] = $data;
        $pull_push_data['push'] = $push_data;

        $redis->set($redis_pull_push, json_encode($pull_push_data));
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

