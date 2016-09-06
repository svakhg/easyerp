<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 回款申请
 */
class Refundapply extends App_Controller
{
	public function __construct(){
        parent::__construct();
		$this->load->model('contract/contract_model', 'contract');
		$this->load->model('contract/contract_payment_details_model', 'payment');
        $this->load->model('contract/contract_refund_model','refund');
        $this->load->model("business/common_model" , 'buscommon');
        $this->load->model('business/business_model' , 'business');
        $this->load->model('sys_user_model', 'sum');
        $this->load->model('system/department_model', 'sdm');
        $this->load->model('finance/finance_refund_model' , 'f_refund');

        $this->load->helper('functions');
        $this->load->helper('array');
        $this->load->helper('ew_filter');
    }
	
	public function index()
    {
        list($refund_status,$refund_status_explan) = $this->refund->getRefundStatus();
        $this->_data['refund_status'] = $refund_status;
        $this->_data["refund_status_explan"] = $refund_status_explan;

        // 运营人员
        $operater_department = $this->sdm->getOperater();
        $this->_data['operater'] = $this->sum->findAll(array('department' => $operater_department['id'] , 'num_code <' => '99999000'), array(), array());

        $this->load->view("contract/apply_list_view",$this->_data);
    }

    /**
     * 搜索列表
     * @return mixed
     */
    public function rapayList()
    {
        $params = $this->input->get();
        $params = ew_filter_quote_html($params);
        $conditions = array();

        //获取回款申请-款项状态
        list($rf_status , $rf_status_explan) = $this->refund->getRefundStatus();
        //获取款项表-款项状态
        list($ps_status , $ps_status_explan) = $this->payment->getPaymentStatus();
        //获取合同状态
        list($cs_status , $cs_status_explan) = $this->contract->getContractStatus();
        $rf_status_flip = array_flip($rf_status);
        $cs_status_flip = array_flip($cs_status);

        //款项状态
        if(isset($params['refund_status']) && !empty($params['refund_status']))
        {
            $conditions['sign_refund_apply.status'] = intval($params['refund_status']);
        }

        //运营
        if(isset($params['operator_uid']) && !empty($params['operator_uid']))
        {
            $search_bids = array(0);
            $search_bids_query = $this->business->findAll(array('operate_uid' => intval($params['operator_uid'])) , array() , array() , 'id');
            foreach($search_bids_query as $tp)
            {
                $search_bids[] = $tp['id'];
            }
            $conditions['sign_contract.bid'] = $search_bids;
        }

        //婚礼日期-开始
        if(isset($params['wed_date_start']) && !empty($params['wed_date_start']))
        {
            $conditions['wed_date >'] = strtotime($params['wed_date_start']);
        }

        //婚礼日期-结束
        if(isset($params['wed_date_end']) && !empty($params['wed_date_end']))
        {
            $conditions['wed_date <'] = strtotime($params['wed_date_end']);
        }

        //商家呢称
        if(isset($params['shop_name']) && !empty($params['shop_name']))
        {
            $conditions['shopper_name like '] = '%' . $params['shop_name'] . '%';
        }

        //合同编号
        if(isset($params['contract_num']) && !empty($params['contract_num']))
        {
            $conditions['contract_num like '] = '%'. $params['contract_num'] .'%';
        }

        $perpages = intval(DD($params, 'pagesize', 20));
        $page = intval(DD($params, 'page', 1));
        $page = $page > 0 ? $page : 1;

        //获取数据字段
        $sel_fields = 'sign_refund_apply.id as rid ,contract_num , contract_status , sign_refund_apply.status as r_status , before_amount , after_amount , username , mobile , '.
                      'sign_contract.bid as busid , shopper_id , wed_date , sign_refund_apply.createtime as submit_time, '.
                      'total_amount , sign_refund_apply.updatetime as process_time , sign_refund_apply.sys_uid as inspector_uid , reason , before_notifyuid , after_notifyuid';

        $result_nums = $this->refund->findExtra($conditions ,array() , true , $sel_fields ,'left');
        $result = $this->refund->findExtra($conditions , array('start'=> ( $page -1 ) * $perpages ,'nums'=> $perpages) , false , $sel_fields , 'left');

        //整理数据
        $make_data = $bids = $bid_opuid = $sys_uids = $shop_ids = array();
        foreach($result as $item)
        {
            $tpdata = array();

            //整理运营uid
            if($item['busid'] > 0)
            {
                $bids[] = $item['busid'];
            }

            //审核人uid
            if($item['inspector_uid'] > 0)
            {
                $sys_uids[] = $item['inspector_uid'];
            }

            //商家uid
            if($item['shopper_id'] > 0)
            {
                $shop_ids[] = $item['shopper_id'];
            }

            //操作按钮状态判断
            if($item['r_status'] == $rf_status['finished'] || $item['r_status'] == $rf_status['rejected'])
            {
                $is_complete = 1; //不显示操作按钮
            }
            else if($item['r_status'] == $rf_status['confirmed'])
            {
                if($item['before_notifyuid'] > 0 && $item['after_notifyuid'] > 0)
                {
                    $is_complete = 1;
                }
                elseif($item['before_notifyuid'] > 0 || $item['after_notifyuid'] > 0)
                {
                    $is_complete = 2; //通知财务
                }
                else
                {
                    $is_complete = 3; //确认回款比例，通知财务
                }
            }
            else if($item['r_status'] == $rf_status['confirming'])
            {
                $is_complete = 4; //确认回款比例，驳回
            }
            $tpdata['is_complete'] = $is_complete;
            $tpdata['contract_num'] = $item['contract_num'];
            $tpdata['refund_status'] = isset($rf_status_flip[$item['r_status']]) ? $rf_status_explan[$rf_status_flip[$item['r_status']]] : '';
            $tpdata['total_amount'] = $item['total_amount'];
            $tpdata['before_amount'] = $item['before_amount'];
            $tpdata['after_amount'] = $item['after_amount'];
            $tpdata['username'] = $item['username'];
            $tpdata['mobile'] = $item['mobile'];
            $tpdata['shopper_id'] = $item['shopper_id'];
            $tpdata['shopper_name'] = '';
            $tpdata['studio_name'] = '';
            $tpdata['shopper_mobile'] = '';
            $tpdata['contract_status'] = isset($cs_status_flip[$item['contract_status']]) ? $cs_status_explan[$cs_status_flip[$item['contract_status']]] : '';
            $tpdata['wed_date'] = date('Y-m-d' , $item['wed_date']);
            $tpdata['submit_time'] = date('Y-m-d H:i:s' , $item['submit_time']);
            $tpdata['process_time'] = date('Y-m-d H:i:s' , $item['process_time']);
            $tpdata['inspector'] = '';
            $tpdata['refuse_reason'] = $item['reason'];
            $tpdata['is_notify'] = ($item['before_notifyuid'] > 0 || $item['after_notifyuid'] > 0) ? 1 : 0; //是否已通知财务
            $tpdata['operator'] = '';
            $tpdata['bid'] = $item['busid'];
            $tpdata['rid'] = $item['rid'];
            $tpdata['inspector_uid'] = $item['inspector_uid'];
            $make_data[] = $tpdata;
        }

        $sys_user_info = $this->user->findUsers();
        if(count($bids) > 0)
        {
            $bus_info = $this->business->findAll(array('id' => $bids) , array() , array() ,'id , operate_uid');
            foreach($bus_info as $bus)
            {
                if($bus['operate_uid'] > 0)
                {
                    $bid_opuid[$bus['id']] = $bus['operate_uid'];
                }
            }
        }

        $shop_map = array();
        if(count($shop_map) <= 0 && count($shop_ids) > 0)
        {
            $shop_infos = $this->buscommon->shoperInfo(array('uids' => implode(',' , $shop_ids)));
            foreach($shop_infos['rows'] as $shop)
            {
                $shop_map[$shop['uid']] = $shop;
            }
        }

        foreach($make_data as &$data)
        {
            if($data['shopper_id'] > 0 && isset($shop_map[$data['shopper_id']]))
            {
                $data['shopper_name'] = $shop_map[$data['shopper_id']]['nickname'];
                $data['studio_name'] = $shop_map[$data['shopper_id']]['studio_name'];
                $data['shopper_mobile'] = $shop_map[$data['shopper_id']]['mobile'];
            }

            if($data['bid'] > 0 && isset($bid_opuid[$data['bid']]) && isset($sys_user_info[$bid_opuid[$data['bid']]]))
            {
                $data['operator'] = $sys_user_info[$bid_opuid[$data['bid']]]['username'];
            }

            if($data['inspector_uid'] > 0 && isset($sys_user_info[$data['inspector_uid']]))
            {
                $data['inspector'] = $sys_user_info[$data['inspector_uid']]['username'];
            }
        }

        $info = array(
            'total' => $result_nums,
            'rows' => $make_data
        );
        return success($info);
    }

