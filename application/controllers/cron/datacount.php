<?php
/**
 * author: easywed
 * createTime: 15/11/11 11:02
 * description:易结网数据统计
 */
set_time_limit(0);
class Datacount extends Base_Controller
{
    private $_excel_storage = 'logs/';
    private $_md5_key = 'Ys6MNpKue2yBQM56rOcaZcAYX9FCW4YA';

    public function __construct()
    {
        parent::__construct();

        $this->load->model('business/business_model' , 'business');
        $this->load->model("business/common_model" , 'buscommon');
        $this->load->model('business/business_shop_map_model' , 'shopmap');
        $this->load->model('sys_user_model', 'sum');
        $this->load->model('system/department_model', 'sdm');

        $this->load->helper('excel_tools');

        $this->load->library('email');
    }

    public function process()
    {
        $params = $this->input->get();
        $action = $params['action'];
        $taskid = $params['taskid'];
        $task_time = $params['tasktime'];
        $taskcode = $params['taskcode'];
        if(md5($taskid . $task_time . $this->_md5_key) != $taskcode)
        {
            die('TOKEN ERROR');
        }

        if(time() - $task_time > 300)
        {
            die('EXPIRED');
        }

        switch($action)
        {
            case 'sendemail_week':
                $html_msg = $h1 = $h2 = $h3 = $h4 = $h5 = $h6 = $h7 = $h8 = $h9 = '';
                $files = array();
                $files[] = $this->_business_process2(2, $h1);
                $html_msg .= $h1;
                $files[] = $this->_adviser_follow_total(2, $h2);
                $html_msg .= $h2;
                $files[] = $this->_adviser_process(2, $h3);
                $html_msg .= $h3;
                $files[] = $this->_customer_parted(2, $h4);
                $html_msg .= $h4;
                //$files[] = $this->_shop_order(2, $h5);
                //$html_msg .= $h5;
                $files[] = $this->_business_transform(2, $h6);
                $html_msg .= $h6;
                $files[] = $this->_reason_analysis(2, $h7);
                $html_msg .= $h7;
                $files[] = $this->_customer_parted_updatetime(2, $h8);
                $html_msg .= $h8;
                $files[] = $this->_business_process_updatetime(2, $h9);
                $html_msg .= $h9;

                $this->_send_email($files, $html_msg , 'week');
                break;
            case 'sendemail_month':
                $html_msg = $h1 = $h2 = $h3 = $h4 = $h5 = $h6 = $h7 = $h8 = $h9 ='';
                $files = array();
                $files[] = $this->_business_process2(3, $h1);
                $html_msg .= $h1;
                $files[] = $this->_adviser_follow_total(3, $h2);
                $html_msg .= $h2;
                $files[] = $this->_adviser_process(3, $h3);
                $html_msg .= $h3;
                $files[] = $this->_customer_parted(3, $h4);
                $html_msg .= $h4;
                //$files[] = $this->_shop_order(3, $h5);
                //$html_msg .= $h5;
                $files[] = $this->_business_transform(3, $h6);
                $html_msg .= $h6;
                $files[] = $this->_reason_analysis(3, $h7);
                $html_msg .= $h7;
                $files[] = $this->_customer_parted_updatetime(3, $h8);
                $html_msg .= $h8;
                $files[] = $this->_business_process_updatetime(3, $h9);
                $html_msg .= $h9;

                $this->_send_email($files, $html_msg , 'month');
                break;
            case 'sendemail_day':
                $html_msg = $h1 = $h2 = $h3 = $h4 = $h5 = '';
                $files = array();
                $files[] = $this->_adviser_process(1, $h1);
                $html_msg .= $h1;
                $files[] = $this->_business_transform(1, $h2);
                $html_msg .= $h2;
                $files[] .= $this->_business_process_updatetime(1, $h3);
                $html_msg .= $h3;
                $this->_send_email($files , $html_msg , 'day');
                break;
            case 'sendemail_today':
                $html_msg = $h1 = $h2 = '';
                $files = array();
                $files[] .= $this->_day_follow_customer(1, $h1);
                $html_msg .= $h1;
                $files[] .= $this->_day_build_customer(1, $h2);
                $html_msg .= $h2;
                $this->_send_email($files , $html_msg , 'day' , 2);
                break;
            case 'sendemail':
                break;
        }
    }

