<?php

class checkMarketTrading
{

    private $num = 0;
    private $cycleList = array(5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 4320, 10080, 43200);

    /*
     * okexbtc:*   开高低收
     * okexbtcindex:*  指标 ema12 emm26 dif dea macd
     * okex_incr  程序每次自增一
     * okex_flag  最新程序运行时间
     * okexbtcindexavg   指标平均
     * email 邮件
     */
    public function __construct()
    {
//        $this->redis = new Redis();
//        $this->redis->connect(REDIS_HOST, REDIS_PORT);
//        $this->redis->auth(REDIS_PSW);

        require_once PATH . '/lib/SendEmail.php';
        $email = new SendEmail();
        $this->email = $email;
        $this->notice_email = NOTICE_EMAIL;
    }

    public function loopRun()
    {
        /*  5分   10分  15  30  60  120   240  360   12*60   一天  3天  一周   一月
         *                                            720    1440  4320 10080   2592000
         */
        $client = stream_socket_client("tcp://" . MARKET_TRADE_CONNECT_IP . ":" . MARKET_TRADE_PORT);
        $cycleList = $this->cycleList;

        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        $redis->select(1);

        $notice = $redis->get('notice');
        $count = count($cycleList) - 1;
        if (empty($notice) || $notice > $count) {
            $flag = 0;
        } else {
            $flag = $notice;
        }
        $redis->set('notice', $flag + 1);
        $v = $cycleList[$flag];
        fwrite($client, '{"coin": ' . '"' . $v . '", "cycle": ' . $v . ', "last_trade_list": ' . $v . "}\n");

    }


    // 计算指标数据
    public function loop()
    {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        $redis->select(1);
        $cycleList = $this->cycleList;

        //循环所以的周期
        foreach ($cycleList as $k => $v) {
            //计算已经有的指标长度
            $index_count = $redis->zCard('okexbtcindex:' . $v);
            // 计算 K线数据长度
            $k_count = $redis->zCard('okexbtc:' . $v);

            //如果 两者相等就退出
            if ($index_count == $k_count) {
                continue;
            }

            // 计算两者的差值
            $difference_count = $k_count - $index_count;
            //获取K线数据 开始的地方
            $begin = $index_count;

            if ($difference_count >= 500) {
                $end = $begin + 500 - 1;
            } else {
                $end = $k_count - 1;
            }

            // 取出待计算的 k线数据
            $list_data = $redis->zRange('okexbtc:' . $v, $begin, $end);

            $x = $end - $begin + 1;
            $incr = $redis->hIncrBy('okexbtcindexavg', 'index_num:' . $v, $x);

            $dif_avg = $redis->hGet('okexbtcindexavg', 'index_dif:' . $v);
            $dea_avg = $redis->hGet('okexbtcindexavg', 'index_dea:' . $v);
            $macd_avg = $redis->hGet('okexbtcindexavg', 'index_macd:' . $v);

            $dif_avg = empty($dif_avg) ? 0 : $dif_avg;
            $dea_avg = empty($dea_avg) ? 0 : $dea_avg;
            $macd_avg = empty($macd_avg) ? 0 : $macd_avg;

            $dif_ = 0;
            $dea_ = 0;
            $macd_ = 0;

            foreach ($list_data as $kk => $vv) {
                //取出本段的收盘价
                $k_arr = explode('|', $vv);
                $shou = $k_arr[4];
                //本周期的时间
                $time__ = $k_arr[0];

                //取上一个周期的ema12  ema26
                $list = $redis->zRange('okexbtcindex:' . $v, $index_count + $kk - 1, $index_count + $kk - 1);

                // 这是第一次进入时  没有上一个收盘价
                if (empty($list)) {
                    //本周期的 ema 12  em 26 dea
                    $ema_12 = 0;
                    $ema_26 = 0;
                    $dif = 0;
                    $dea = 0;
                    $macd = 0;
                } else {
                    $ii = explode('|', $list[0]);
                    // 第二次进入  有收盘价  但是没有 上一个周期的指标数据
                    if (empty($ii[0]) && empty($ii[1]) && empty($ii[2]) && empty($ii[4])) {
                        // 取上一次周期的收盘价
                        // $list_shang = $redis->zRange('okexbtc:' . $v, $index_count + $kk -1, $index_count + $kk-1);
                        $list_shang = $list_data[$kk - 1];
                        $k_arr_shang = explode('|', $list_shang);
                        $shou_shang = $k_arr_shang[4];

                        $ema_12 = $shou_shang + ($shou - $shou_shang) * 2 / 13;
                        $ema_26 = $shou_shang + ($shou - $shou_shang) * 2 / 27;
                        $dif = $ema_12 - $ema_26;
                        $dea = 0 + $dif * 2 / 10;
                        $macd = 2 * ($dif - $dea);
                    } else {
                        $ema_12_shang = $ii[0];
                        $ema_26_shang = $ii[1];
                        $dea_shang = $ii[3];

                        $ema_12 = $ema_12_shang * 11 / 13 + $shou * 2 / 13;
                        $ema_26 = $ema_26_shang * 25 / 27 + $shou * 2 / 27;
                        $dif = $ema_12 - $ema_26;
                        $dea = $dea_shang * 8 / 10 + $dif * 2 / 10;
                        $macd = 2 * ($dif - $dea);
                    }
                }

                //存储本周期的指标数据 round($num,2);
                $str_index = [];
                $str_index[] = round($ema_12, 5);
                $str_index[] = round($ema_26, 5);
                $str_index[] = round($dif, 5);
                $str_index[] = round($dea, 5);
                $str_index[] = round($macd, 5);
                $str_index[] = $time__;

                $dif_ += abs($dif);
                $dea_ += abs($dea);
                $macd_ += abs($macd);

                $redis->zAdd('okexbtcindex:' . $v, $time__, implode('|', $str_index));
            }

            $dif_avg_ = ($dif_avg * ($incr - $x) + $dif_) / $incr;
            $dea_avg_ = ($dea_avg * ($incr - $x) + $dea_) / $incr;
            $macd_avg_ = ($macd_avg * ($incr - $x) + $macd_) / $incr;

            $redis->hSet('okexbtcindexavg', 'index_dif:' . $v, round($dif_avg_, 5));
            $redis->hSet('okexbtcindexavg', 'index_dea:' . $v, round($dea_avg_, 5));
            $redis->hSet('okexbtcindexavg', 'index_macd:' . $v, round($macd_avg_, 5));
        }
    }