    /**
     * 驳回操作
     */
    public function doRefuse()
    {
        $parmas = $this->input->post();
        $parmas = ew_filter_quote_html($parmas);
        $rid = isset($parmas['rid']) ? intval($parmas['rid']) : 0;
        $reason = isset($parmas['reason']) ? $parmas['reason'] : '';

        //获取回款申请-款项状态
        list($r_status , $r_status_explan) = $this->refund->getRefundStatus();

        $refund_apply = $this->refund->findByCondition(array('id' => $rid));
        if(!isset($refund_apply['id']))
        {
            return failure('回款申请不存在');
        }

        if($refund_apply['status'] != $r_status['confirming'])
        {
            return failure('回款申请状态不是待确认');
        }

        //更新款项状态为已驳回,记录操作人及操作时间
        $contract_info = $this->contract->findByCondition(array('id' => $refund_apply['cid']));
        if(!isset($contract_info['id']))
        {
            return failure('合同信息不存在');
        }

        $post_params = array(
            'contract_num' => $contract_info['contract_num'],
            'status' => 0,//驳回
            'before_amount' => $refund_apply['before_amount'],
            'after_amount' => $refund_apply['after_amount'],
            'reason' => $reason
        );

        $resp = $this->refund->syncRefundStatus($post_params);
        if($resp)
        {
            $udp = array(
                'status' => $r_status['rejected'],
                'sys_uid' => $this->session->userdata("admin_id"),
                'reason' => $reason,
                'updatetime' => time()
            );
            $this->refund->updateByCondition($udp , array('id' => $refund_apply['id']));
            return success('驳回成功');
        }
        else
        {
            return failure('同步驳回状态失败');
        }
    }

