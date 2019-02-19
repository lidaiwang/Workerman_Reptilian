<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/12/17
 * Time: 10:45
 *
 *    开高低收
 */


class Line
{
    public $db;
    public $redis;
    public $point_num = 2;
    public $extra_point_num = 1;
    public $round_num = 4;
    protected $log;
    public $period;
    public $symbol;

    public function __construct($symbol, $period)
    {
        $db = new MysqliDb (DB_HOST, DB_NAME, DB_PSW, DB_BASE);
        $this->db = $db;

        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        if (REDIS_PSW) {
            $redis->auth(REDIS_PSW);
        }
        $this->redis = $redis;

        $this->log = Loggers::getInstance("service");

        $this->symbol = $symbol;
        $this->period = $period;

        $calculationIndex = $this->calculationIndex();
        if (!empty($calculationIndex)) {
            $this->quantificationIndex();
        }
    }

    /*
     * 利用数据进行计算指标
     *   macd   https://blog.csdn.net/daodan988/article/details/51258676
     *   ma     均线  周期  30
     */
    public function calculationIndex()
    {
        $list = array();
        $redis = $this->redis;
        $period = $this->period;
        $symbol = $this->symbol;
        $keys = 'origin:' . $symbol . ':' . $period;
        $index_keys = 'index:' . $symbol . ':' . $period;

        if (CONTINUOUS_FLAG == false) {
            $redis->del($index_keys);
        }

        //如果 两者相等就退出
        $k_count = $redis->zCard($keys);
        $index_count = $redis->zCard($index_keys);
        if ($k_count <= $index_count) {
            return $list;
        }

        //获取K线数据 开始的地方
        $begin = $index_count;
        $end = $k_count - 1;

        //取出指标最后一个单元
        $index_last = array();
        if (!empty($index_count)) {
            $index_last = $redis->zRange($index_keys, $index_count - 1, $index_count - 1);
            $index_last = json_decode($index_last[0], true);
            $index_last = array_values($index_last);
        }

        $db = $this->db;
        $msqRecord = array();
        $table_name = 'k_line_index';
        $last_block_mysql = $db
            ->where('symbol', $symbol)
            ->where('period', $period)
            ->orderBy("id", "Desc")
            ->getOne($table_name);
        $last_time_mysql = $last_block_mysql['time'];

        $last_block_redis = $redis->zRange($index_keys, -1, -1);
        $last_time_redis = 0;
        if (!empty($last_block_redis)) {
//            $last_time_redis = json_decode($last_block_redis[0], true)[0];
            $last_time_redis = json_decode($last_block_redis[0], true)['time'];
        }

        // 取出待计算的 k线数据
        $list_data = $redis->zRange($keys, $begin, $end);
        $pipe = $redis->multi(Redis::PIPELINE);
        foreach ($list_data as $k => $v) {
            $v = json_decode($v, true);

            $time = $v[0];
            $open = $v[1];
            $high = $v[2];
            $low = $v[3];
            $receive = $v[4];
            $date = $v[6];

            if (empty($index_last) && empty($list)) {
                $flag = 1;
            } elseif (empty($index_last) && !empty($list)) {
                $flag = 2;
            } else {
                $flag = 3;
            }

            switch ($flag) {
                case 1:
                    //第一次计算 没有前面指标
                    $diff = 0;
                    $dea = 0;
                    $macd = 0;
                    $ema_12 = 0;
                    $ema_26 = 0;
                    $bar = 0;
                    break;
                case 2:
                    $k_1 = array_values($list[$k - 1]);
                    if ($k_1[1] + $k_1[2] + $k_1[3] + $k_1[4] + $k_1[5] == 0) {
                        //第二次计算 且没有前面指标
                        $list_data[$k - 1] = json_decode($list_data[$k - 1], true);
                        $ema_12 = round($list_data[$k - 1][4] + ($receive - $list_data[$k - 1][4]) * 2 / 13, $this->round_num);
                        $ema_26 = round($list_data[$k - 1][4] + ($receive - $list_data[$k - 1][4]) * 2 / 27, $this->round_num);
                        $diff = round($ema_12 - $ema_26, $this->round_num);
                        $dea = round(0 + $diff * 2 / 10, $this->round_num);
                        $macd = 0;
                        $bar = round(($diff - $dea) * 2, $this->round_num);
                    } else {
                        //第二次计算 且前面有指标
                        $ema_12 = round($k_1[4] * 11 / 13 + $receive * 2 / 13, $this->round_num);
                        $ema_26 = round($k_1[5] * 25 / 27 + $receive * 2 / 27, $this->round_num);
                        $diff = round($ema_12 - $ema_26, $this->round_num);
                        $dea = round($k_1[2] * 8 / 10 + $diff * 2 / 10, $this->round_num);
                        $macd = 0;
                        $bar = round(($diff - $dea) * 2, $this->round_num);
                    }
                    break;
                default:
                    //前面有指标
                    $ema_12 = round($index_last[4] * 11 / 13 + $receive * 2 / 13, $this->round_num);
                    $ema_26 = round($index_last[5] * 25 / 27 + $receive * 2 / 27, $this->round_num);
                    $diff = round($ema_12 - $ema_26, $this->round_num);
                    $dea = round($index_last[2] * 8 / 10 + $diff * 2 / 10, $this->round_num);
                    $macd = 0;
                    $bar = round(($diff - $dea) * 2, $this->round_num);
                    break;
            }

            $arr = array(
                'time' => $time,
                'diff' => $diff,//dif
                'dea' => $dea,//dea
                'macd' => $macd,
                'ema_12' => $ema_12,
                'ema_26' => $ema_26,
                'bar' => $bar,//macd
                'date' => $date,
                'receive' => $receive,
            );
            if ($time > $last_time_mysql) {
                $msqRecord[] = array(
                    'symbol' => $symbol,
                    'period' => $period,
                    'time' => $time,
                    'date' => $date,
                    'dif' => $diff,
                    'dea' => $dea,
                    'macd' => $bar,
                    'form_data' => json_encode($arr),
                    'add_time' => date("Y-m-d H:i:s"),
                );
            }

            if ($time > $last_time_redis) {
                $pipe->zAdd($index_keys, $time, json_encode($arr));
            }

            $list[$k] = $arr;
        }
        $pipe->exec();
        $db->insertMulti($table_name, $msqRecord);

        return array_reverse($list);
    }

