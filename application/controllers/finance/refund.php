<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * author: easywed
 * createTime: 15/12/28 14:41
 * description:财务返款管理.
 */

class Refund extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('business/common_model' , 'buscommon');
        $this->load->model('business/business_model' , 'business');
        $this->load->model('contract/contract_model' , 'contract');
        $this->load->model('contract/contract_payment_details_model', 'payment');
        $this->load->model('finance/refund_payment_map_model' , 'refund_pay_map');
        $this->load->model('finance/finance_refund_model' , 'f_refund');
        $this->load->model('sys_user_model', 'sum');
        $this->load->model('system/roles_model', 'srm');
        $this->load->model('system/department_model', 'sdm');

        $this->load->helper('ew_filter');
    }

    /**
     * 财务返款首页
     */
    public function index()
    {
        //获取签约方式
        list($sign_types , $_) = $this->contract->getTypes();
        //获取财务返款款项状态
        list($wd_status , $wds_explan) = $this->f_refund->getWithDrawStatus();
        //获取财务返款类型
        list($wd_types , $wdt_explan) = $this->f_refund->getWithDrawType();
        //获取款项表支付类型
        list($pay_modes , $paym_explan) = $this->payment->getPayMode();

        $this->_data['wd_status'] = $wd_status;
        $this->_data['wd_status_explan'] = $wds_explan;
        $this->_data['wd_types'] = $wd_types;
        $this->_data['wd_types_explan'] = $wdt_explan;
        $this->_data['pay_modes'] = array('alipay' , 'e_bank' , 'cash', 'other');
        $this->_data['pay_modes_explan'] = $paym_explan;

        $this->load->view('finance/refund_list_view' , $this->_data);
    }

    /**
     * 列表检索
     */
    public function search()
    {
        $params = $this->input->get();
        $params = ew_filter_quote_html($params);
        $conditions = array();

        //获取款项类型
        list($fund_types , $_) = $this->payment->getPaymentType();
        //获取签约方式
        list($contract_types , $contract_types_explan) = $this->contract->getTypes();
        //获取支付方式
        list($pay_modes , $pay_modes_explan) = $this->payment->getPayMode();
        //获取财务返款款项状态
        list($wd_status , $wds_explan) = $this->f_refund->getWithDrawStatus();
        //获取财务返款类型
        list($wd_types , $wdt_explan) = $this->f_refund->getWithDrawType();

        $pay_modes_flip = array_flip($pay_modes);
        $contract_types_flip = array_flip($contract_types);
        $wd_status_flip = array_flip($wd_status);
        $wd_types_flip = array_flip($wd_types);

        //合同编号
        if(isset($params['contract_num']) && !empty($params['contract_num']))
        {
            $search_payids = array();
            //根据合同编号获取合同id
            $contract_info = $this->contract->findByCondition(array('contract_num' => $params['contract_num']));

            if(!$contract_info)
            {
                $contract_info['sign_contract_payment_details.id'] = 0;
            }
            else
            {
                $search_payids[] = 0;
                //查询双方提现记录中是否有此合同
                $refund_payment_map_query = $this->refund_pay_map->findAll(array('cid' => $contract_info['id']));
                foreach($refund_payment_map_query as $data)
                {
                    $search_payids[] = $data['payment_id'];
                }

                //查询三方提现记录中是否有此合同
                $payment_query = $this->payment->findAll(array('cid' => $contract_info['id']));
                foreach($payment_query as $data)
                {
                    $search_payids[] = $data['id'];
                }
                $conditions['sign_contract_payment_details.id'] = $search_payids;
            }
        }

        //款项状态
        $refund_status = isset($params['refund_status']) ? $params['refund_status'] : 0;
        if(in_array($refund_status , $wd_status))
        {
            $conditions['sign_finance_refund.status'] = $refund_status;
        }

        //签约方式
        if(isset($params['sign_type']))
        {
            if($params['sign_type'] == $contract_types['two'])
            {
                $conditions['sign_type'] = $wd_types['both'];
            }
            elseif($params['sign_type'] == $contract_types['three'])
            {
                $conditions['sign_type'] = $wd_types['three'];
            }
        }

        //提交时间-开始
        if(isset($params['commit_begin_time']) && !empty($params['commit_begin_time']))
        {
            $conditions['sign_finanace_refund.create_time > '] = strtotime($params['commit_begin_time']);
        }

        //提交时间-结束
        if(isset($params['commit_end_time']) && !empty($params['commit_end_time']))
        {
            $conditions['sign_finanace_refund.create_time < '] = strtotime($params['commit_end_time']);
        }

        $shop_map = array();
        //商家呢称
        if(isset($params['shopper_name']) && !empty($params['shopper_name']))
        {
            //根据商家呢称获取商家uid
            $shop_infos = $this->buscommon->shoperInfo(array('keyword' => $params['shopper_name']));

            foreach($shop_infos as $shop)
            {
                $shop_map[$shop['id']] = $shop;
            }

            if(count($shop_map) > 0)
            {
                $conditions['shopper_id'] = array_keys($shop_map);
            }
        }

        $perpages = intval(DD($params, 'pagesize', 20));
        $page = intval(DD($params, 'page', 1));
        $page = $page > 0 ? $page : 1;

        //获取数据字段
        $sel_fields = 'sign_finance_refund.id as fid , sign_finance_refund.status as f_status , sign_type, sign_finance_refund.account_name as f_a_n, sign_finance_refund.bank_name as f_b_n, sign_finance_refund.bank_account as f_b_a, pay_time , pay_mode , cid , note , sign_contract_payment_details.create_time as submit_time,' .
                      'shopper_id , amount , sign_contract_payment_details.bid as busid , sign_contract_payment_details.update_time as process_time'.
                      ', sys_uid , serial_number';

        $result_nums = $this->f_refund->findExtra($conditions ,array() , true , $sel_fields ,'left');
        $result = $this->f_refund->findExtra($conditions , array('start'=> ( $page -1 ) * $perpages ,'nums'=> $perpages) , false , $sel_fields , 'left');

        //整理数据
        $make_data = $contract_ids = $sys_uids = $bids = $shop_ids = $bid_opuid = array();
        foreach($result as $item)
        {
            $tpdata = array();

            //整理合同id
            if($item['cid'] > 0)
            {
                $contract_ids[] = $item['cid'];
            }

            //整理商家uid
            if($item['shopper_id'] > 0)
            {
                $shop_ids[] = $item['shopper_id'];
            }

            //整理记帐人uid
            if($item['sys_uid'] > 0)
            {
                $sys_uids[] = $item['sys_uid'];
            }

            //整理商机id
            if($item['busid'] > 0)
            {
                $bids[] = $item['busid'];
            }

            $tpdata['is_both'] = $item['sign_type'] == $wd_types['both'] ? 1 : 0;
            $tpdata['status'] = $item['f_status'];
            $tpdata['refund_txt'] = isset($wd_status_flip[$item['f_status']]) ? $wds_explan[$wd_status_flip[$item['f_status']]] : '';
            $tpdata['amount'] = $item['amount'];
            $tpdata['pay_time'] = $item['pay_time'] > 0 ? date('Y-m-d H:i:s' , $item['pay_time']) : '';
            $tpdata['pay_mode'] = isset($pay_modes_flip[$item['pay_mode']]) ? $pay_modes_explan[$pay_modes_flip[$item['pay_mode']]] : '';
            $tpdata['note'] = $item['note'];
            $tpdata['sign_type'] = isset($wd_types_flip[$item['sign_type']]) ? $wdt_explan[$wd_types_flip[$item['sign_type']]] : '';
            $tpdata['create_time'] = date('Y-m-d H:i:s' , $item['submit_time']);
            $tpdata['update_time'] = ($item['f_status'] == $wd_status['done_refund'] && $item['process_time'] > 0) ? date('Y-m-d H:i:s' , $item['process_time']) : '';
            $tpdata['account'] = '';
            $tpdata['operation'] = '';
            $tpdata['serial_number'] = ($item['f_status'] == $wd_status['done_refund']) ?  $item['serial_number'] : '';
            $tpdata['shopper_id'] = $item['shopper_id'];
            $tpdata['shopper_name'] = '';
            $tpdata['studio_name'] = '';
            $tpdata['contract_num'] = '';
            $tpdata['sys_uid'] = $item['sys_uid'];
            $tpdata['bid'] = $item['busid'];
            $tpdata['cid'] = $item['cid'];
            $tpdata['fid'] = $item['fid'];
            $tpdata['cashing_bank_name'] = $item['f_b_n'];
            $tpdata['cashing_account_name'] = $item['f_a_n'];
            $tpdata['cashing_bank_account'] = $item['f_b_a'];
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

        $contract_map = array();
        if(count($contract_ids) > 0)
        {
            $contract_info = $this->contract->findAll(array('id' => $contract_ids) , array() , array() , 'id , contract_num');
            foreach($contract_info as $info)
            {
                $contract_map[$info['id']] = $info['contract_num'];
            }
        }

        foreach($make_data as &$data)
        {
            if($data['shopper_id'] > 0 && isset($shop_map[$data['shopper_id']]))
            {
                $data['shopper_name'] = $shop_map[$data['shopper_id']]['nickname'];
                $data['studio_name'] = $shop_map[$data['shopper_id']]['studio_name'];
            }

            if($data['bid'] > 0 && isset($bid_opuid[$data['bid']]) && isset($sys_user_info[$bid_opuid[$data['bid']]]))
            {
                $data['operation'] = $sys_user_info[$bid_opuid[$data['bid']]]['username'];
            }

            if($data['cid'] > 0 && isset($contract_map[$data['cid']]))
            {
                $data['contract_num'] = $contract_map[$data['cid']];
            }

            if($data['sys_uid'] > 0 && isset($sys_user_info[$data['sys_uid']]))
            {
                $data['account'] = $sys_user_info[$data['sys_uid']]['username'];
            }
        }

        $info = array(
            'total' => $result_nums,
            'rows' => $make_data
        );
        return success($info);
    }

    /**
     * 获取_双方合同提现_二级款项合同内容
     */
    public function showDetail()
    {
        //获取合同状态
        list($c_status , $c_explan) = $this->contract->getContractStatus();
        $c_status_flip = array_flip($c_status);

        $params = $this->input->get();

        $fid = isset($params['fid']) ? intval($params['fid']) : 0;
        if($fid > 0)
        {
            $f_refund_info = $this->f_refund->findByCondition(array('id' => $fid));

            if(!isset($f_refund_info['id']))
            {
                return failure("提现记录不存在");
            }

            $cond_pids = array();
            $refund_payment_map_query = $this->refund_pay_map->findAll(array('payment_id' => $f_refund_info['payment_id']));
            foreach($refund_payment_map_query as $data)
            {
                $cond_pids[] = $data['sub_payment_id'];
            }

            $output = array();
            if(count($cond_pids) > 0)
            {
                $sel_fields = 'contract_num , contract_status , wed_place , wed_date , amount ';
                $result = $this->payment->findExtra(array('sign_contract_payment_details.id' => $cond_pids) , array() , false , $sel_fields);
                foreach($result as $data)
                {
                    $output[] = array(
                        'contract_num' => $data['contract_num'],
                        'contract_status' => isset($c_status_flip[$data['contract_status']]) ? $c_explan[$c_status_flip[$data['contract_status']]] : '',
                        'wed_place' => $data['wed_place'],
                        'wed_date' => date('Y-m-d H:i:s' , $data['wed_date']),
                        'amount' => $data['amount']
                    );
                }
                $info = array(
                    'total' => count($output),
                    'rows' => $output
                );
                return success($info);
            }
        }

        return failure("操作失败");
    }

    /**
     * 给商家返款
     */
    public function doRefund()
    {
        $params = $this->input->post();
        $params = ew_filter_quote_html($params);

        //获取财务返款类型
        list($wd_types , $wdt_explan) = $this->f_refund->getWithDrawType();
        //获取财务返款款项状态
        list($wd_status , $wds_explan) = $this->f_refund->getWithDrawStatus();
        //获取款项类型
        list($pay_types , $payt_explan) = $this->payment->getPaymentType();
        //获取款项表款项状态
        list($pay_status , $pays_explan) = $this->payment->getPaymentStatus();
        //获取款项表支付类型
        list($pay_modes , $paym_explan) = $this->payment->getPayMode();
        //获取合同表合同状态
        list($contract_status , $contracts_explan) = $this->contract->getContractStatus();
        //获取合同表款项状态
        list($contract_fundstatus , $contractf_explan) = $this->contract->getFundStatus();
        //主站支付方式
        $master_pay_mode = array('alipay' => 2 , 'e_bank' => 3 , 'cash' => 4 ,'other'=>5);

        $fid = isset($params['fid']) ? intval($params['fid']) : 0;
        $pay_mode = isset($params['pay_mode']) ? $params['pay_mode'] : 0;
        $pay_time = isset($params['pay_time']) ? strtotime($params['pay_time']) : 0;
        $pay_note = isset($params['pay_note']) ? $params['pay_note'] : '';

        if(!$fid || !$pay_mode || !$pay_time)
        {
            return failure('提交参数错误');
        }

        if(!isset($master_pay_mode[$pay_mode]))
        {
            return failure('支付方式不存在');
        }

        $f_refund_info = $this->f_refund->findByCondition(array('id' => $fid));
        if($f_refund_info['status'] != $wd_status['wait_refund'])
        {
            return failure('款项状态不正确');
        }

        if(isset($f_refund_info['id']))
        {
            $syncFlag = false;
            if($f_refund_info['sign_type'] == $wd_types['three'])
            {
                $payment_info = $this->payment->findByCondition(array('id' => $f_refund_info['payment_id']));
                if(!isset($payment_info['id']))
                {
                    return failure('款项信息不存在');
                }

                $contract_info = $this->contract->findByCondition(array('id' => $payment_info['cid']));
                if(!isset($contract_info['id']))
                {
                    return failure('合同信息不存在');
                }

                $cash_type = '';
                if($payment_info['fund_type'] == $pay_types['before_payback'])
                {
                    $cash_type = 'before';
                }
                elseif($payment_info['fund_type'] == $pay_types['after_payback'])
                {
                    $cash_type = 'after';
                }

                $post_params = array(
                    'contract_num' => $contract_info['contract_num'],
                    'payment_time' => $pay_time,
                    'amount' => $payment_info['amount'],
                    'mode' => $master_pay_mode[$pay_mode],
                    'type' => $cash_type,
                    'remark' => $pay_note
                );
                //通知主站三方回款
                $syncFlag = $this->f_refund->syncToBackMoney($post_params);
            }
            elseif($f_refund_info['sign_type'] == $wd_types['both'])
            {
                $post_params = array(
                    'cash_id' => $f_refund_info['ew_cash_id'],
                    'payment_time' => $pay_time,
                    'shopper_uid' => $f_refund_info['shopper_id'],
                    'remark' => $pay_note
                );
                //通知主站双方提现
                $syncFlag = $this->f_refund->syncToCash($post_params);
            }
            else
            {
                return failure('状态不正确');
            }
            //更新款项信息和返款信息状态
            if($f_refund_info['payment_id'] > 0 && $syncFlag)
            {
                $udp = array(
                    'pay_time' => $pay_time,
                    'status' => $pay_status['confirmed'],
                    'update_time' => time(),
                    'pay_mode' => $pay_modes[$pay_mode],
                    'sys_uid' => $this->session->userdata("admin_id"),
                    'note' => $pay_note
                );
                $this->payment->updateByCondition($udp , array('id' => $f_refund_info['payment_id']));
                $this->f_refund->updateByCondition(array('status' => $wd_status['done_refund']) , array('id' => $f_refund_info['id']));

                //如果是双方回款，则把合同状态置为已完成，返款状态置为已返首次款
                if($f_refund_info['sign_type'] == $wd_types['both'])
                {
                    $refund_payment_map_query = $this->refund_pay_map->findAll(array('payment_id' => $f_refund_info['payment_id']));
                    foreach($refund_payment_map_query as $data)
                    {
                        $contract_ids[] = $data['cid'];
                        if(count($contract_ids) > 0)
                        {
                            $this->contract->updateByCondition(array('contract_status' => $contract_status['completed'] , 'funds_status' => $contract_fundstatus['already_first_back']),array('id' => $contract_ids));
                        }
                    }
                }

                return success('操作成功');
            }
        }
        return failure('操作失败');
    }
}