    /**
     * 商家分单统计
     */
    private function _shop_order($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $usertypes = $this->business->getUserType();

        //获取策划师列表
        $shop_list = $this->buscommon->shoperInfo(array('page' => 1 , 'pagesize' => 1000));

        //获取已分单的商机列表
        $dsql = 'SELECT a.shop_id,a.status as s_status,a.face_status,a.bid,b.usertype,b.trade_status,b.createtime,b.ordertime,b.status as b_status'.
            ' FROM ew_business_shop_map a LEFT JOIN ew_business b ON a.bid=b.id WHERE b.is_test = 0 AND b.ordertime >= '. $begin_time .' AND b.ordertime<= '. $end_time;

        $result = array();
        $shop_sub_info = array(
            'order_num' => 0, //分单量
            'A+_num' => 0, //A+类客户量
            'A_num' => 0, //A类客户量
            'B+_num' => 0, //B+类客户量
            'B_num' => 0, //B类客户量
            'C_num' => 0, //C类客户量
            'D_num' => 0, //D类客户量
            'A+_order_num' => 0, //A+类成单量
            'A_order_num' => 0, //A类成单量
            'B+_order_num' => 0, //B+类成单量
            'B_order_num' => 0, //B类成单量
            'C_order_num' => 0, //C类成单量
            'D_order_num' => 0, //D类成单量
            'A+_face_num' => 0, //A+类见面量
            'A_face_num' => 0, //A类见面量
            'B+_face_num' => 0, //B+类见面量
            'B_face_num' => 0, //B类见面量
            'C_face_num' => 0, //C类见面量
            'D_face_num' => 0, //D类见面量
            'a_b_order_rate' => 0, //A+B类成单率
            'c_d_order_rate' => 0, //C+D类成单率
            'a_b_face_rate' => 0, //A+B类见面率
            'c_d_face_rate' => 0 //C+D类见面率
        );
        foreach($shop_list['rows'] as $shop)
        {
            $result[$shop['uid']] = array('nickname' => $shop['realname'] , 'shopname' => $shop['studio_name'] , 'mobile' => $shop['phone'] ,'sub_info' => $shop_sub_info);
        }
        $dquery = $this->erp_conn->query($dsql);
        foreach($dquery->result_array() as $row)
        {
            if(!isset($result[$row['shop_id']]) || $row['b_status'] != $bstatus['parted']) continue;
            //分单量
            $result[$row['shop_id']]['sub_info']['order_num']++;

            if(!empty($row['usertype']))
            {
                //客户量
                $result[$row['shop_id']]['sub_info'][$row['usertype'].'_num']++;
                //成单量
                if($row['s_status'] == Business_shop_map_model::STATUS_SIGN)
                {
                    $result[$row['shop_id']]['sub_info'][$row['usertype'].'_order_num']++;
                }
                //见面量
                if($row['face_status'] == Business_shop_map_model::FACE_STATUS_MEET)
                {
                    $result[$row['shop_id']]['sub_info'][$row['usertype'].'_face_num']++;
                }
            }
        }

        $file_name = APPPATH . '/logs/' . 'shop_order_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $table_title = '商家分单统计[shop_order]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '商家呢称','店铺名称','商家手机','分单量','A+类客户量','A类客户量','B+类客户量',
            'B类客户量','C类客户量','D类客户量','A+类成单量','A类成单量','B+类成单量','B类成单量',
            'C类成单量','D类成单量','A&B类成单率','C&D类成单率','A&B类见面率','C&D类见面率'
        );
        $exporter->addRow(
            $output[0]
        );
        foreach($result as $val)
        {
            $a_b_order_total = $val['sub_info']['A+_num'] + $val['sub_info']['A_num'] + $val['sub_info']['B+_num'] + $val['sub_info']['B_num'];
            $c_d_order_total = $val['sub_info']['C_num'] + $val['sub_info']['D_num'];

            $a_b_order_rate = $a_b_order_total > 0 ? ($val['sub_info']['A+_order_num'] + $val['sub_info']['A_order_num'] + $val['sub_info']['B+_order_num'] + $val['sub_info']['B_order_num']) / $a_b_order_total : 0;
            $c_d_order_rate = $c_d_order_total > 0 ? ($val['sub_info']['C_order_num'] + $val['sub_info']['D_order_num']) / $c_d_order_total : 0;

            $a_b_face_rate = $a_b_order_total > 0 ? ($val['sub_info']['A+_face_num'] + $val['sub_info']['A_face_num'] + $val['sub_info']['B+_face_num'] + $val['sub_info']['B_face_num']) / $a_b_order_total : 0;
            $c_d_face_rate = $c_d_order_total > 0 ? ($val['sub_info']['C_face_num'] + $val['sub_info']['D_face_num']) / $c_d_order_total : 0;

            $tmpdata = array(
                $val['nickname'] , $val['shopname'] , $val['mobile'], $val['sub_info']['order_num'], $val['sub_info']['A+_num'],
                $val['sub_info']['A_num'] , $val['sub_info']['B+_num'] , $val['sub_info']['B_num'] , $val['sub_info']['C_num'],
                $val['sub_info']['D_num'] , $val['sub_info']['A+_order_num'] , $val['sub_info']['A_order_num'] , $val['sub_info']['B+_order_num'],
                $val['sub_info']['B_order_num'] , $val['sub_info']['C_order_num'] , $val['sub_info']['D_order_num'] , 100*$a_b_order_rate.'%',
                100*$c_d_order_rate.'%',100*$a_b_face_rate.'%',100*$c_d_face_rate.'%'
            );
            $output[] = $tmpdata;
            $exporter->addRow($tmpdata);
        }
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 商机转换率分析
     */
    private function _business_transform($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $bsource_flip = array_flip($bsource);
        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other'], $bstatus['garbage_repeat'], $bstatus['3days_ago']);
        $parted_status = array($bstatus['parted'], $bstatus['parted_n_4']);

        $dsql = 'SELECT id,source,status,trade_status,createtime,is_sign,hmsr FROM ew_business WHERE is_test = 0 AND createtime >=' . $begin_time . ' AND createtime <= ' . $end_time;
        $dquery = $this->erp_conn->query($dsql);

        $result = array();
        foreach($dquery->result_array() as $row)
        {
            if(empty($row['hmsr']))continue;
            //$t_hmsr = explode('_' , $row['hmsr']);
            //$row['hmsr'] = $t_hmsr[0];

            //新增商机数量
            isset($result[$row['hmsr']]['new_record']) ? $result[$row['hmsr']]['new_record']++ : $result[$row['hmsr']]['new_record'] = 1;

            //无效商机数量
            if(in_array($row['status'], $invalid_status))
            {
                isset($result[$row['hmsr']]['invalid']) ? $result[$row['hmsr']]['invalid']++ : $result[$row['hmsr']]['invalid'] = 1;
            }

            //建单数量
            if(in_array($row['status'], array($bstatus['build'] , $bstatus['parted'] , $bstatus['parted_n_4'])) || $row['trade_status'] > 0)
            {
                isset($result[$row['hmsr']]['build']) ? $result[$row['hmsr']]['build']++ : $result[$row['hmsr']]['build'] = 1;
            }

            //分单数量
            if(in_array($row['status'],$parted_status) || $row['trade_status'] > 0)
            {
                isset($result[$row['hmsr']]['parted']) ? $result[$row['hmsr']]['parted']++ : $result[$row['hmsr']]['parted'] = 1;
            }

            //成单数量
            if($row['trade_status'] == $tstatus['ordered'] || $row['is_sign'] == 1)
            {
                isset($result[$row['hmsr']]['ordered']) ? $result[$row['hmsr']]['ordered']++ : $result[$row['hmsr']]['ordered'] = 1;
            }

            //重复数量
            if($row['status'] == $bstatus['garbage_repeat'] || $row['is_del'] == 1)
            {
                isset($result[$row['hmsr']]['repeat']) ? $result[$row['hmsr']]['repeat']++ : $result[$row['hmsr']]['repeat'] = 1;
            }
        }

        $file_name = APPPATH . '/logs/' . 'business_transform_' . date('Y-m-d', $end_time) . '.xls';
        $output = array();
        $table_title = '商家转换率分析[business_transform]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '商机来源','提交商机数量','无效商机数量','建单数量','分单数量',
            '成单数量','重复数量','无效商机率','建单率','分单率','成单率'
        );
        $exporter->addRow(
            $output[0]
        );
        foreach($result as $rkey => $val)
        {
            $t_new_record = isset($val['new_record']) ? $val['new_record'] : 0;
            $t_invalid = isset($val['invalid']) ? $val['invalid'] : 0;
            $t_build = isset($val['build']) ? $val['build'] : 0;
            $t_parted = isset($val['parted']) ? $val['parted'] : 0;
            $t_ordered = isset($val['ordered']) ? $val['ordered'] : 0;
            $t_repeat = isset($val['repeat']) ? $val['repeat'] : 0;
            $tmpdata = array(
                $rkey, $t_new_record , $t_invalid , $t_build, $t_parted , $t_ordered , $t_repeat,
                $t_new_record > 0 ? (round($t_invalid / $val['new_record'] * 100 , 2)) . '%' : '0%',
                $t_new_record > 0 ? (round($t_build / $val['new_record'] * 100 , 2)) . '%' : '0%',
                $t_new_record > 0 ? (round($t_parted / $val['new_record'] * 100 , 2)) . '%' : '0%',
                $t_new_record > 0 ? (round($t_ordered / $val['new_record'] * 100 , 2)) . '%' : '0%',
            );
            $output[] = $tmpdata;
            $exporter->addRow($tmpdata);
        }
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 顾问累计处理情况统计
     * @param int $cycle 1日，2周，3月
     */
    private function _adviser_follow_total($cycle = 1 , &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $bsource_flip = array_flip($bsource);

        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other'], $bstatus['garbage_repeat'], $bstatus['3days_ago']);
        $total_follow_status = array($bstatus['newadd'], $bstatus['follow_next']);
        $total_build_status = array($bstatus['build'], $bstatus['parted']);
        $total_close_status = array($tstatus['invalid']);
        $parted_status = array($bstatus['parted'], $bstatus['parted_n_4']);

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime FROM ew_business WHERE is_test = 0 AND advisertime >=' . $begin_time . ' AND advisertime <= '. $end_time .' AND follower_uid > 0';
        $dquery = $this->erp_conn->query($dsql);

        $department = $this->sdm->getWedAdviser();
        $adviser = $this->sum->findAll(array('department' => $department['id']), array(), array());

        $result = array();
        $total_nums = $total_invalid_nums = $total_follow_nums = $total_build_nums = $total_parted_nums = $total_ordered_nums = $total_discard_nums = $total_closed_nums = 0;
        foreach($dquery->result_array() as $row)
        {
            $total_nums++;
            //总分配数据
            isset($result[$row['follower_uid']]['total_allocate']) ? $result[$row['follower_uid']]['total_allocate']++ : $result[$row['follower_uid']]['total_allocate'] = 1;
            //累计无效客户
            if(in_array($row['status'], $invalid_status))
            {
                $total_invalid_nums++;
                isset($result[$row['follower_uid']]['invalid']) ? $result[$row['follower_uid']]['invalid']++ : $result[$row['follower_uid']]['invalid'] = 1;
            }
            //累计跟进中
            if(in_array($row['status'], $total_follow_status))
            {
                $total_follow_nums++;
                isset($result[$row['follower_uid']]['follow']) ? $result[$row['follower_uid']]['follow']++ : $result[$row['follower_uid']]['follow'] = 1;
            }
            //累计建单
            if(in_array($row['status'], array($bstatus['build'] , $bstatus['parted'] , $bstatus['parted_n_4'])) || $row['trade_status'] > 0)
            {
                $total_build_nums++;
                isset($result[$row['follower_uid']]['build']) ? $result[$row['follower_uid']]['build']++ : $result[$row['follower_uid']]['build'] = 1;
            }
            //累计分单
            if(in_array($row['status'] , $parted_status)|| $row['trade_status'] > 0)
            {
                $total_parted_nums++;
                isset($result[$row['follower_uid']]['parted']) ? $result[$row['follower_uid']]['parted']++ : $result[$row['follower_uid']]['parted'] = 1;
            }
            //累计成单
            if($row['trade_status'] == $tstatus['ordered'])
            {
                $total_ordered_nums++;
                isset($result[$row['follower_uid']]['ordered']) ? $result[$row['follower_uid']]['ordered']++ : $result[$row['follower_uid']]['ordered'] = 1;
            }
            //累计丢单
            if($row['trade_status'] == $tstatus['discard'])
            {
                $total_discard_nums++;
                isset($result[$row['follower_uid']]['discard']) ? $result[$row['follower_uid']]['discard']++ : $result[$row['follower_uid']]['discard'] = 1;
            }
            //累计闭单
            if(in_array($row['trade_status'], $total_close_status))
            {
                $total_closed_nums++;
                isset($result[$row['follower_uid']]['closed']) ? $result[$row['follower_uid']]['closed']++ : $result[$row['follower_uid']]['closed'] = 1;
            }
        }

        $file_name = APPPATH . '/logs/' . 'adviser_follow_total_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '顾问累计处理情况统计[adviser_follow_total]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '顾问姓名','总分配数据','累计无效客户','累计跟进中','累计建单','累计建单率','累计分单数',
            '分单转换率','累计成单数','成单转换率','累计丢单数','累计无效订单数'
        );
        $exporter->addRow(
            $output[0]
        );