    /*
    * okexbtc:*   开高低收
    * okexbtcindex:*  指标 ema12 emm26 dif dea macd
    * okex_incr  程序每次自增一
    * okex_flag  最新程序运行时间
    * okexbtcindexavg   指标平均
    * email 邮件
    */
    public function loop2()
    {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        $redis->select(1);
        $cycleList = $this->cycleList;
    }


    // 与前面的柱子面积进行比较  判断背驰
    public function loop1()
    {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        $redis->select(1);
        $cycleList = $this->cycleList;
        // $cycleList = [5];


        // echo  $num . ' ' . date('Y-m-d H:i:s',time()) . "\n";

        foreach ($cycleList as $kkkk1 => $v) {

            $redis_num = $redis->zCard('okexbtcindex:' . $v);
            $num_ = $redis->hGet('okexbtcindex_loop', $v);
            if ($num_ == $redis_num) {
                continue;
            } else {
                $num = $redis->incr('okex_incr');
                echo $num . ' _ ';
                $redis->hSet('okexbtcindex_loop', $v, $redis_num);
            }

            //倒序取出  200条k线  包含  $ema_12 $ema_26  $dif $dea $macd
            $diff = 199;
            $list = $redis->ZREVRANGE('okexbtcindex:' . $v, 0, $diff);

            // 取出对应等级的  平均值   是绝对值
            $okexbtcindexavg = $redis->hgetall('okexbtcindexavg');
            $index_dif = $okexbtcindexavg['index_dif:' . $v];
            $index_dea = $okexbtcindexavg['index_dea:' . $v];
            $index_macd = $okexbtcindexavg['index_macd:' . $v];

            $b[] = $index_dif;
            $b[] = $index_dea;
            $b[] = $index_macd;

            //  交换 符号 递增
            $n = 0;
            // 记录上一次的符号
            $s = 0;
            //  每一小段的具体 macd 的心
            $macd_order = [];
            // 每一个小段的平均值  最大 最小  面积  平均值
            $i = [];

            //柱子变 长则退出
            $aa0 = explode('|', $list[0]);
            $aa1 = explode('|', $list[1]);
            if (abs($aa0[4]) > abs($aa1[4])) {
                echo date('Y-m-d H:i:s', $aa0[5]) . ' _  ' . $v . ' _  ' . "$aa0[4] > $aa1[4] 柱子在变长" . "\n";
                continue;
            }

            //循环200 条  K线数据进行分类
            foreach ($list as $key => $value) {
                $x = explode('|', $value);

                $macd = $x[4];
                // 判断这一次属于 什么正负
                if ($macd > 0) {
                    $flag = '+';
                } else {
                    $flag = '-';
                }

                // 第二次进入
                if ($s !== 0 && $s !== $flag) {
                    $max = max($macd_order[$n]);
                    if ($max < 0) {
                        $max = min($macd_order[$n]);
                    }
                    $min = min($macd_order[$n]);
                    $sum = array_sum($macd_order[$n]);
                    $num = count($macd_order[$n]);

                    $i[$n][] = $max;
                    $i[$n][] = $min;
                    $i[$n][] = $sum;
                    $i[$n][] = $num;
                    $i[$n][] = round($sum / $num, 5);

                    $n++;
                }
                // 记录上一次的 正负
                $s = $flag;

                $macd_order[$n][] = $macd;
                $order[$n][] = $x;

                if ($key >= $diff) {
                    $max = max($macd_order[$n]);
                    if ($max < 0) {
                        $max = min($macd_order[$n]);
                    }
                    $min = min($macd_order[$n]);
                    $sum = array_sum($macd_order[$n]);
                    $num = count($macd_order[$n]);

                    $i[$n][] = $max;
                    $i[$n][] = $min;
                    $i[$n][] = $sum;
                    $i[$n][] = $num;
                    $i[$n][] = round($sum / $num, 5);
                }
            }

            //取最新的一个类型的柱子
            $new_max = $i[0][0];
//            $new_min = $i[0][1];
            $new_sum = $i[0][2];
            $new_num = $i[0][3];
//            $new_avg = $i[0][4];

            //取第一个方块  如果最新的柱子离 最高的哪根 超过两根时 则退出
            $ll0 = $macd_order[0];
            $keys_ = array_search($new_max, $ll0);
            $uu = count($macd_order[0]);
            $diee = $uu - $keys_;

            $x_ = explode('|', $list[0]);
            $macd11 = $x_[4];

            if ($keys_ > 1) {
                echo date('Y-m-d H:i:s', $aa0[5]) . ' _ ' . $v . "大：$new_max 在 $diee ,现在 $macd11 第$uu 根   柱子离最高的 超过预期" . "\n";
                continue;
            }

            if ($new_num < 4) {
                echo date('Y-m-d H:i:s', $aa0[5]) . ' _ ' . $v . "/ $new_num 柱子太少" . "\n";
                continue;
            }

            $k0 = 0;
            while (true) {
                if ($k0 > 7) {
                    echo date('Y-m-d H:i:s', $aa0[5]) . ' _ ' . $v . '/' . $k0 / 2 . ' 前面没有合适的对比' . "\n";
                    break;
                }
                $new_max_ = $i[$k0][0];
                $new_min_ = $i[$k0][1];
                $new_sum_ = $i[$k0][2];
                $new_num_ = $i[$k0][3];
                $new_avg_ = $i[$k0][4];

                $k0 = $k0 + 2;

                // 高点降低  面积减少  数量减少
                if ($new_num_ < 7 || abs($new_max_) < abs($new_max) || abs($new_sum_) < abs($new_sum) * 2 || $new_num_ < $new_num * 2) {
                    echo date('Y-m-d H:i:s', $aa0[5]) . '__ ' . $v . '/' . $k0 / 2 . ' __' . implode('|', $i[0]) . '  __  ' . implode('|', $i[$k0]) . "\n";
                    continue;
                }

                $e = [];
//                $list1 = $redis->ZREVRANGE('okexbtc:' . $v, 0, 0)[0];
                $list1 = $redis->ZREVRANGEBYSCORE('okexbtc:' . $v, $aa0[5], $aa0[5])[0];
                $e[] = date('Y-m-d H:i:s', $aa0[5]);
                $e[] = $v;
                $e[] = explode('|', $list1)[4];


                if ($v >= 15) {
                    require_once PATH . '/lib/SendEmail.php';
                    $email = new SendEmail();
                    if ($aa0[4] > 0) {
                        $mn = '空';
                    } else {
                        $mn = '多';
                    }

                    if ($v >= 60 * 24) {
                        $v = $v / (60 * 24) . '天';
                    } elseif ($v >= 60) {
                        $v = $v / 60 . '小时';
                    }
                    $subject = $v . '_背离提醒_' . $mn;
                    $email->send_('1311684648@qq.com', $subject, implode(" | ", $e));
                }


                $redis->zadd('email', $aa0[5], implode(" | ", $e));
                echo "ok" . "\n";
                echo date('Y-m-d H:i:s', $aa0[5]) . ' _ ' . $v . '/' . $k0 / 2 . ' _ ' . implode("|", $e) . "\n";
                break;
            }
        }
    }

}