    /**
     * 确认回款申请
     */
    public function confirmAaccount()
    {
        $parmas = $this->input->post();
        $parmas = ew_filter_quote_html($parmas);
        $rid = isset($parmas['rid']) ? intval($parmas['rid']) : 0;
        $before_amount = isset($parmas['before_amount']) ? $parmas['before_amount'] : 0;
        $after_amount = isset($parmas['after_amount']) ? $parmas['after_amount'] : 0;

        //获取回款申请-款项状态
        list($r_status , $r_status_explan) = $this->refund->getRefundStatus();

        $refund_apply = $this->refund->findByCondition(array('id' => $rid));

        if(!isset($refund_apply['id']))
        {
            return failure('回款申请不存在');
        }

        if($before_amount <= 0 || $after_amount <=0)
        {
            return failure('抱歉，婚礼金额不正确');
        }

        //更新款项状态为已驳回,记录操作人及操作时间
        $contract_info = $this->contract->findByCondition(array('id' => $refund_apply['cid']));
        if(!isset($contract_info['id']))
        {
            return failure('合同信息不存在');
        }

        $post_params = array(
            'contract_num' => $contract_info['contract_num'],
            'status' => 1,//通过
            'before_amount' => $before_amount,
            'after_amount' => $after_amount,
            'reason' => ''
        );

        $resp = $this->refund->syncRefundStatus($post_params);
        if($resp)
        {
            $udp = array(
                'before_amount' => $before_amount,
                'after_amount' => $after_amount,
                'sys_uid' => $this->session->userdata("admin_id"),
                'status' => $r_status['confirmed'],
                'updatetime' => time()
            );
            $this->refund->updateByCondition($udp , array('id' => $refund_apply['id']));
            return success('操作成功');
        }
        else
        {
            return failure('同步驳回状态失败');
        }
    }