    /*
     *  进行量化判断
     */
    public function quantificationIndex()
    {
        //存储指标数据
        $index_data = array();
        //源高低点数据
        $high_low_data_origin = array();
        //高低点数据  因为连续两高两低是取后面点的数据
        $high_low_data = array();
        //量化后的数据 对数据进行筛选  形态结构选型
        $select_data = array();
        //源被高低点隔开的数据
        $cut_off_data_origin = array();
        //被高低点隔开的数据 被原来删掉的高低点的数据需要划到后面的键值
        $cut_off_data = array();

        $redis = $this->redis;
        $period = $this->period;
        $symbol = $this->symbol;
        $keys = 'origin:' . $symbol . ':' . $period;
        $index_keys = 'index:' . $symbol . ':' . $period;

        $leng = $redis->zCard($index_keys);
        $max_leng = 1500;
        if ($leng >= $max_leng) {
            $data = $redis->zRange($index_keys, $leng - $max_leng, $leng - 1, true);
        } else {
            $data = $redis->zRange($index_keys, 0, -1, true);
        }

        $db = $this->db;
        $msqRecord = array();
        $table_name = 'k_line_select';
        $last_block_mysql = $db
            ->where('symbol', $symbol)
            ->where('period', $period)
            ->orderBy("id", "Desc")
            ->getOne($table_name);
        $last_time_mysql = $last_block_mysql['time'];

        $symbol_new = str_replace(':', "-", $symbol);
        $subject = "{$symbol_new}_{$period}分钟_背离";
        $content = '';

        $j = -1;//指标键值自增
        $y = 0;//高低点键值自增
        foreach ($data as $k => $v) {
            $j++;
            $index_one = json_decode($k, true);
            $index_one = array_values($index_one);

            $time = $index_one[0];
            $dif = $index_one[1];
            $dea = $index_one[2];
            $macd = $index_one[6];
            $date = $index_one[7];
            $receive = $index_one[8];
            $dif_dea_avg = round(($dif + $dea) / 2, $this->round_num);

            $one_index_data = array(
                $time,
                $dif,
                $dea,
                $macd,
                $dif_dea_avg,
                $date,
                $receive,
                $dif,
                $dea,
                $macd,
            );
            $index_data[$j] = $one_index_data;

            //要求前面至少有 50 个单元
            if (isset($index_data[$j - 50])) {
                //利用前一个单元  判断交叉
                $front_one = $index_data[$j - 1];
                if ($front_one[1] == $front_one[2] || ($front_one[1] < $front_one[2] && $dif > $dea)
                    || $dif == $dea || ($front_one[1] > $front_one[2] && $dif < $dea)) {
                    $t = 1;
                }

                //判断 $dif  $dea 高低点  三点
                $high_point_flag = 0;
                $low_point_flag = 0;
                /*
                 *             &$j-3
                 *          &$j-4     & $j-2
                 *        &$j-5         &$j-1
                 *      &$j-6             & $j
                 *
                 *    $j < $j-1 && $j-3 > $j-4
                 *    $j-1 < $j-2 && $j-4 > $j-5
                 *    $j-2 < $j-3 && $j-5 > $j-6
                 *
                 *   $j-6 > $j-7
                 */
                for ($cycle_inc = 1; $cycle_inc <= $this->point_num; $cycle_inc++) {
                    ////高点
                    if (($index_data[$j - $cycle_inc + 1][4] < $index_data[$j - $cycle_inc][4] &&
                            $index_data[$j - $this->point_num - $cycle_inc + 1][4] >= $index_data[$j - $this->point_num - $cycle_inc][4])
                        || ($index_data[$j - $cycle_inc + 1][4] <= $index_data[$j - $cycle_inc][4] &&
                            $index_data[$j - $this->point_num - $cycle_inc + 1][4] > $index_data[$j - $this->point_num - $cycle_inc][4])
                    ) {
                        $high_point_flag++;
                    }

                    ////低点
                    if (($index_data[$j - $cycle_inc + 1][4] > $index_data[$j - $cycle_inc][4] &&
                            $index_data[$j - $this->point_num - $cycle_inc + 1][4] <= $index_data[$j - $this->point_num - $cycle_inc][4])
                        || ($index_data[$j - $cycle_inc + 1][4] >= $index_data[$j - $cycle_inc][4] &&
                            $index_data[$j - $this->point_num - $cycle_inc + 1][4] < $index_data[$j - $this->point_num - $cycle_inc][4])
                    ) {
                        $low_point_flag++;
                    }
                }

                //如果高低点判断  两边的判断要求是不一样的
                for ($cycle_inc = 1; $cycle_inc <= $this->extra_point_num; $cycle_inc++) {
                    if ($index_data[$j - $this->point_num * 2 - $cycle_inc + 1][4] >= $index_data[$j - $this->point_num * 2 - $cycle_inc][4]) {
                        $high_point_flag++;
                    }

                    if ($index_data[$j - $this->point_num * 2 - $cycle_inc + 1][4] <= $index_data[$j - $this->point_num * 2 - $cycle_inc][4]) {
                        $low_point_flag++;
                    }
                }

                $high_low_point_flag = false;
                $section_data = array();
                if ($high_point_flag == $this->point_num + $this->extra_point_num) {
                    $section_data = array(
                        'status' => 1,
                    );
                    $high_low_point_flag = true;
                }
                if ($low_point_flag == $this->point_num + $this->extra_point_num) {
                    $section_data = array(
                        'status' => 0,
                    );
                    $high_low_point_flag = true;
                }

                //如果是高低点
                if ($high_low_point_flag) {
                    //如果前面紧挨的一个单元也是高低点  使用后面的高低点
                    //同时也把高低点切割的数据就行重新分配 也删掉前面的数据
                    $renew_flag = true;
                    if (isset($high_low_data[$y - 1]) && $high_low_data[$y - 1]['time'] == $index_data[$j - $this->point_num][0] - $period * 60) {
                        if (isset($cut_off_data[$high_low_data[$y - 1]['time']])) {
                            $cut_off_data[$index_data[$j - $this->point_num][0]] = $cut_off_data[$high_low_data[$y - 1]['time']];
                            unset($cut_off_data[$high_low_data[$y - 1]['time']]);
                            $renew_flag = false;
                        }
                        unset($high_low_data[$y - 1]);
                    }
                    //去掉两个相邻的同向的高低点
                    //同时也把高低点切割的数据就行重新分配 也删掉前面的数据
                    if (isset($high_low_data[$y - 1]) && $high_low_data[$y - 1]['status'] == $section_data['status']) {
                        if (isset($cut_off_data[$high_low_data[$y - 1]['time']])) {
                            $cut_off_data[$index_data[$j - $this->point_num][0]] = $cut_off_data[$high_low_data[$y - 1]['time']];
                            unset($cut_off_data[$high_low_data[$y - 1]['time']]);
                            $renew_flag = false;
                        }
                        unset($high_low_data[$y - 1]);
                    }

                    //获取高低点的数据
                    $high_low_data[$y] = array_merge(array(
                        'dif_dea_avg' => $index_data[$j - $this->point_num][4],
                        'time' => $index_data[$j - $this->point_num][0],
                        'date' => $index_data[$j - $this->point_num][5],
                        'receive' => $index_data[$j - $this->point_num][6],
                        'dif' => $index_data[$j - $this->point_num][7],
                        'dea' => $index_data[$j - $this->point_num][8],
                        'macd' => $index_data[$j - $this->point_num][9],
                    ), $section_data);
                    $high_low_data_origin[$y] = $high_low_data[$y];
                    $y++;

                    //被高低点分割的数据进行还原 因为高低点判断的
                    // 上一个切割的数据后几位是当前数据的前几位
                    $serialization_high_low_data = array_values($high_low_data);
                    $serialization_high_low_num = count($serialization_high_low_data);
                    //至少有三个高低点数据
                    if (isset($serialization_high_low_data[$serialization_high_low_num - 3]) && isset($cut_off_data[$serialization_high_low_data[$serialization_high_low_num - 3]['time']])) {
                        //因为先有高低点  后有高低点分割的数据 分割数据长度比高低点数据短一
                        if (count($cut_off_data[$serialization_high_low_data[$serialization_high_low_num - 3]['time']]) > 3 && $renew_flag == true) {
                            //还原  把前一个数组的后 n 个元素   还原到现在的这个数组的前面去
                            for ($cycle_inc = 0; $cycle_inc < $this->point_num; $cycle_inc++) {
                                $pop = array_pop($cut_off_data[$serialization_high_low_data[$serialization_high_low_num - 3]['time']]);
                                array_unshift($cut_off_data[$serialization_high_low_data[$serialization_high_low_num - 2]['time']], $pop);
                                if ($cycle_inc == $this->point_num - 1) {
                                    array_push($cut_off_data[$serialization_high_low_data[$serialization_high_low_num - 3]['time']], $pop);
                                }
                            }
                        }
                    }

                    if ($serialization_high_low_num > 2) {
                        $select_flag = true;
                        //进行一些判断 123长短的判断
                        if (abs($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg']) > 0 &&
                            abs($serialization_high_low_data[$serialization_high_low_num - 1]['dif_dea_avg']) / abs($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg']) <= 2 / 3 &&
                            abs($serialization_high_low_data[$serialization_high_low_num - 1]['dif_dea_avg']) / abs($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg']) >= 1 / 5 &&
                            abs($serialization_high_low_data[$serialization_high_low_num - 2]['dif_dea_avg']) / abs($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg']) <= 1 / 3 &&
                            abs($serialization_high_low_data[$serialization_high_low_num - 2]['dif_dea_avg']) < abs($serialization_high_low_data[$serialization_high_low_num - 1]['dif_dea_avg'])
                        ) {

                        } else {
                            $select_flag = false;
                        }

                        //中间的单元和中间单元的前一个单元的位置判断 正负大小关系
                        if (($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] < 0 && ($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] + $serialization_high_low_data[$serialization_high_low_num - 2]['dif_dea_avg'] <= $serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] || ($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] + $serialization_high_low_data[$serialization_high_low_num - 2]['dif_dea_avg'] >= $serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] && abs($serialization_high_low_data[$serialization_high_low_num - 2]['dif_dea_avg']) / abs($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg']) <= 1 / 4))) ||
                            ($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] > 0 && ($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] + $serialization_high_low_data[$serialization_high_low_num - 2]['dif_dea_avg'] >= $serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] || ($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] + $serialization_high_low_data[$serialization_high_low_num - 2]['dif_dea_avg'] <= $serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg'] && abs($serialization_high_low_data[$serialization_high_low_num - 2]['dif_dea_avg']) / abs($serialization_high_low_data[$serialization_high_low_num - 3]['dif_dea_avg']) <= 1 / 4)))
                        ) {

                        } else {
                            $select_flag = false;
                        }

                        //判断高低点之间的连续
                        if (isset($serialization_high_low_data[$serialization_high_low_num - 4]) && isset($cut_off_data[$serialization_high_low_data[$serialization_high_low_num - 4]['time']])) {

                            // 这个3是筛选 类型决定的  不用改
                            for ($cycle_inc = 0; $cycle_inc < 3; $cycle_inc++) {
                                $parameter = $cut_off_data[$serialization_high_low_data[$serialization_high_low_num - $cycle_inc - 2]['time']];
                                $parameter_count = count($parameter);
                                //尾部最后单元是没有进行 高低点数据还原的
                                if ($cycle_inc == 0) {
                                    for ($cycle_ = 1; $cycle_ <= $this->point_num; $cycle_++) {
                                        unset($parameter[$parameter_count - $cycle_]);
                                    }
                                }

                                $dif_dea_avg_list = array_column($parameter, 4);
                                $max = max($dif_dea_avg_list);
                                $min = min($dif_dea_avg_list);
                                $begin_point = $parameter[0][4];
                                $parameter_count = count($parameter);
                                $end_point = $parameter[$parameter_count - 1][4];
                                if ($begin_point > $end_point) {
                                    $point_max = $begin_point;
                                    $point_min = $end_point;
                                } else {
                                    $point_max = $end_point;
                                    $point_min = $begin_point;
                                }

                                if ($max > $point_max || $min < $point_min) {
                                    $select_flag = false;
                                    break;
                                }
                            }

                        }

                        //还可以进行趋势判断  如30日均线
                        $one_select_data = $serialization_high_low_data[$serialization_high_low_num - 1];
                        if ($select_flag) {
                            $select_data[] = $one_select_data;

                            if ($one_select_data['time'] > $last_time_mysql) {
                                $spu = array(
                                    //源高低点数据
                                    'high_low_data_origin' => $high_low_data_origin,
                                    ////高低点数据  因为连续两高两低是取后面点的数据
                                    'high_low_data' => $high_low_data,
                                    //源被高低点隔开的数据
                                    'cut_off_data_origin' => $cut_off_data_origin,
                                    //被高低点隔开的数据 被原来删掉的高低点的数据需要划到后面的键值
                                    'cut_off_data' => $cut_off_data,
                                    'index_data' => $index_data,
                                );
                                $add_time = date("Y-m-d H:i:s");
                                $msqRecord[] = array(
                                    'symbol' => $symbol,
                                    'period' => $period,
                                    'time' => $one_select_data['time'],
                                    'date' => $one_select_data['date'],
                                    'status' => 0,
                                    'status_name' => '高低点前后两三角形背离',
                                    'intr' => "0",
                                    'add_time' => $add_time,
                                    'form_data' => json_encode($spu),
                                );

                                $position = $one_select_data['status'] == 1 ? "高点" : "低点";
                                $content .= " 当前时间：{$add_time}; <br/> 预警点：{$one_select_data['date']};<br/> 预警点价格是:{$one_select_data['receive']};<br/> 预警处于{$position} 。 ";
                                $content .= "<br/>";
                                $content .= "<br/>";
                            }
                        }

                    }
                }

                //利用高低点分割数据
                $serialization_high_low_data = array_values($high_low_data);
                $serialization_high_low_num = count($serialization_high_low_data);
                if (isset($serialization_high_low_data[$serialization_high_low_num - 1]['time'])) {
                    $cut_off_data[$serialization_high_low_data[$serialization_high_low_num - 1]['time']][] = $one_index_data;
                    $cut_off_data_origin[$serialization_high_low_data[$serialization_high_low_num - 1]['time']][] = $one_index_data;
                }
            }
        }

        if (!empty($content) && CONTINUOUS_FLAG) {
            $sendEmail = new SendEmail();
            $sendEmail->send_(SEND_EMAIL, $subject, $content);
        }
        $db->insertMulti($table_name, $msqRecord);

        $spu = array(
            'select_data' => $select_data,
            //源高低点数据
            'high_low_data_origin' => $high_low_data_origin,
            ////高低点数据  因为连续两高两低是取后面点的数据
            'high_low_data' => $high_low_data,
            //源被高低点隔开的数据
            'cut_off_data_origin' => $cut_off_data_origin,
            //被高低点隔开的数据 被原来删掉的高低点的数据需要划到后面的键值
            'cut_off_data' => $cut_off_data,
            'index_data' => $index_data,
        );
//        $this->log->warning("日志", -1, json_encode($spu));

        return $select_data;
    }

}





















