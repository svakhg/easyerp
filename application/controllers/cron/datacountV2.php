<?php
/**
 * author: easywed
 * createTime: 15/11/11 11:02
 * description:易结网数据统计_2.0
 */
set_time_limit(0);
class DatacountV2 extends Base_Controller
{
    private $_excel_storage = 'logs/';
    private $_md5_key = 'Ys6MNpKue2yBQM56rOcaZcAYX9FCW4YA';

    private $_bsource_flip;
    private $_invalid_status;
    private $_parted_status;
    private $_build_status;
    private $_follow_status;
    private $_bsource_explan;

    private $_bstatus;
    private $_bsource;
    private $_tstatus;
    private $_contract_status;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('business/business_model' , 'business');
        $this->load->model("business/common_model" , 'buscommon');
        $this->load->model('business/business_shop_map_model' , 'shopmap');
        $this->load->model('sys_user_model', 'sum');
        $this->load->model('system/department_model', 'sdm');
        $this->load->model('contract/contract_model' , 'contract');

        $this->load->helper('excel_tools');

        $this->load->library('email');

        //初始化状态
        list($bstatus , $_) = $this->business->getBusinessStatus();
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($tstatus , $_) = $this->business->getTradeStatus();
        list($cstatus , $_) = $this->contract->getContractStatus();

        $this->_bstatus = $bstatus;
        $this->_bsource = $bsource;
        $this->_tstatus = $tstatus;
        $this->_contract_status = $cstatus;
        $this->_bsource_explan = $bsource_explan;