    /**
     * 通知财务
     * @return mixed
     */
    public function notifyFinancial()
    {
        $parmas = $this->input->get();
        $parmas = ew_filter_quote_html($parmas);
        $rid = isset($parmas['rid']) ? intval($parmas['rid']) : 0;
        $amount_type = isset($parmas['amount_type']) ? $parmas['amount_type'] : 0;

        //获取回款申请-款项状态
        list($r_status , $r_status_explan) = $this->refund->getRefundStatus();
        //获取款项表-款项类型
        list($p_types , $p_types_explan) = $this->payment->getPaymentType();
        //获取款项表-款项状态
        list($p_status , $p_status_explan) = $this->payment->getPaymentStatus();
        //获取款项表-支付类型
        list($p_modes , $p_modes_explan) = $this->payment->getPayMode();
        //获取财务返款-返款状态
        list($f_status , $f_status_explan) = $this->f_refund->getWithDrawStatus();
        //获取财务返款-提现类型
        list($f_types , $f_types_explan) = $this->f_refund->getWithDrawType();

        $refund_apply = $this->refund->findByCondition(array('id' => $rid));
        if(!isset($refund_apply['id']))
        {
            return failure('回款申请不存在');
        }

        $contract_info = $this->contract->findByCondition(array('id' => $refund_apply['cid']));
        if(!isset($contract_info['id']))
        {
            return failure('合同信息不存在');
        }

        $payment_info = array();
        if($amount_type == 'before')
        {
            if($refund_apply['before_amount'] <= 0)
            {
                return failure('婚礼前回款金额不能为0');
            }
            //查询款项表此回款申请婚礼前回款信息
            $payment_info = $this->payment->findByCondition(array('refund_id' => $refund_apply['id'] , 'fund_type' => $p_types['before_payback']));
        }
        elseif($amount_type == 'after')
        {
            if($refund_apply['after_amount'] <= 0)
            {
                return failure('婚礼后回款金额不能为0');
            }
            //查询款项表此回款申请婚礼后回款信息
            $payment_info = $this->payment->findByCondition(array('refund_id' => $refund_apply['id'] , 'fund_type' => $p_types['after_payback']));
        }
        else
        {
            return failure('回款申请回款类型错误');
        }

        //如果没有通知财务，往款项表插入一条记录
        if(!isset($payment_info['id']))
        {
            $fund_type = $amount_type == 'before' ? $p_types['before_payback'] : $p_types['after_payback'];
            $amount = $amount_type == 'before' ? $refund_apply['before_amount'] : $refund_apply['after_amount'];

            $pay_insert_data = array(
                "cid" => $contract_info['id'],
                "bid" => $contract_info['bid'],
                "sid" => 0,
                "ew_pay_id" => 0,
                "serial_number" => date('YmdHis').rand(100000,999999),
                "status" => $p_status["to_confirm"],
                "amount" => $amount,
                "pay_mode" => $p_modes['other'],
                "fund_type" => $fund_type,
                "fund_describe" => "",
                "voucher_img" => "",
                "create_time" => time(),
                "refuse_reason" => "",
                "note" => '',
                'refund_id' => $refund_apply['id']
            );
            $payment_id = $this->payment->add($pay_insert_data);

            if($payment_id > 0)
            {
                $finance_refund_insert = array(
                    'payment_id' => $payment_id,
                    'create_time' => time(),
                    'shopper_id' => $contract_info['shopper_id'],
                    'status' => $f_status['wait_refund'],
                    'refund_id' => $refund_apply['id'],
                    'sign_type' => $f_types['three']
                );
                $this->f_refund->add($finance_refund_insert);

                //更新回款申请表
                $refund_udp = array();
                if($amount_type == 'before')
                {
                    $refund_udp = array('before_notifytime' => time() , 'before_notifyuid' => $this->session->userdata("admin_id"));
                    if($refund_apply['after_notifyuid'] > 0)
                    {
                        $refund_udp['status'] = $r_status['finished'];
                    }
                }
                elseif($amount_type == 'after')
                {
                    $refund_udp = array('after_notifytime' => time() , 'after_notifyuid' => $this->session->userdata("admin_id"));
                    if($refund_apply['before_notifyuid'] > 0)
                    {
                        $refund_udp['status'] = $r_status['finished'];
                    }
                }
                $this->refund->updateByCondition($refund_udp , array('id' => $refund_apply['id']));
                return success('通知财务成功');
            }
        }
        return failure('通知财务失败');
    }

    /**
     * 获取回款申请的财务明细列表
     */
    public function showRefundDetail()
    {
        $parmas = $this->input->get();
        $parmas = ew_filter_quote_html($parmas);
        $rid = isset($parmas['rid']) ? intval($parmas['rid']) : 0;

        $refund_apply = $this->refund->findByCondition(array('id' => $rid));
        if(!isset($refund_apply['id']))
        {
            return failure('回款申请不存在');
        }
        $sys_user_info = $this->user->findUsers();
        $output = array(
            array(
                'dateline_desc' => '婚礼前回款',
                'amount' => $refund_apply['before_amount'],
                'status' => $refund_apply['before_notifyuid'] > 0 ? '已通知财务' : '未通知财务',
                'notifytime' => $refund_apply['before_notifytime'] > 0 ? date('Y-m-d H:i:s' , $refund_apply['before_notifytime']) : 0,
                'operator' => isset($sys_user_info[$refund_apply['before_notifyuid']]) ? $sys_user_info[$refund_apply['before_notifyuid']]['username'] : '',
                'action' => 'before',
                'rid' => $rid
            ),
            array(
                'dateline_desc' => '婚礼后回款',
                'amount' => $refund_apply['after_amount'],
                'status' => $refund_apply['after_notifyuid'] > 0 ? '已通知财务' : '未通知财务',
                'notifytime' => $refund_apply['after_notifytime'] > 0 ? date('Y-m-d H:i:s' , $refund_apply['before_notifytime']) : 0,
                'operator' => isset($sys_user_info[$refund_apply['after_notifyuid']]) ? $sys_user_info[$refund_apply['after_notifyuid']]['username'] : '',
                'action' => 'after',
                'rid' => $rid
            )
        );
        $info = array(
            'total' => 2,
            'rows' => $output
        );
        return success($info);
    }
}