        foreach($adviser as $user)
        {
            if($user['username'] == 'guwen' || $user['username'] == 'guwen1')continue;
            $t_allocate = isset($result[$user['id']]['total_allocate']) ? $result[$user['id']]['total_allocate'] : 0;
            $t_invalid = isset($result[$user['id']]['invalid']) ? $result[$user['id']]['invalid'] : 0;
            $t_follow = isset($result[$user['id']]['follow']) ? $result[$user['id']]['follow'] : 0;
            $t_build = isset($result[$user['id']]['build']) ? $result[$user['id']]['build'] : 0;
            $t_parted = isset($result[$user['id']]['parted']) ? $result[$user['id']]['parted'] : 0;
            $t_ordered = isset($result[$user['id']]['ordered']) ? $result[$user['id']]['ordered'] : 0;
            $t_discard = isset($result[$user['id']]['discard']) ? $result[$user['id']]['discard'] : 0;
            $t_closed = isset($result[$user['id']]['closed']) ? $result[$user['id']]['closed'] : 0;

            $tpdata = array(
                $user['username'],
                $t_allocate, $t_invalid, $t_follow, $t_build,
                $t_allocate > 0 ? round($t_build / $t_allocate * 100, 2) . '%' : '0%',
                $t_parted,
                $t_allocate > 0 ? round($t_parted / $t_allocate * 100, 2) . '%' : '0%',
                $t_ordered,
                $t_allocate > 0 ? round($t_ordered / $t_allocate * 100, 2) . '%' : '0%',
                $t_discard,
                $t_closed
            );
            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }

        $end_data = array(
            '合计',
            $total_nums, $total_invalid_nums, $total_follow_nums, $total_build_nums,
            $total_nums > 0 ? round($total_build_nums / $total_nums * 100,2) . '%' : '0%',
            $total_parted_nums,
            $total_nums > 0 ? round($total_parted_nums / $total_nums * 100,2) . '%' : '0%',
            $total_ordered_nums,
            $total_nums > 0 ? round($total_ordered_nums / $total_nums * 100,2) . '%' : '0%',
            $total_discard_nums,
            $total_closed_nums
        );
        $exporter->addRow($end_data);
        $output[] = $end_data;
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 顾问商机处理情况
     * @param int $cycle 1日，2周，3月
     */
    private function _adviser_process($cycle = 1 , &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $bsource_flip = array_flip($bsource);

        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other'], $bstatus['3days_ago']);
        $total_close_status = array($tstatus['invalid']);
        $parted_status = array($bstatus['parted'], $bstatus['parted_n_4']);

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime FROM ew_business WHERE (is_test = 0 AND advisertime >=' . $begin_time . ' AND advisertime <= '. $end_time.' )' ;
        $dsql .= ' OR (is_test = 0 AND updatetime >= ' . $begin_time . ' AND updatetime <= ' . $end_time . ')';
        $dsql .= ' OR (is_test = 0 AND ordertime >= ' . $begin_time . ' AND ordertime >= ' . $end_time . ' )';
        $dquery = $this->erp_conn->query($dsql);

        $adviser_map = array();
        $department = $this->sdm->getWedAdviser();
        $adviser = $this->sum->findAll(array('department' => $department['id']), array(), array());
        foreach($adviser as $v)
        {
            $adviser_map[$v['id']] = 1;
        }

        $result = array();
        $allocate_n = $invalid_n = $phone_n = $noanswer_n = $nodemand_n = $other_n = $build_n = $parted_n = $ordered_n = $discard_n = $closed_n = 0;
        foreach($dquery->result_array() as $row)
        {
            if(!isset($adviser_map[$row['follower_uid']]))continue;
            //总分配数据
            if($row['advisertime'] >= $begin_time && $row['advisertime'] <= $end_time)
            {
                $allocate_n++;
                isset($result[$row['follower_uid']]['total_allocate']) ? $result[$row['follower_uid']]['total_allocate']++ : $result[$row['follower_uid']]['total_allocate'] = 1;
            }

            if($row['ordertime'] >= $begin_time && $row['ordertime'] <= $end_time)
            {
                //分单数量
                if(in_array($row['status'] , $parted_status) || $row['trade_status'] > 0)
                {
                    $parted_n++;
                    isset($result[$row['follower_uid']]['parted']) ? $result[$row['follower_uid']]['parted']++ : $result[$row['follower_uid']]['parted'] = 1;
                }
            }

            if($row['updatetime'] >= $begin_time && $row['updatetime'] <= $end_time)
            {
                //无效数量
                if(in_array($row['status'], $invalid_status))
                {
                    $invalid_n++;
                    isset($result[$row['follower_uid']]['invalid']) ? $result[$row['follower_uid']]['invalid']++ : $result[$row['follower_uid']]['invalid'] = 1;
                }
                //电话错误
                if($row['status'] == $bstatus['garbage_invalid_info'])
                {
                    $phone_n++;
                    isset($result[$row['follower_uid']]['garbage_invalid_info']) ? $result[$row['follower_uid']]['garbage_invalid_info']++ : $result[$row['follower_uid']]['garbage_invalid_info'] = 1;
                }
                //拒接或者关机
                if($row['status'] == $bstatus['follow_noanswer'])
                {
                    $noanswer_n++;
                    isset($result[$row['follower_uid']]['follow_noanswer']) ? $result[$row['follower_uid']]['follow_noanswer']++ : $result[$row['follower_uid']]['follow_noanswer'] = 1;
                }
                //无需求
                if($row['status'] == $bstatus['garbage_three_times'])
                {
                    $nodemand_n++;
                    isset($result[$row['follower_uid']]['garbage_three_times']) ? $result[$row['follower_uid']]['garbage_three_times']++ : $result[$row['follower_uid']]['garbage_three_times'] = 1;
                }
                //其他
                if($row['status'] == $bstatus['garbage_other'])
                {
                    $other_n++;
                    isset($result[$row['follower_uid']]['garbage_other']) ? $result[$row['follower_uid']]['garbage_other']++ : $result[$row['follower_uid']]['garbage_other'] = 1;
                }
                //建单数量
                if(in_array($row['status'], array($bstatus['build'] , $bstatus['parted'] , $bstatus['parted_n_4'])) || $row['trade_status'] > 0)
                {
                    $build_n++;
                    isset($result[$row['follower_uid']]['build']) ? $result[$row['follower_uid']]['build']++ : $result[$row['follower_uid']]['build'] = 1;
                }
                //成单数量
                if($row['trade_status'] == $tstatus['ordered'])
                {
                    $ordered_n++;
                    isset($result[$row['follower_uid']]['ordered']) ? $result[$row['follower_uid']]['ordered']++ : $result[$row['follower_uid']]['ordered'] = 1;
                }
                //丢单数量
                if($row['trade_status'] == $tstatus['discard'])
                {
                    $discard_n++;
                    isset($result[$row['follower_uid']]['discard']) ? $result[$row['follower_uid']]['discard']++ : $result[$row['follower_uid']]['discard'] = 1;
                }
                //闭单数量
                if(in_array($row['trade_status'] , $total_close_status))
                {
                    $closed_n++;
                    isset($result[$row['follower_uid']]['closed']) ? $result[$row['follower_uid']]['closed']++ : $result[$row['follower_uid']]['closed'] = 1;
                }
            }
        }

        $file_name = APPPATH . '/logs/' . 'adviser_process_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '顾问商机处理情况[adviser_process]';
        $table_data = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_data));
        $output[] = array(
            '顾问姓名','分配数量','无效数量','电话错误','拒接或者关机','无需求','其他','建单数量','分单数量',
            '成单数量','丢单数量','无效订单数量'
        );
        $exporter->addRow(
            $output[0]
        );

        foreach($adviser as $user)
        {
            if($user['username'] == 'guwen' || $user['username'] == 'guwen1')continue;
            $tpdata = array(
                $user['username'],
                isset($result[$user['id']]['total_allocate']) ? $result[$user['id']]['total_allocate'] : 0,
                isset($result[$user['id']]['invalid']) ? $result[$user['id']]['invalid'] : 0,
                isset($result[$user['id']]['garbage_invalid_info']) ? $result[$user['id']]['garbage_invalid_info'] : 0,
                isset($result[$user['id']]['follow_noanswer']) ? $result[$user['id']]['follow_noanswer'] : 0,
                isset($result[$user['id']]['garbage_three_times']) ? $result[$user['id']]['garbage_three_times'] : 0,
                isset($result[$user['id']]['garbage_other']) ? $result[$user['id']]['garbage_other'] : 0,
                isset($result[$user['id']]['build']) ? $result[$user['id']]['build'] : 0,
                isset($result[$user['id']]['parted']) ? $result[$user['id']]['parted'] : 0,
                isset($result[$user['id']]['ordered']) ? $result[$user['id']]['ordered'] : 0,
                isset($result[$user['id']]['discard']) ? $result[$user['id']]['discard'] : 0,
                isset($result[$user['id']]['closed']) ? $result[$user['id']]['closed'] : 0
            );
            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }
        $end_data = array('合计', $allocate_n , $invalid_n , $phone_n , $noanswer_n , $nodemand_n , $other_n , $build_n , $parted_n , $ordered_n , $discard_n , $closed_n);
        $exporter->addRow($end_data);
        $output[] = $end_data;
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_data , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 商机累计处理情况
     * @param int $cycle 1日，2周，3月
     */
    private function _business_process2($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $bsource_flip = array_flip($bsource);

        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other'], $bstatus['3days_ago']);
        $total_close_status = array($tstatus['invalid']);
        $parted_status = array($bstatus['parted'], $bstatus['parted_n_4']);

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_del FROM ew_business WHERE is_test = 0 AND createtime >=' . $begin_time . ' AND createtime <= '. $end_time;
        $dquery = $this->erp_conn->query($dsql);

        $result = array();
        $newadd_n = $invalid_n = $phone_n = $noanswer_n = $nodemand_n = $other_n = $repeat_n = $follower_n = $follow_n = $build_n = $parted_n = $ordered_n = $discard_n = $closed_n = 0;
        foreach($dquery->result_array() as $row)
        {
            if($row['source'] <= 0)continue;
            //新增商机数量
            $newadd_n++;
            isset($result[$row['source']]['new_add']) ? $result[$row['source']]['new_add']++ : $result[$row['source']]['new_add'] = 1;

            //无效数量
            if(in_array($row['status'], $invalid_status))
            {
                $invalid_n++;
                isset($result[$row['source']]['invalid']) ? $result[$row['source']]['invalid']++ : $result[$row['source']]['invalid'] = 1;
            }
            //电话错误
            if($row['status'] == $bstatus['garbage_invalid_info'])
            {
                $phone_n++;
                isset($result[$row['source']]['garbage_invalid_info']) ? $result[$row['source']]['garbage_invalid_info']++ : $result[$row['source']]['garbage_invalid_info'] = 1;
            }
            //拒接或者关机
            if($row['status'] == $bstatus['follow_noanswer'])
            {
                $noanswer_n++;
                isset($result[$row['source']]['follow_noanswer']) ? $result[$row['source']]['follow_noanswer']++ : $result[$row['source']]['follow_noanswer'] = 1;
            }
            //无需求
            if($row['status'] == $bstatus['garbage_three_times'])
            {
                $nodemand_n++;
                isset($result[$row['source']]['garbage_three_times']) ? $result[$row['source']]['garbage_three_times']++ : $result[$row['source']]['garbage_three_times'] = 1;
            }
            //其他
            if($row['status'] == $bstatus['garbage_other'])
            {
                $other_n++;
                isset($result[$row['source']]['garbage_other']) ? $result[$row['source']]['garbage_other']++ : $result[$row['source']]['garbage_other'] = 1;
            }
            //重复提交
            if($row['is_del'] == 1)
            {
                $repeat_n++;
                isset($result[$row['source']]['repeat']) ? $result[$row['source']]['repeat']++ : $result[$row['source']]['repeat'] = 1;
            }
            //分顾问商机数量
            if($row['follower_uid'] > 0)
            {
                isset($result[$row['source']]['follower']) ? $result[$row['source']]['follower']++ : $result[$row['source']]['follower'] = 1;
            }
            //跟进中商机数量
            if($row['status'] == $bstatus['follow_next'])
            {
                $follow_n++;
                isset($result[$row['source']]['follow']) ? $result[$row['source']]['follow']++ : $result[$row['source']]['follow'] = 1;
            }
            //建单数量
            if(in_array($row['status'], array($bstatus['build'] , $bstatus['parted'] , $bstatus['parted_n_4'])) || $row['trade_status'] > 0)
            {
                $build_n++;
                isset($result[$row['source']]['build']) ? $result[$row['source']]['build']++ : $result[$row['source']]['build'] = 1;
            }
            //分单数量
            if(in_array($row['status'] , $parted_status) || $row['trade_status'] > 0)
            {
                $parted_n++;
                isset($result[$row['source']]['parted']) ? $result[$row['source']]['parted']++ : $result[$row['source']]['parted'] = 1;
            }
            //成单数量
            if($row['trade_status'] == $tstatus['ordered'])
            {
                $ordered_n++;
                isset($result[$row['source']]['ordered']) ? $result[$row['source']]['ordered']++ : $result[$row['source']]['ordered'] = 1;
            }
            //丢单数量
            if($row['trade_status'] == $tstatus['discard'])
            {
                $discard_n++;
                isset($result[$row['source']]['discard']) ? $result[$row['source']]['discard']++ : $result[$row['source']]['discard'] = 1;
            }
            //闭单数量
            if(in_array($row['trade_status'] , $total_close_status))
            {
                $closed_n++;
                isset($result[$row['source']]['closed']) ? $result[$row['source']]['closed']++ : $result[$row['source']]['closed'] = 1;
            }
        }

        $file_name = APPPATH . '/logs/' . 'business_process2_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '商机累计处理情况[business_process2]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '商机来源','提交商机数量','无效数量','电话错误','拒接或者关机','无需求','其他','重复提交','分顾问商机数量',
            '跟进中商机数量','建单数量','分单数量','成单数量','丢单数量','无效订单数量'
        );
        $exporter->addRow(
            $output[0]
        );

        foreach($bsource_flip as $bkey => $bs)
        {
            $t_follower = isset($result[$bkey]['follower']) ? $result[$bkey]['follower'] : 0;
            $tpdata = array(
                $bsource_explan[$bs],
                isset($result[$bkey]['new_add']) ? $result[$bkey]['new_add'] : 0,
                isset($result[$bkey]['invalid']) ? $result[$bkey]['invalid'] : 0,
                isset($result[$bkey]['garbage_invalid_info']) ? $result[$bkey]['garbage_invalid_info'] : 0,
                isset($result[$bkey]['follow_noanswer']) ? $result[$bkey]['follow_noanswer'] : 0,
                isset($result[$bkey]['garbage_three_times']) ? $result[$bkey]['garbage_three_times'] : 0,
                isset($result[$bkey]['garbage_other']) ? $result[$bkey]['garbage_other'] : 0,
                isset($result[$bkey]['repeat']) ? $result[$bkey]['repeat'] : 0,
                $t_follower,
                isset($result[$bkey]['follow']) ? $result[$bkey]['follow'] : 0,
                isset($result[$bkey]['build']) ? $result[$bkey]['build'] : 0,
                isset($result[$bkey]['parted']) ? $result[$bkey]['parted'] : 0,
                isset($result[$bkey]['ordered']) ? $result[$bkey]['ordered'] : 0,
                isset($result[$bkey]['discard']) ? $result[$bkey]['discard'] : 0,
                isset($result[$bkey]['closed']) ? $result[$bkey]['closed'] : 0,
            );
            $output[] = $tpdata;
            $follower_n += $t_follower;
            $exporter->addRow($tpdata);
        }

        $end_data = array('合计', $newadd_n , $invalid_n , $phone_n , $noanswer_n , $nodemand_n , $other_n , $repeat_n , $follower_n , $follow_n , $build_n , $parted_n , $ordered_n , $discard_n , $closed_n);
        $output[] = $end_data;
        $exporter->addRow($end_data);
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 商机实际处理情况统计
     * @param int $cycle 1日，2周，3月
     * @param $html_msg html字符串
     * @return string
     */
    private function _business_process_updatetime($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $bsource_flip = array_flip($bsource);

        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other'], $bstatus['3days_ago']);
        $total_close_status = array($tstatus['invalid']);
        $parted_status = array($bstatus['parted'], $bstatus['parted_n_4']);

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_del FROM ew_business WHERE ( is_test = 0 AND createtime >=' . $begin_time . ' AND createtime <= '. $end_time . ')';
        $dsql .= ' OR (is_test = 0 AND updatetime >= ' . $begin_time . ' AND updatetime <= ' . $end_time . ')';
        $dsql .= ' OR (is_test = 0 AND ordertime >= ' . $begin_time . ' AND ordertime >= ' . $end_time . ' )';
        $dquery = $this->erp_conn->query($dsql);

        $result = array();
        $newadd_n = $invalid_n = $phone_n = $noanswer_n = $nodemand_n = $other_n = $repeat_n = $follower_n = $follow_n = $build_n = $parted_n = $ordered_n = $discard_n = $closed_n = 0;
        foreach($dquery->result_array() as $row)
        {
            if($row['source'] <= 0)continue;
            //新增商机数量
            if($row['createtime'] >= $begin_time && $row['createtime'] <= $end_time)
            {
                $newadd_n++;
                isset($result[$row['source']]['new_add']) ? $result[$row['source']]['new_add']++ : $result[$row['source']]['new_add'] = 1;
            }

            if($row['updatetime'] >= $begin_time && $row['updatetime'] <= $end_time)
            {
                //无效数量
                if (in_array($row['status'], $invalid_status))
                {
                    $invalid_n++;
                    isset($result[$row['source']]['invalid']) ? $result[$row['source']]['invalid']++ : $result[$row['source']]['invalid'] = 1;
                }
                //电话错误
                if ($row['status'] == $bstatus['garbage_invalid_info'])
                {
                    $phone_n++;
                    isset($result[$row['source']]['garbage_invalid_info']) ? $result[$row['source']]['garbage_invalid_info']++ : $result[$row['source']]['garbage_invalid_info'] = 1;
                }
                //拒接或者关机
                if ($row['status'] == $bstatus['follow_noanswer'])
                {
                    $noanswer_n++;
                    isset($result[$row['source']]['follow_noanswer']) ? $result[$row['source']]['follow_noanswer']++ : $result[$row['source']]['follow_noanswer'] = 1;
                }
                //无需求
                if ($row['status'] == $bstatus['garbage_three_times'])
                {
                    $nodemand_n++;
                    isset($result[$row['source']]['garbage_three_times']) ? $result[$row['source']]['garbage_three_times']++ : $result[$row['source']]['garbage_three_times'] = 1;
                }
                //其他
                if ($row['status'] == $bstatus['garbage_other'])
                {
                    $other_n++;
                    isset($result[$row['source']]['garbage_other']) ? $result[$row['source']]['garbage_other']++ : $result[$row['source']]['garbage_other'] = 1;
                }
                //重复提交
                if ($row['is_del'] == 1)
                {
                    $repeat_n++;
                    isset($result[$row['source']]['repeat']) ? $result[$row['source']]['repeat']++ : $result[$row['source']]['repeat'] = 1;
                }
                //分配顾问数量
                if($row['follower_uid'] > 0)
                {
                    isset($result[$row['source']]['follower']) ? $result[$row['source']]['follower']++ : $result[$row['source']]['follower'] = 1;
                }
                //跟进中商机数量
                if ($row['status'] == $bstatus['follow_next'])
                {
                    $follow_n++;
                    isset($result[$row['source']]['follow']) ? $result[$row['source']]['follow']++ : $result[$row['source']]['follow'] = 1;
                }
                //建单数量
                if (in_array($row['status'], array($bstatus['build'], $bstatus['parted'], $bstatus['parted_n_4'])) || $row['trade_status'] > 0)
                {
                    $build_n++;
                    isset($result[$row['source']]['build']) ? $result[$row['source']]['build']++ : $result[$row['source']]['build'] = 1;
                }
                //成单数量
                if ($row['trade_status'] == $tstatus['ordered'])
                {
                    $ordered_n++;
                    isset($result[$row['source']]['ordered']) ? $result[$row['source']]['ordered']++ : $result[$row['source']]['ordered'] = 1;
                }
                //丢单数量
                if ($row['trade_status'] == $tstatus['discard'])
                {
                    $discard_n++;
                    isset($result[$row['source']]['discard']) ? $result[$row['source']]['discard']++ : $result[$row['source']]['discard'] = 1;
                }
                //闭单数量
                if (in_array($row['trade_status'], $total_close_status))
                {
                    $closed_n++;
                    isset($result[$row['source']]['closed']) ? $result[$row['source']]['closed']++ : $result[$row['source']]['closed'] = 1;
                }
            }

            if($row['ordertime'] >= $begin_time && $row['ordertime'] <= $end_time)
            {
                //分单数量
                if (in_array($row['status'] , $parted_status) || $row['trade_status'] > 0)
                {
                    $parted_n++;
                    isset($result[$row['source']]['parted']) ? $result[$row['source']]['parted']++ : $result[$row['source']]['parted'] = 1;
                }
            }
        }

        $file_name = APPPATH . '/logs/' . 'business_process_updatetime_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '商机实际处理情况[business_process_updatetime]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '商机来源','提交商机数量','无效数量','电话错误','拒接或者关机','无需求','其他','重复提交','分顾问商机数量',
            '跟进中商机数量','建单数量','分单数量','成单数量','丢单数量','无效订单数量'
        );
        $exporter->addRow(
            $output[0]
        );

        foreach($bsource_flip as $bkey => $bs)
        {
            $t_follower = isset($result[$bkey]['follower']) ? $result[$bkey]['follower'] : 0;
            $tpdata = array(
                $bsource_explan[$bs],
                isset($result[$bkey]['new_add']) ? $result[$bkey]['new_add'] : 0,
                isset($result[$bkey]['invalid']) ? $result[$bkey]['invalid'] : 0,
                isset($result[$bkey]['garbage_invalid_info']) ? $result[$bkey]['garbage_invalid_info'] : 0,
                isset($result[$bkey]['follow_noanswer']) ? $result[$bkey]['follow_noanswer'] : 0,
                isset($result[$bkey]['garbage_three_times']) ? $result[$bkey]['garbage_three_times'] : 0,
                isset($result[$bkey]['garbage_other']) ? $result[$bkey]['garbage_other'] : 0,
                isset($result[$bkey]['repeat']) ? $result[$bkey]['repeat'] : 0,
                $t_follower,
                isset($result[$bkey]['follow']) ? $result[$bkey]['follow'] : 0,
                isset($result[$bkey]['build']) ? $result[$bkey]['build'] : 0,
                isset($result[$bkey]['parted']) ? $result[$bkey]['parted'] : 0,
                isset($result[$bkey]['ordered']) ? $result[$bkey]['ordered'] : 0,
                isset($result[$bkey]['discard']) ? $result[$bkey]['discard'] : 0,
                isset($result[$bkey]['closed']) ? $result[$bkey]['closed'] : 0,
            );
            $output[] = $tpdata;
            $follower_n += $t_follower;
            $exporter->addRow($tpdata);
        }

        $end_data = array('合计', $newadd_n , $invalid_n , $phone_n , $noanswer_n , $nodemand_n , $other_n , $repeat_n , $follower_n , $follow_n , $build_n , $parted_n , $ordered_n , $discard_n , $closed_n);
        $output[] = $end_data;
        $exporter->addRow($end_data);
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     *客户类型分单情况
     * @param int $cycle 1日，2周，3月
     */
    private function _customer_parted($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $bsource_flip = array_flip($bsource);

        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other'], $bstatus['3days_ago']);
        $total_close_status = array($tstatus['invalid']);
        $face_status = array($tstatus['faced'], $tstatus['ordered']);
        $closed_status = array($tstatus['invalid']);
        $parted_status = array($bstatus['parted'], $bstatus['parted_n_4']);

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_face,is_sign,is_del,usertype FROM ew_business WHERE is_test = 0 AND ordertime >=' . $begin_time . ' AND ordertime <= '. $end_time;
        $dquery = $this->erp_conn->query($dsql);

        $result = array();
        $t_allocate = $t_face = $t_ordered = $t_discard = $t_closed = 0;
        foreach($dquery->result_array() as $row)
        {
            if(empty($row['usertype']))continue;
            //分单量
            if(in_array($row['status'] , $parted_status) || $row['trade_status'] > 0)
            {
                $t_allocate++;
                isset($result[$row['usertype']]['parted']) ? $result[$row['usertype']]['parted']++ : $result[$row['usertype']]['parted'] = 1;
            }
            //见面量
            if(in_array($row['trade_status'], $face_status) || $row['is_face'] == 1)
            {
                $t_face++;
                isset($result[$row['usertype']]['face']) ? $result[$row['usertype']]['face']++ : $result[$row['usertype']]['face'] = 1;
            }
            //成单量
            if($row['trade_status'] == $tstatus['ordered'] || $row['is_sign'] == 1)
            {
                $t_ordered++;
                isset($result[$row['usertype']]['sign']) ? $result[$row['usertype']]['sign']++ : $result[$row['usertype']]['sign'] = 1;
            }
            //丢单量
            if($row['trade_status'] == $tstatus['discard'])
            {
                $t_discard++;
                isset($result[$row['usertype']]['discard']) ? $result[$row['usertype']]['discard']++ : $result[$row['usertype']]['discard'] = 1;
            }
            //无效量
            if(in_array($row['trade_status'], $closed_status))
            {
                $t_closed++;
                isset($result[$row['usertype']]['closed']) ? $result[$row['usertype']]['closed']++ : $result[$row['usertype']]['closed'] = 1;
            }
        }

        $file_name = APPPATH . '/logs/' . 'customer_parted_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '客户类型分单情况[customer_parted]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '客户类型','分单量','见面量','见面率','成单量','成单率','丢单量','丢单率','无效量','无效率'
        );
        $exporter->addRow(
            $output[0]
        );

        $usertypes = $this->business->getUserType();

        foreach($usertypes as $tp)
        {
            $parted_n = isset($result[$tp]['parted']) ? $result[$tp]['parted'] : 0;
            $face_n = isset($result[$tp]['face']) ? $result[$tp]['face'] : 0;
            $sign_n = isset($result[$tp]['sign']) ? $result[$tp]['sign'] : 0;
            $discard_n = isset($result[$tp]['discard']) ? $result[$tp]['discard'] : 0;
            $closed_n = isset($result[$tp]['closed']) ? $result[$tp]['closed'] : 0;

            $tpdata = array(
                $tp, $parted_n, $face_n, $parted_n > 0 ? round($face_n / $parted_n * 100,2) . '%' : '0%',
                $sign_n, $parted_n > 0 ? round($sign_n / $parted_n * 100, 2) . '%' : '0%',
                $discard_n, $parted_n > 0 ? round($discard_n / $parted_n * 100, 2) . '%' : '0%',
                $closed_n, $parted_n > 0 ? round($closed_n / $parted_n * 100, 2) . '%' : '0%'
            );
            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }

        $end_data = array(
            '合计', $t_allocate , $t_face ,
            $t_allocate > 0 ? round($t_face / $t_allocate * 100, 2) . '%' : '0%',
            $t_ordered, $t_allocate > 0 ? round($t_ordered / $t_allocate * 100, 2) . '%' : '0%',
            $t_discard, $t_allocate > 0 ? round($t_discard / $t_allocate * 100, 2) . '%' : '0%',
            $t_closed, $t_allocate > 0 ? round($t_closed / $t_allocate * 100, 2) . '%' : '0%'
        );
        $exporter->addRow($end_data);
        $output[] = $end_data;
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     *客户类型分单实际处理情况
     * @param int $cycle 1日，2周，3月
     */
    private function _customer_parted_updatetime($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $bsource_flip = array_flip($bsource);

        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other'], $bstatus['3days_ago']);
        $total_close_status = array($tstatus['invalid']);
        $face_status = array($tstatus['faced'], $tstatus['ordered']);
        $closed_status = array($tstatus['invalid']);
        $parted_status = array($bstatus['parted'], $bstatus['parted_n_4']);

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_face,is_sign,is_del FROM ew_business WHERE ( is_test = 0 AND ordertime >=' . $begin_time . ' AND ordertime <= '. $end_time . ')';
        $dsql .= ' OR (is_test = 0 AND updatetime >= ' . $begin_time . ' AND updatetime <= ' . $end_time . ')';
        $dquery = $this->erp_conn->query($dsql);

        $result = array();
        $t_allocate = $t_face = $t_ordered = $t_discard = $t_closed = 0;
        foreach($dquery->result_array() as $row)
        {
            if(empty($row['usertype']))continue;
            if($row['ordertime'] >= $begin_time && $row['ordertime'] <= $end_time)
            {
                //分单量
                if(in_array($row['status'] , $parted_status) || $row['trade_status'] > 0)
                {
                    $t_allocate++;
                    isset($result[$row['usertype']]['parted']) ? $result[$row['usertype']]['parted']++ : $result[$row['usertype']]['parted'] = 1;
                }
            }

            if($row['updatetime'] >= $begin_time && $row['updatetime'] <= $end_time)
            {
                //见面量
                if(in_array($row['trade_status'], $face_status) || $row['is_face'] == 1)
                {
                    $t_face++;
                    isset($result[$row['usertype']]['face']) ? $result[$row['usertype']]['face']++ : $result[$row['usertype']]['face'] = 1;
                }
                //成单量
                if($row['trade_status'] == $tstatus['ordered'] || $row['is_sign'] == 1)
                {
                    $t_ordered++;
                    isset($result[$row['usertype']]['sign']) ? $result[$row['usertype']]['sign']++ : $result[$row['usertype']]['sign'] = 1;
                }
                //丢单量
                if($row['trade_status'] == $tstatus['discard'])
                {
                    $t_discard++;
                    isset($result[$row['usertype']]['discard']) ? $result[$row['usertype']]['discard']++ : $result[$row['usertype']]['discard'] = 1;
                }
                //无效量
                if(in_array($row['trade_status'], $closed_status))
                {
                    $t_closed++;
                    isset($result[$row['usertype']]['closed']) ? $result[$row['usertype']]['closed']++ : $result[$row['usertype']]['closed'] = 1;
                }
            }
        }

        $file_name = APPPATH . '/logs/' . 'customer_parted_updatetime_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '客户类型分单实际处理情况[customer_parted_updatetime]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '客户类型','分单量','见面量','成单量','丢单量','无效订单量'
        );
        $exporter->addRow(
            $output[0]
        );

        $usertypes = $this->business->getUserType();

        foreach($usertypes as $tp)
        {
            $parted_n = isset($result[$tp]['parted']) ? $result[$tp]['parted'] : 0;
            $face_n = isset($result[$tp]['face']) ? $result[$tp]['face'] : 0;
            $sign_n = isset($result[$tp]['sign']) ? $result[$tp]['sign'] : 0;
            $discard_n = isset($result[$tp]['discard']) ? $result[$tp]['discard'] : 0;
            $closed_n = isset($result[$tp]['closed']) ? $result[$tp]['closed'] : 0;

            $tpdata = array(
                $tp, $parted_n, $face_n, $sign_n, $discard_n, $closed_n
            );
            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }

        $end_data = array(
            '合计', $t_allocate, $t_face, $t_ordered, $t_discard, $t_closed
        );
        $exporter->addRow($end_data);
        $output[] = $end_data;
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 无效订单原因分析
     * @param int $cycle
     * @param $html_msg
     */
    private function _reason_analysis($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();

        $dsql = 'SELECT status_note,trade_status FROM ew_business WHERE is_test = 0 AND updatetime >= ' . $begin_time . ' AND updatetime <= ' . $end_time . ' AND status_note <> "" AND trade_status = ' . $tstatus['invalid'];
        $dquery = $this->erp_conn->query($dsql);

        $result = array();
        $total_nums = 0;
        foreach($dquery->result_array() as $row)
        {
            $total_nums++;
            $md5_str = md5($row['status_note']);
            if(isset($result[$md5_str]))
            {
                $result[$md5_str]['nums']++;
            }
            else
            {
                $result[$md5_str] = array(
                    'desc' => $row['status_note'],
                    'nums' => 1
                );
            }
        }

        $file_name = APPPATH . '/logs/' . 'reason_analysis_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '无效订单原因分析[reason_analysis]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '无效订单原因','单量','无效比例'
        );
        $exporter->addRow(
            $output[0]
        );

        foreach($result as $val)
        {
            $tpdata = array(
                $val['desc'], $val['nums'],
                $total_nums > 0 ? (round($val['nums'] / $total_nums * 100, 2)) . '%' : '0%'
            );
            $exporter->addRow($tpdata);
            $output[] = $tpdata;
        }

        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 今日跟进客户情况
     * @param int $cycle
     * @param $html_msg
     */
    private function _day_follow_customer($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        $begin_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $end_time = mktime(20,0,0,date('m'),date('d'),date('Y'));
        //list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();

        $build_status = array($bstatus['build'], $bstatus['parted'], $bstatus['parted_n_4']);
        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other']);
        $follow_status = array($bstatus['newadd'], $bstatus['follow_next'], $bstatus['3days_ago']);

        $adviser_map = array();
        $department = $this->sdm->getWedAdviser();
        $adviser = $this->sum->findAll(array('department' => $department['id']), array(), array());
        foreach($adviser as $v)
        {
            $adviser_map[$v['id']] = 1;
        }

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_face,is_sign,is_del FROM ew_business WHERE '.
            'is_test = 0 AND advisertime >= ' . $begin_time . ' AND advisertime <= ' . $end_time;
        $dquery = $this->erp_conn->query($dsql);

        //整理已经分配顾问的商机
        $bids = $bus_map = array();
        foreach($dquery->result_array() as $row)
        {
            $bids[] = $row['id'];
        }
        $bus_map = $this->_get_allocate_history($bids , $begin_time);

        $result = array();
        $t_allocate = $t_build = $t_follow = $t_recall = $t_month_build = $t_month_follow = $t_month_invalid = 0;
        foreach($dquery->result_array() as $row)
        {
            if(isset($bus_map[$row['id']]))continue;
            //今日分发量
            isset($result[$row['follower_uid']]['allocate']) ? $result[$row['follower_uid']]['allocate']++ : $result[$row['follower_uid']]['allocate'] = 1;
            $t_allocate++;
            //今日分发的客户的建单数
            if(in_array($row['status'], $build_status) || $row['trade_status'] > 0)
            {
                if($row['updatetime'] > $begin_time && $row['updatetime'] < $end_time)
                {
                    $t_build++;
                    isset($result[$row['follower_uid']]['build']) ? $result[$row['follower_uid']]['build']++ : $result[$row['follower_uid']]['build'] = 1;
                }
            }

            //今日分发客户中待跟进客户数
            if(in_array($row['status'] , $follow_status))
            {
                $t_follow++;
                isset($result[$row['follower_uid']]['follow']) ? $result[$row['follower_uid']]['follow']++ : $result[$row['follower_uid']]['follow'] = 1;
            }
        }

        //回捞客户的建单数
        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_face,is_sign,is_del FROM ew_business WHERE (is_test = 0 AND updatetime > ' . $begin_time . ' AND updatetime < ' . $end_time . ')';
        //$dsql .= ' OR (is_test = 0 AND ordertime > '.$begin_time.' AND ordertime < '.$end_time.')';
        $dquery = $this->erp_conn->query($dsql);
        foreach($dquery->result_array() as $row)
        {
            if(in_array($row['status'], $build_status) || $row['trade_status'] > 0)
            {
                if($row['advisertime'] < $begin_time)
                {
                    $t_recall++;
                    isset($result[$row['follower_uid']]['recall']) ? $result[$row['follower_uid']]['recall']++ : $result[$row['follower_uid']]['recall'] = 1;
                }
            }
        }

        //本月数据统计
        $month_time = mktime(0,0,0,date('m'),1,date('Y'));
        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_face,is_sign,is_del FROM ew_business WHERE advisertime > ' . $month_time . ' AND advisertime < ' . $end_time;
        $dquery = $this->erp_conn->query($dsql);
        foreach($dquery->result_array() as $row)
        {
            //本月累计建单数
            if(in_array($row['status'], $build_status) || $row['trade_status'] > 0)
            {
                $t_month_build++;
                isset($result[$row['follower_uid']]['month_build']) ? $result[$row['follower_uid']]['month_build']++ : $result[$row['follower_uid']]['month_build'] = 1;
            }

            //本月跟进中
            if(in_array($row['status'], $follow_status))
            {
                $t_month_follow++;
                isset($result[$row['follower_uid']]['month_follow']) ? $result[$row['follower_uid']]['month_follow']++ : $result[$row['follower_uid']]['month_follow'] = 1;
            }

            //本月累计无效数
            if(in_array($row['status'], $invalid_status))
            {
                $t_month_invalid++;
                isset($result[$row['follower_uid']]['month_invalid']) ? $result[$row['follower_uid']]['month_invalid']++ : $result[$row['follower_uid']]['month_invalid'] = 1;
            }
        }

        $file_name = APPPATH . '/logs/' . 'day_follow_customer_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '今日跟进客户情况[day_follow_customer]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '顾问名字' , '今日分发量' , '今日分发的客户的建单数' , '今日分发客户中待跟进客户数',
            '回捞客户数' , '回捞客户的建单数' , '今日53客服建单数' , '本月累计建单数',
            '本月累计跟进数' , '本月累计无效数'
        );
        $exporter->addRow(
            $output[0]
        );

        foreach($adviser as $user)
        {
            if($user['username'] == 'guwen' || $user['username'] == 'guwen1')continue;
            $tpdata = array(
                $user['username'],
                isset($result[$user['id']]['allocate']) ? $result[$user['id']]['allocate'] : 0,
                isset($result[$user['id']]['build']) ? $result[$user['id']]['build'] : 0,
                isset($result[$user['id']]['follow']) ? $result[$user['id']]['follow'] : 0,
                0,
                isset($result[$user['id']]['recall']) ? $result[$user['id']]['recall'] : 0,
                0,
                isset($result[$user['id']]['month_build']) ? $result[$user['id']]['month_build'] : 0,
                isset($result[$user['id']]['month_follow']) ? $result[$user['id']]['month_follow'] : 0,
                isset($result[$user['id']]['month_invalid']) ? $result[$user['id']]['month_invalid'] : 0,
            );
            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }

        $end_data = array(
            '合计', $t_allocate , $t_build , $t_follow ,0, $t_recall ,0, $t_month_build ,$t_month_follow , $t_month_invalid
        );
        $exporter->addRow($end_data);
        $output[] = $end_data;
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 今日建单情况
     * @param int $cycle
     * @param $html_msg
     */
    private function _day_build_customer($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        $begin_time = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $end_time = mktime(20,0,0,date('m'),date('d'),date('Y'));
        //list($begin_time , $end_time) = $this->_get_time($cycle);

        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        $usertypes = $this->business->getUserType();

        $build_status = array($bstatus['build'], $bstatus['parted'], $bstatus['parted_n_4']);
        $invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other']);
        $follow_status = array($bstatus['newadd'], $bstatus['follow_next'], $bstatus['3days_ago']);

        $adviser_map = array();
        $department = $this->sdm->getWedAdviser();
        $adviser = $this->sum->findAll(array('department' => $department['id']), array(), array());
        foreach($adviser as $v)
        {
            $adviser_map[$v['id']] = 1;
        }

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_face,is_sign,is_del,usertype FROM ew_business WHERE '.
            'is_test = 0 AND updatetime >= ' . $begin_time . ' AND updatetime <= ' . $end_time;
        $dquery = $this->erp_conn->query($dsql);

        //整理已经分配顾问的商机
        $bids = $bus_map = array();
        foreach($dquery->result_array() as $row)
        {
            $bids[] = $row['id'];
        }
        $bus_map = $this->_get_allocate_history($bids , $begin_time);

        $result = $total_build = array();
        $total_build_nums = 0;
        foreach($dquery->result_array() as $row)
        {
            if(isset($bus_map[$row['id']]))continue;
            if(in_array($row['status'], $build_status) || $row['trade_status'] > 0)
            {
                if(in_array($row['usertype'] , $usertypes))
                {
                    if($row['advisertime'] > $begin_time && $row['advisertime'] < $end_time)
                    {
                        $total_build_nums++;
                        //今日分发客户建单
                        isset($result[$row['follower_uid']]['build'][$row['usertype']]) ? $result[$row['follower_uid']]['build'][$row['usertype']]++ : $result[$row['follower_uid']]['build'][$row['usertype']] = 1;
                        isset($total_build[$row['usertype']]) ? $total_build[$row['usertype']]++ : $total_build[$row['usertype']] = 1;
                    }
                    //回捞客户建单
                    if($row['advisertime'] < $begin_time)
                    {
                        isset($result[$row['follower_uid']]['recall'][$row['usertype']]) ? $result[$row['follower_uid']]['recall'][$row['usertype']]++ : $result[$row['follower_uid']]['recall'][$row['usertype']] = 1;
                    }
                }
            }
        }

        $file_name = APPPATH . '/logs/' . 'day_build_customer_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '今日建单情况[day_build_customer]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $ext_fields = array('C2','C4','C5','C5和C6','C6','C7');

        $output[] = array_merge(array('顾问名字' , '今日建单总数') , $ext_fields);
        $exporter->addRow(
            $output[0]
        );

        foreach($adviser as $user)
        {
            if($user['username'] == 'guwen' || $user['username'] == 'guwen1')continue;
            $tpdata = array(
                $user['username'] , '今日分发客户建单'
            );
            $tpdata2 = array(
                $user['username'] , '回捞客户建单'
            );
            foreach($ext_fields as $field)
            {
                $tmp_num = isset($result[$user['id']]['build'][$field]) ? $result[$user['id']]['build'][$field] : 0;
                $tpdata[] = $tmp_num;

                $tmp_num2 = isset($result[$user['id']]['recall'][$field]) ? $result[$user['id']]['recall'][$field] : 0;
                $tpdata2[] = $tmp_num2;
            }
            $output[] = $tpdata;
            $output[] = $tpdata2;
            $exporter->addRow($tpdata);
            $exporter->addRow($tpdata2);
        }

        $end_data = array(
            '合计' , $total_build_nums
        );
        foreach($ext_fields as $field)
        {
            $tmp_data = isset($total_build[$field]) ?  $total_build[$field] : 0;
            $end_data[] = $tmp_data;
        }

        $exporter->addRow($end_data);
        $output[] = $end_data;
        $exporter->finalize();
        $html_msg = $this->_make_html_str($table_title , $table_date , $output);
        if(file_exists($file_name))
        {
            //@unlink($file_name);
        }
        return $file_name;
    }

    /**
     * 获取统计数据周期的开始与结束时间
     * @param int $cycle
     * @return array|string
     */
    private function _get_time($cycle = 1)
    {
        $begin_time = $end_time = 0;
        if($cycle == 2)
        {
            $week = date('w');
            //如果不是周六，则不统计数据
            if($week != 3)
            {
                die('not Wednesday');
            }

            $end_time = mktime(0,0,0,date('m'),date('d') - 5,date('Y')) - 1;//上上周六 23:59:59,本周3减5天
            $begin_time = $end_time + 1 - 3600 * 24 * 8;//上周六 00:00:00

//            $end_time = mktime(0,0,0,date('m'),date('d'),date('Y')) - 1;//周5 23:59:59
//            $begin_time = $end_time + 1 - 3600 * 24 * 7;//上周六 00:00:00
        }
        else if($cycle == 3)
        {
            $today = date('j');
            if($today != 1)
            {
                //die('The 1st');
            }

            $begin_time = mktime(0,0,0,date('m')-1,1,date('Y'));//上月1号
            $end_time = mktime(0,0,0,date('m'),1,date('Y')) - 1;//上月最后一天 23:59:59
        }
        else
        {
            $begin_time = mktime(0,0,0,date('m'),date('d') - 1,date('Y'));//昨天 00:00:00
            $end_time = mktime(0,0,0,date('m'),date('d'),date('Y')) - 1;//昨天 23:59:59
        }
        return array($begin_time , $end_time);
    }

    /**
     * 发送邮件
     * @param array $files 附件列表
     */
    private function _send_email($files = array() , $content = "" , $cycle = '' , $user_type = 1)
    {
        if(count($files) <= 0)return false;
        $endtime = mktime(0,0,0,date('m'),date('d'),date('Y')) - 1;
        $mail_conf['crlf'] = "\r\n";
        $mail_conf['newline'] = "\r\n";
        $mail_conf['protocol'] = 'smtp';
        $mail_conf['smtp_host'] = 'smtp.qq.com';
        $mail_conf['smtp_user'] = 'is@easywed.cn';
        $mail_conf['smtp_pass'] = 'QWERasdf1234';
        $mail_conf['smtp_port'] = '25';
        $mail_conf['charset'] = 'utf-8';
        $mail_conf['wordwrap'] = TRUE;
        $mail_conf['mailtype'] = 'html';
        $this->email->initialize($mail_conf);

        $this->email->from('is@easywed.cn', '易结erp数据统计');
        if($user_type == 1)
        {
            $this->email->to('han.shuo@easywed.cn,lu.zheng@easywed.cn,luo.liang@easywed.cn,zhang.lihong@easywed.cn,wang.dong@easywed.cn,tang.han@easywed.cn,li.xiao@easywed.cn,lu.guangqing@easywed.cn,jin.lin@easywed.cn,ni.xuejia@easywed.cn');
            //$this->email->to('lu.zhengfei@easywed.cn');
        }
        else if($user_type == 2)
        {
            $this->email->to('lu.zheng@easywed.cn,zhang.lihong@easywed.cn,lu.guangqing@easywed.cn,jin.lin@easywed.cn,ni.xuejia@easywed.cn,li.kang@easywed.cn');
            //$this->email->to('lu.zhengfei@easywed.cn');
        }
        else
        {
            $this->email->to('lu.zhengfei@easywed.cn');
        }

        $email_title = '易结erp数据统计-日报';
        if($cycle == 'week')
        {
            $email_title = '易结erp数据统计-周报';
        }
        elseif($cycle == 'month')
        {
            $email_title = '易结erp数据统计-月报';
        }
        $this->email->subject($email_title);

        $this->email->message($content);
        foreach($files as $file)
        {
            if(!file_exists($file))continue;
            $this->email->attach($file);
        }

        if($this->email->send())
        {
            //成功
            foreach($files as $file)
            {
                if(file_exists($file))
                {
                    @unlink($file);
                }
            }
        }
    }

    /**
     * 生成html报表
     * @param $title 报表标题
     * @param $date 报表日期
     * @param $data_arr 报表数据
     * @return string
     */
    private function _make_html_str($title , $date , $data_arr)
    {
        $html_str = '<h2><b>'. $title .'</b>' . '('. $date .')</h2>';
        $html_str .= '<table border="1" style="border:1px solid #EFEFEF;" cellpadding="5" cellspacing="0">';
        $key_len = count($data_arr);
        foreach($data_arr as $dkey => $dval)
        {
            if($dkey == 0)
            {
                $html_str .= '<tr style="background:gray;color:white;">';
                foreach($dval as $val)
                {
                    $html_str .= '<th>'. $val .'</th>';
                }
                $html_str .= '</tr>';
            }
            elseif($dkey == ($key_len) - 1 && $dval[0] == '合计')
            {
                $html_str .= '<tr style="background:yellow;">';
                foreach($dval as $val)
                {
                    $html_str .= '<td>'. $val .'</td>';
                }
                $html_str .= '</tr>';
            }
            else
            {
                $html_str .= '<tr>';
                foreach($dval as $val)
                {
                    $html_str .= '<th>'. $val .'</th>';
                }
                $html_str .= '</tr>';
            }
        }
        $html_str .= '</table><hr />';
        return $html_str;
    }

    /**
     * 获取商机分配顾问的历史记录
     * @param array $bids 商机id
     * @param int $end_time 分配截止时间
     */
    private function _get_allocate_history($bids = array() , $end_time = 0)
    {
        if(count($bids) <= 0 || $end_time <= 0)return array();

        $dsql = 'SELECT bid,allocatetime FROM ew_business_adviser_log WHERE allocatetime < '.$end_time.' AND bid in('.implode(',' , $bids).')';
        $allocate_history = array();
        $allocate_history_query = $this->erp_conn->query($dsql);
        foreach($allocate_history_query->result_array() as $row)
        {
            $allocate_history[$row['bid']] = $row['allocatetime'];
        }

        unset($bids);
        return $allocate_history;
    }
}