        $this->_bsource_flip = array_flip($bsource);
        $this->_invalid_status = array($bstatus['follow_noanswer'], $bstatus['garbage_invalid_info'], $bstatus['garbage_three_times'], $bstatus['garbage_other']);
        $this->_parted_status = array($bstatus['parted'], $bstatus['parted_n_4']);
        $this->_build_status = array($bstatus['build'] , $bstatus['parted'] , $bstatus['parted_n_4']);
        $this->_follow_status = array($bstatus['follow_next'] , $bstatus['3days_ago']);
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
                $html_msg = $h1 = $h2 = $h3 = $h4 = "";
                $files[] = array();
                $files[] = $this->_week_channel_process(2 , $h1);
                $html_msg .= $h1;
                $files[] = $this->_week_channel_build(2 , $h2);
                $html_msg .= $h2;
                $files[] = $this->_week_customer_process(2 , $h3);
                $html_msg .= $h3;
                $files[] = $this->_week_customer_build(2 , $h4);
                $html_msg .= $h4;
                $this->_send_email($files , $html_msg , 'week' , 2);
                break;
            case 'sendemail_month':
                break;
            case 'sendemail_day':
                $html_msg = $h1 = '';
                $files[] = array();
                $files[] = $this->_day_planer_signed(1 , $h1);
                $html_msg .= $h1;
                $this->_send_email($files , $html_msg , 'day' , 1);
                break;
            case 'sendemail':
                break;
        }
    }

    /**
     * 顾问部本周各渠道呼入处理情况
     * @param int $cycle
     * @param $html_msg
     */
    private function _week_channel_process($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_del FROM ew_business WHERE ( is_test = 0 AND createtime >=' . $begin_time . ' AND createtime <= '. $end_time . ')';
        $dquery = $this->erp_conn->query($dsql);

        $total_nums = $t_build = $t_follow = $t_invalid = $t_repeat = 0;
        $result = array();

        //整理已经分配顾问的商机
        $bids = $bus_map = array();
        foreach($dquery->result_array() as $row)
        {
            $bids[] = $row['id'];
        }
        $bus_map = $this->_get_allocate_history($bids , $begin_time);

        foreach($dquery->result_array() as $row)
        {
            if($row['source'] <= 0)continue;
            if(isset($bus_map[$row['id']]))continue;

            if($row['advisertime'] > $begin_time && $row['advisertime'] < $end_time)
            {
                //本周分配顾问的呼入总数
                if($row['status'] != $this->_bstatus['garbage_repeat'])
                {
                    $total_nums++;
                    isset($result[$row['source']]['new_add']) ? $result[$row['source']]['new_add']++ : $result[$row['source']]['new_add'] = 1;
                }

                //本周分配的建单客户
                if(in_array($row['status'] , $this->_build_status))
                {
                    $t_build++;
                    isset($result[$row['source']]['build']) ? $result[$row['source']]['build']++ : $result[$row['source']]['build'] = 1;
                }

                //待跟进
                if(in_array($row['status'] , $this->_follow_status))
                {
                    $t_follow++;
                    isset($result[$row['source']]['follow']) ? $result[$row['source']]['follow']++ : $result[$row['source']]['follow'] = 1;
                }

                //无效客户
                if(in_array($row['status'] , $this->_invalid_status))
                {
                    $t_invalid++;
                    isset($result[$row['source']]['invalid']) ? $result[$row['source']]['invalid']++ : $result[$row['source']]['invalid'] = 1;
                }
            }

            //无效-重复
            if($row['status'] == $this->_bstatus['garbage_repeat'] && $row['updatetime'] > $begin_time && $row['updatetime'] < $end_time)
            {
                $t_repeat++;
                isset($result[$row['source']]['repeat']) ? $result[$row['source']]['repeat']++ : $result[$row['source']]['repeat'] = 1;
            }

        }

        $file_name = APPPATH . '/logs/' . 'week_channel_process_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '顾问部本周各渠道呼入处理情况[week_channel_process]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $output[] = array(
            '商机来源','本周分配顾问的呼入总数' , '无效-重复' , '本周分配的建单客户' , '回捞的建单客户' , '待跟进' ,
            '无效客户' , '无效原因说明'
        );

        $exporter->addRow(
            $output[0]
        );

        foreach($this->_bsource_flip as $bkey => $bs)
        {
            $tpdata = array(
                $this->_bsource_explan[$bs],
                isset($result[$bkey]['new_add']) ? $result[$bkey]['new_add'] : 0,
                isset($result[$bkey]['repeat']) ? $result[$bkey]['repeat'] : 0,
                isset($result[$bkey]['build']) ? $result[$bkey]['build'] : 0,
                0,
                isset($result[$bkey]['follow']) ? $result[$bkey]['follow'] : 0,
                isset($result[$bkey]['invalid']) ? $result[$bkey]['invalid'] : 0,
                ''
            );
            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }

        $end_data = array('合计', $total_nums , $t_repeat , $t_build , 0 , $t_follow , $t_invalid , '');
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
     * 顾问部本周分配的建单客户各渠道情况
     * @param int $cycle
     * @param $html_msg
     */
    private function _week_channel_build($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_del,usertype FROM ew_business WHERE ( is_test = 0 AND advisertime >=' . $begin_time . ' AND advisertime <= '. $end_time . ')';
        $dquery = $this->erp_conn->query($dsql);

        //整理已经分配顾问的商机
        $bids = $bus_map = array();
        foreach($dquery->result_array() as $row)
        {
            $bids[] = $row['id'];
        }
        $bus_map = $this->_get_allocate_history($bids , $begin_time);

        $result = $total_build = $total_build_source = array();
        $total_nums = 0;
        foreach($dquery->result_array() as $row)
        {
            if($row['source'] <= 0 || empty($row['usertype']))continue;
            if(isset($bus_map[$row['id']]))continue;

            if(in_array($row['status'] , $this->_build_status) || $row['trade_status'] > 0)
            {
                $total_nums++;
                isset($result[$row['source']][$row['usertype']]) ? $result[$row['source']][$row['usertype']]++ : $result[$row['source']][$row['usertype']] = 1;
                isset($total_build[$row['usertype']]) ? $total_build[$row['usertype']]++ : $total_build[$row['usertype']] = 1;
                isset($total_build_source[$row['source']]) ? $total_build_source[$row['source']]++ : $total_build_source[$row['source']] = 1;
            }
        }

        $file_name = APPPATH . '/logs/' . 'week_channel_build' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '顾问部本周分配的建单客户各渠道情况[week_channel_build]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $ext_fields = array('C2','C6','C5和C6','C4','C5','C7');

        $output[] = array_merge(array('来源渠道' , '总建单') , $ext_fields);
        $exporter->addRow(
            $output[0]
        );

        foreach($this->_bsource_flip as $bkey => $bs)
        {
            $tpdata = array(
                $this->_bsource_explan[$bs],
                isset($total_build_source[$bkey]) ? $total_build_source[$bkey] : 0
            );

            foreach($ext_fields as $field)
            {
                $tmp_num = isset($result[$bkey][$field]) ? $result[$bkey][$field] : 0;
                $tpdata[] = $tmp_num;
            }

            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }

        $end_data = array(
            '合计' , $total_nums
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
     * 顾问部本周客户处理情况
     * @param int $cycle
     * @param $html_msg
     * @return string
     */
    private function _week_customer_process($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        $adviser_map = array();
        $department = $this->sdm->getWedAdviser();
        $adviser = $this->sum->findAll(array('department' => $department['id']), array(), array());
        foreach($adviser as $v)
        {
            $adviser_map[$v['id']] = 1;
        }

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_del FROM ew_business WHERE ( is_test = 0 AND createtime >=' . $begin_time . ' AND createtime <= '. $end_time . ')';
        $dquery = $this->erp_conn->query($dsql);

        //整理已经分配顾问的商机
        $bids = $bus_map = array();
        foreach($dquery->result_array() as $row)
        {
            $bids[] = $row['id'];
        }
        $bus_map = $this->_get_allocate_history($bids , $begin_time);

        $result = array();
        $total_nums = $t_build = $t_follow = $t_invalid = 0;
        foreach($dquery->result_array() as $row)
        {
            if($row['follower_uid'] <= 0) continue;
            if(isset($bus_map[$row['id']]))continue;

            if($row['advisertime'] > $begin_time && $row['advisertime'] < $end_time)
            {
                //本周分配顾问的呼入总数
                $total_nums++;
                isset($result[$row['follower_uid']]['new_add']) ? $result[$row['follower_uid']]['new_add']++ : $result[$row['follower_uid']]['new_add'] = 1;

                //本周分配的建单客户
                if(in_array($row['status'] , $this->_build_status))
                {
                    $t_build++;
                    isset($result[$row['follower_uid']]['build']) ? $result[$row['follower_uid']]['build']++ : $result[$row['follower_uid']]['build'] = 1;
                }

                //待跟进
                if(in_array($row['status'] , $this->_follow_status))
                {
                    $t_follow++;
                    isset($result[$row['follower_uid']]['follow']) ? $result[$row['follower_uid']]['follow']++ : $result[$row['follower_uid']]['follow'] = 1;
                }

                //无效客户
                if(in_array($row['status'] , $this->_invalid_status))
                {
                    $t_invalid++;
                    isset($result[$row['follower_uid']]['invalid']) ? $result[$row['follower_uid']]['invalid']++ : $result[$row['follower_uid']]['invalid'] = 1;
                }
            }
        }

        $file_name = APPPATH . '/logs/' . 'week_customer_process_' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '顾问部本周客户处理情况[week_customer_process]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));

        $output[] = array(
            '顾问名','本周分配顾问的呼入总数' , '本周分配的建单客户' , '回捞的建单客户' , '待跟进' ,
            '无效客户' , '无效原因说明'
        );

        $exporter->addRow(
            $output[0]
        );

        foreach($adviser as $user)
        {
            if($user['username'] == 'guwen' || $user['username'] == 'guwen1')continue;
            $tpdata = array(
                $user['username'],
                isset($result[$user['id']]['new_add']) ? $result[$user['id']]['new_add'] : 0,
                isset($result[$user['id']]['build']) ? $result[$user['id']]['build'] : 0,
                0,
                isset($result[$user['id']]['follow']) ? $result[$user['id']]['follow'] : 0,
                isset($result[$user['id']]['invalid']) ? $result[$user['id']]['invalid'] : 0,
                ''
            );
            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }

        $end_data = array(
            '合计' , $total_nums , $t_build , 0 , $t_follow , $t_invalid , ''
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
     * 顾问部本周分配的建单客户分布情况
     * @param int $cycle
     * @param $html_msg
     */
    private function _week_customer_build($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time($cycle);

        $adviser_map = array();
        $department = $this->sdm->getWedAdviser();
        $adviser = $this->sum->findAll(array('department' => $department['id']), array(), array());
        foreach($adviser as $v)
        {
            $adviser_map[$v['id']] = 1;
        }

        $dsql = 'SELECT id,follower_uid,source,status,trade_status,createtime,advisertime,updatetime,ordertime,is_del,usertype FROM ew_business WHERE ( is_test = 0 AND createtime >=' . $begin_time . ' AND createtime <= '. $end_time . ')';
        $dquery = $this->erp_conn->query($dsql);

        //整理已经分配顾问的商机
        $bids = $bus_map = array();
        foreach($dquery->result_array() as $row)
        {
            $bids[] = $row['id'];
        }
        $bus_map = $this->_get_allocate_history($bids , $begin_time);

        $result = $total_build = $total_build_user = array();
        $total_nums = 0;
        foreach($dquery->result_array() as $row)
        {
            if($row['follower_uid'] <= 0 || empty($row['usertype']))continue;
            if(isset($bus_map[$row['id']]))continue;

            if(in_array($row['status'] , $this->_build_status) || $row['trade_status'] > 0)
            {
                $total_nums++;
                isset($result[$row['follower_uid']][$row['usertype']]) ? $result[$row['follower_uid']][$row['usertype']]++ : $result[$row['follower_uid']][$row['usertype']] = 1;
                isset($total_build[$row['usertype']]) ? $total_build[$row['usertype']]++ : $total_build[$row['usertype']] = 1;
                isset($total_build_user[$row['follower_uid']]) ? $total_build_user[$row['follower_uid']]++ : $total_build_user[$row['follower_uid']] = 1;
            }
        }

        $file_name = APPPATH . '/logs/' . 'week_customer_build' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '顾问部本周分配的建单客户分布情况[week_customer_build]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));
        $ext_fields = array('C2','C6','C5和C6','C4','C5','C7');

        $output[] = array_merge(array('顾问名' , '总建单') , $ext_fields);
        $exporter->addRow(
            $output[0]
        );

        foreach($adviser as $user)
        {
            $tpdata = array(
                $user['username'],
                isset($total_build_user[$user['id']]) ? $total_build_user[$user['id']] : 0
            );

            foreach($ext_fields as $field)
            {
                $tmp_num = isset($result[$user['id']][$field]) ? $result[$user['id']][$field] : 0;
                $tpdata[] = $tmp_num;
            }

            $output[] = $tpdata;
            $exporter->addRow($tpdata);
        }

        $end_data = array(
            '合计' , $total_nums
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
     * 策划师签单率
     * @param int $cycle
     * @param $html_msg
     */
    private function _day_planer_signed($cycle = 1, &$html_msg)
    {
        $begin_time = $end_time = 0;
        list($begin_time , $end_time) = $this->_get_time();
        $begin_time = strtotime('2015-8-10 00:00');

        $shop_grade_map = array();
        //获取策划师列表
        $shop_list = $this->buscommon->shoperInfo(array('page' => 1 , 'pagesize' => 1000));
        //获取策划师等级
        $shop_grade = $this->buscommon->getShoperGrade();
        foreach($shop_grade as $info)
        {
            $shop_grade_map[$info['id']] = $info;
        }

        //获取已分单的商机列表
        $dsql = 'SELECT a.shop_id,a.status as s_status,a.face_status,a.bid,b.usertype,b.trade_status,b.createtime,b.ordertime,b.status as b_status'.
            ' FROM ew_business_shop_map a LEFT JOIN ew_business b ON a.bid=b.id WHERE b.is_test = 0 AND b.ordertime >= '. $begin_time .' AND b.ordertime<= '. $end_time;
        $result = array();
        $dquery = $this->erp_conn->query($dsql);
        foreach($dquery->result_array() as $row)
        {
            //分单数
            if(($row['s_status'] == Business_shop_map_model::STATUS_NOT || $row['s_status'] == Business_shop_map_model::STATUS_SIGN) && in_array($row['b_status'] , $this->_parted_status) && $row['trade_status'] != $this->_tstatus['invalid'])
            {
                isset($result[$row['shop_id']]['parted']) ? $result[$row['shop_id']]['parted']++ : $result[$row['shop_id']]['parted'] = 1;
            }



            //丢单数
            if($row['s_status'] == Business_shop_map_model::STATUS_LOST)
            {
                isset($result[$row['shop_id']]['closed']) ? $result[$row['shop_id']]['closed']++ : $result[$row['shop_id']]['closed'] = 1;
            }
        }

        //线下合同数
        $dsql = 'SELECT contract_status,create_time,contract_num,shopper_id,offline FROM ew_sign_contract WHERE is_test = 0 AND sign_time > ' . $begin_time . ' AND sign_time < ' . $end_time;
        $dquery = $this->erp_conn->query($dsql);
        foreach($dquery->result_array() as $row)
        {
            if(in_array($row['contract_status'] , array($this->_contract_status['confirmed'] , $this->_contract_status['completed'])) && preg_match('/^YJ[\d]+/is' , $row['contract_num']) && $row['offline'] == 1)
            {
                isset($result[$row['shopper_id']]['offline']) ? $result[$row['shopper_id']]['offline']++ : $result[$row['shopper_id']]['offline'] = 1;
            }

            //签约数
            if($row['contract_status'] == $this->_contract_status['confirmed'])
            {
                isset($result[$row['shopper_id']]['ordered']) ? $result[$row['shopper_id']]['ordered']++ : $result[$row['shopper_id']]['ordered'] = 1;
            }
        }

        $file_name = APPPATH . '/logs/' . 'day_planer_signed' . date('Y-m-d', $end_time) . '.xls';

        $output = array();
        $exporter = new ExportDataExcel('file', $file_name);
        $exporter->initialize();
        $table_title = '策划师签单率[day_planer_signed]';
        $table_date = '数据日期：' . date('Y-m-d', $begin_time) . ' 至 ' . date('Y-m-d', $end_time);
        $exporter->addRow(array($table_title));
        $exporter->addRow(array($table_date));

        $output[] = array_merge(array('策划师呢称' , '策划师类型' , '分单数' , '线下合同数' , '签约数' , '丢单数' , '签单率'));
        $exporter->addRow(
            $output[0]
        );

        foreach($shop_list['rows'] as $shop)
        {
            if(empty($shop['grade']))continue;
            $t_parted = isset($result[$shop['uid']]['parted']) ? $result[$shop['uid']]['parted'] : 0;
            $t_ordered = isset($result[$shop['uid']]['ordered']) ? $result[$shop['uid']]['ordered'] : 0;
            $t_closed = isset($result[$shop['uid']]['closed']) ? $result[$shop['uid']]['closed'] : 0;
            $t_offline = isset($result[$shop['uid']]['offline']) ? $result[$shop['uid']]['offline'] : 0;
            $t_ordered_per = 0;
            if(($t_parted + $t_offline) > 0)
            {
                $t_ordered_per = round(($t_offline + $t_ordered) / ($t_parted + $t_offline) * 100 , 2);
            }
            $t_ordered_per = $t_ordered_per . '%';

            $tpdata = array(
                $shop['nickname'] , isset($shop_grade_map[$shop['grade']]) ? $shop_grade_map[$shop['grade']]['grade_name'] : '',
                $t_parted , $t_offline , $t_ordered , $t_closed ,
                $t_ordered_per
            );
            $output[] = $tpdata;
            $exporter->addRow($tpdata);
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
            if($week != 6)
            {
                //die('not saturday');
            }
            $end_time = mktime(0,0,0,date('m'),date('d'),date('Y')) - 1;//周5 23:59:59
            $begin_time = $end_time + 1 - 3600 * 24 * 7;//上周六 00:00:00
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
            //$this->email->to('han.shuo@easywed.cn,lu.zheng@easywed.cn,luo.liang@easywed.cn,zhang.lihong@easywed.cn,wang.dong@easywed.cn,tang.han@easywed.cn,li.xiao@easywed.cn,lu.guangqing@easywed.cn,jin.lin@easywed.cn,ni.xuejia@easywed.cn,lu.zhengfei@easywed.cn');
            $this->email->to('li.xiang@easywed.cn,jin.lin@easywed.cn');
        }
        else if($user_type == 2)
        {
            $this->email->to('lu.zheng@easywed.cn,zhang.lihong@easywed.cn,lu.guangqing@easywed.cn,jin.lin@easywed.cn,ni.xuejia@easywed.cn');
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