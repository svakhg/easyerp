<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * erp合同写入数据的接口
 * by zhangmiao
 */

class Workercontract extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('contract/contract_model', 'contract');
        $this->load->model('contract/contract_refund_model', 'refund');
        $this->load->model('contract/contract_payment_details_model', 'payment');

        $this->load->helper('ew_filter');
    }

    /**
     * 策划师发起签约,上传合同
     *
     * @return mixed
     */
    public function addContract()
    {
        $data = $this->input->post();
        //查询business表是否存在交易编号，存在则把合同表中的bid赋值为business表中的id
        $business_info = $this->erp_conn->from("business")->where("tradeno",$data['number'])->get()->result_array();
        if(isset($business_info[0])){
            $business_id = $business_info[0]['id'];
        }else{
            return failure("商机编号不存在(no business_id)");
            //$business_id = 0;
        }
        $save_data = array();
        $save_data['bid'] = $business_id;

        $save_data['uid'] = DD($data, 'uid', 0);
        $save_data['username'] = DD($data, 'username');
        $save_data['mobile'] = DD($data, 'mobile');
        $save_data['contract_num'] = DD($data, 'contract_num');
        $save_data['wed_date'] = DD($data, 'wed_date') ? strtotime(DD($data, 'wed_date')) : 0;
        $save_data['wed_location'] = DD($data, 'wed_location');
        $save_data['wed_place'] = DD($data, 'wed_place');
        $save_data['wed_amount'] = DD($data, 'wed_amount');
        $save_data['discount_amount'] = DD($data, 'discount_amount', 0);
        $save_data['create_time'] = DD($data, 'create_time', 0);
        $save_data['number_img'] = $save_data['sign_img'] = '';
        $save_data['shopper_id'] = DD($data, 'shopper_id', 0);
        $save_data['shopper_name'] = DD($data, 'shopper_name');
        $save_data['stop_reason'] = DD($data, 'stop_reason');
        $save_data['upload_time'] = DD($data, 'sub_time', 0);


        $is_test = DD($data, 'is_test', 0);
        $save_data['is_test'] = $is_test == 1 ? 1 : 0;

        list($contract_types, $contract_types_explan) = $this->contract->getTypes();
        $type = DD($data, 'type', $contract_types['three']);
        $save_data['type'] = $type == $contract_types['two'] ? $contract_types['two'] : $contract_types['three']; // 双方合同：2，三方合同：1

        // 如果有合同图片表示合同状态进入了'已上传,带审核'状态.
        list($contract_fund_status, $contract_fund_status_explan) = $this->contract->getFundStatus();
        if(isset($data['contract_num']) && !empty($data['contract_num']) && isset($data['number_img']) && !empty($data['number_img']) && isset($data['sign_img']) && !empty($data['sign_img'])){
            $save_data['contract_status'] = $contract_fund_status['paid_advance']; // 已上传合同待审核
        }else{
            $save_data['contract_status'] = $contract_fund_status['topay_advance']; // 待上传合同
        }

        // 合同图片,如果合同是驳回后上传的合同,图片为空,
        $save_data['sign_img'] = DD($data, 'sign_img');
        $save_data['number_img'] = DD($data, 'number_img');

        //查询合同编号是否存在，存在则更新该条数据
        $contract_info = $this->erp_conn->from("sign_contract")->where("contract_num",$data['contract_num'])->get()->result_array();
        if(isset($contract_info[0])){
            $res_contract = $this->erp_conn->where("contract_num",$data['contract_num'])->update("sign_contract", $save_data);
        }else{
            $res_contract = $this->erp_conn->insert("sign_contract", $save_data);
        }

        if($res_contract == true){
            return success("success");
        }else{
            return failure("failure");
        }

        
    }

    /**
     * 合同类型选择错误后取消
     *
     * @return mixed
     */
    public function delContract()
    {
        $data = $this->input->post();
        $res = $this->erp_conn->update("sign_contract",array("is_del"=>1),array("contract_num"=>$data['contract_num']));
        if($res == true){
            return success("success");
        }else{
            return failure("failure");
        }
    }


    /**
     * 策划师托管资金
     *
     * @return mixed
     */
    public function addPayment()
    {
        $data = $this->input->post();

        //查询合同编号是否存在，存在则将cid赋值给要保存的数据
        if(!isset($data['contract_num']) || !is_string($data['contract_num']) || empty($data['contract_num']))
        {
            return failure('没有上传合同编号');
        }

        $contract_info = $this->erp_conn->from("sign_contract")->where("contract_num",$data['contract_num'])->get()->result_array();
        if(isset($contract_info[0])){
            $contract_id = $contract_info[0]['id'];
            $business_id = $contract_info[0]['bid'];
        }else{
            return failure("商机编号不存在(no business_id)");
        }

        // 第三期交易流程中没有中间合同一说
        $pay_data = array();
        $pay_data['cid'] = $contract_id;
        $pay_data['bid'] = $business_id;
        $pay_data['sid'] = 0;
        $pay_data['ew_pay_id'] = DD($data, 'ew_pay_id', 0);
        $pay_data['pay_mode'] = DD($data, 'pay_mode', 0);
        $pay_data['fund_describe'] = DD($data, 'fund_describe');
        $pay_data['amount'] = DD($data, 'amount', 0);
        $pay_data['voucher_img'] = DD($data, 'voucher_img');
        $pay_data['note'] = DD($data, 'note');
        $pay_data['create_time'] = $pay_data['update_time'] = DD($data, 'create_time', 0);
        $contract_type = DD($data, 'contract_type', 1);

        $is_test = DD($data, 'is_test', 0);
        $pay_data['is_test'] = $is_test == 1 ? 1 : 0;

        list($payment_status, $payment_status_explan) = $this->payment->getPaymentStatus();

        $paid = DD($data, 'paid', 0);
        if($paid)
        {
            $pay_data['pay_time'] = $pay_data['update_time'] = DD($data, 'payment_time', $pay_data['create_time']);
            $pay_data['status'] = $payment_status['confirmed'];
        }

        $pay_exist_data = $this->erp_conn->from("sign_contract_payment_details")->where("ew_pay_id",$data['ew_pay_id'])->get()->result_array();

        // 如果存在ew_pay_id,插入或者更新数据，
        // 否则，不保存payment数据
        if(isset($data['ew_pay_id']) && $data['ew_pay_id'] != 0){

            // 根据ew_pay_id查询,如果存在则更新
            if(isset($pay_exist_data[0])){
                $res_payment = $this->erp_conn->where("ew_pay_id",$data['ew_pay_id'])->update("sign_contract_payment_details", $pay_data);
            }else{
                !isset($pay_data['status']) && $pay_data['status'] = 1;
                $pay_data['serial_number'] = date('YmdHis').rand(100000,999999);
                $res_payment = $this->erp_conn->insert("sign_contract_payment_details", $pay_data);
            }

            //如果是双方合同 且 已经支付paid ：将合同更新为－》状态：已确认，款项状态：待首次返款
            if($paid && $contract_type==2){
                list($contract_status, $contract_status_explan) = $this->contract->getContractStatus();
                list($contract_fund_status, $contract_fund_status_explan) = $this->contract->getFundStatus();
                $contract_upd_data = array(
                    'contract_status' => $contract_status['confirmed'],
                    'funds_status' => $contract_fund_status['first_back'],
                    );
                $this->erp_conn->update("sign_contract",$contract_upd_data,array("id"=>$contract_id));
            }

        }
        return success("success");
    }


    /**
     * 策划师针对三方合同发起的回款申请
     *
     * @return mixed
     */
    public function requestBackMoney()
    {
        $data = $this->input->post();

        // 检查参数是否正确
        if(!isset($data['contract_num']) || !is_string($data['contract_num']) || empty($data['contract_num']))
        {
            return failure('没有上传合同编号');
        }

        // 查看合同是否存在
        $contract_info = $this->erp_conn->from("sign_contract")->where("contract_num",$data['contract_num'])->get()->result_array();
        if(isset($contract_info[0])){
            $contract_id = $contract_info[0]['id'];
        }else{
            return failure('合同'.$data['contract_num'].'不存在');
        }

        // 根据合同id获取可以回款的总金额
        $total_amount = $this->payment->findByContractID($contract_id, 5);
        list($status, $status_explan) = $this->refund->getRefundStatus();

        // 根据合同ID检测是否存在返款申请信息
        $refund_info = $this->erp_conn->from('sign_refund_apply')->where('cid', $contract_id)->where('status', $status['confirmed'])->get()->result_array();
        if(isset($refund_info[0]))
        {
            return success('返款申请已通过,不能重复申请');
        }
        $insert_datas = array(
            'cid' => $contract_id,
            'total_amount' => $total_amount,
            'createtime' => time(),
            'updatetime' => time(),
            'status' => $status['confirming']
        );
        $res_payment = $this->erp_conn->insert("sign_refund_apply", $insert_datas);
        return success("success");
    }

    /**
     * 策划师的提现操作
     */
    public function requestCashing()
    {
        $data = $this->input->post();

        $payment_ids = DD($data, 'payment_ids');
        $cash_id = DD($data, 'cash_id', 0);
        if(!is_string($payment_ids) || empty($payment_ids) || !is_numeric($cash_id) || $cash_id < 0)
        {
            return failure('提现操作参数错误');
        }

        $payment_ids = explode(',', $payment_ids);

        $payment_info = $this->erp_conn->from('sign_contract_payment_details', array('id', 'cid', 'ew_pay_id'))->where_in('ew_pay_id', $payment_ids)->get()->result_array();
        //echo $this->erp_conn->last_query();die;
        if(empty($payment_info))
        {
            return failure('没有对应的提现记录');
        }

        list($payment_type, $payment_type_explan) = $this->payment->getPaymentType();
        list($payment_mode, $payment_mode_explan) = $this->payment->getPayMode();
        $is_test = DD($data, 'is_test', 0);
        $is_test = $is_test == 1 ? 1 : 0;

        $bank_name = DD($data, 'bank_name'); // 银行名称
        $account_name = DD($data, 'account_name'); // 银行账户名称
        $bank_account = DD($data, 'bank_account'); // 银行账号

        $time = time();
        // 插入到款项表
        $insert_payment_params = array(
            'cid' => 0,
            'bid' => 0,
            'sid' => 0,
            'ew_pay_id' => 0,
            "serial_number" => date('YmdHis').rand(100000,999999),
            'amount' => DD($data, 'amount', 0),
            'fund_type' => $payment_type['both_payback'],
            'fund_describe' => '双方合同回款',
            'pay_mode' => 0,
            'create_time' => $time,
            'update_time' => $time,
            'is_test' => $is_test
        );
        $this->erp_conn->insert('sign_contract_payment_details', $insert_payment_params);
        $insert_payment_id = $this->erp_conn->insert_id();

        // 插入财务返款表
        $insert_finance_refund_params = array(
            'payment_id' => $insert_payment_id,
            'create_time' => $time,
            'shopper_id' => DD($data, 'shopper_uid', 0),
            'ew_cash_id' => $cash_id,
            'status' => 1,
            'sign_type' => 1,
            'bank_name' => $bank_name,
            'account_name' => $account_name,
            'bank_account' => $bank_account,
        );
        $this->erp_conn->insert('sign_finance_refund', $insert_finance_refund_params);

        // 插入到返款-款项映射表
        $insert_fund_map_params = array();
        foreach($payment_info as $p)
        {
            $insert_fund_map_params[] = array(
                'payment_id' => $insert_payment_id,
                'sub_payment_id' => $p['id'],
                'cid' => $p['cid']
            );
        }
        if(!empty($insert_fund_map_params))
        {
            $this->erp_conn->insert_batch('sign_refund_payment_map', $insert_fund_map_params);
        }

        return success('success');
    }

    /**
     * 插入一条完整的订单, 来源于新人直接在商家店铺预约即新人指定商家服务.
     *
     * 包括:商机,商家列表,合同信息,支付信息
     *
     * post params:
     *  array(
     *      // 商机与商家信息
     *      'business' => array(
     *              // 必填
     *              'uid' => '新人UID',
     *              'username' => '新人昵称',
     *              'mobile' => '新人手机号',
     *              'wed_date' => '婚礼日期',
     *              'shopper_uid' => '预约商家UID',
     *
     *              // 选填
     *              'shopper_name' => '预约商家昵称',
     *              'userpart' => '新人身份',
     *              'usertype' => '客户类型',
     *              'source' => '商机来源',
     *              'source_note' => '商机来源备注',
     *              'is_face' => '是否见面(1:是, 0:不是)',
     *              'is_sign' => '是否签约(1:是, 0:不是)',
     *              'is_test' => '是否是测试数据(0:不是, 1:是)'
     *      ),
     *      // 合同信息
     *      'contract' => array(
     *              // 必填
     *              'contract_num' => '合同编号',
     *              'type' => '合同类型类型(1:三方,2:双方;默认1)',
     *
     *              // 选填
     *              'offline' => '线下线上',
     *              'alias' => '商家类型别名',
     *              'archive' => '是否存档(1:未归档,2:已归档)',
     *              'is_test' => '是否是测试数(1:是,0:不是)'
     *      ),
     *      // 支付信息
     *      'payment' => array(
     *              // 必填
     *              'pay_id' => '主站支付ID',
     *              'amount' => '支付金额',
     *              'mode' => '支付方式',
     *
     *              // 选填
     *              'remark' => '支付备注',
     *              'is_test' => '是否是测试数(1:是,0:不是)',
     *              'type' => '资金类型',
     *              'type_string' => '资金类型描述',
     *              'pay_time' => '支付时间',
     *      )
     * )
     *
     */
    public function addFullOrder()
    {
        $data = $this->input->post();

        $business = DD($data, 'business');
        $contract = DD($data, 'contract');
        $payments = DD($data, 'payment');
        if(empty($business) || empty($contract) || empty($payments))
        {
            return failure('新人线下导单参数错误 ');
        }
        $this->load->model('business/business_model', 'business');
        $this->load->model('business/business_shop_map_model', 'shop_map');


        // 插入商机信息
        list($sources, $sources_explan) = $this->business->getBusinessSource();
        list($business_types, $business_types_explan) = $this->business->getBusinessType();
        list($trade_status, $trade_status_explan) = $this->business->getTradeStatus();
        list($business_status, $business_status_explan) = $this->business->getBusinessStatus();
        list($business_find_shop_type, $business_find_shop_type_explan) = $this->business->getFindShopType();

        $time = time();
        $tradeno = $this->business->generateTradeNo();
        $wed_date = DD($business, 'wed_date', 0);
        $wed_date = is_string($wed_date) ? strtotime($wed_date) : $wed_date;
        $insert_business_attr = array(
            'uid' => DD($business, 'uid', 0),
            'username' => DD($business, 'username'),
            'userpart' => DD($business, 'userpart', '7'),
            'usertype' => DD($business, 'usertype', 'C2'),
            'source' => DD($business, 'source', $sources['store_booking']),
            'source_note' => DD($business, 'source_note', $sources_explan['store_booking']),
            'mobile' => DD($business, 'mobile'),
            'tel' => '',
            'weixin' => '',
            'qq' => '',
            'other_contact' => '',
            'hotel_name' => '',
            'createtime' => $time,
            'signletime' => $time,
            'ordertime' => $time,
            'ordertype' => $business_types['wed_plan'],
            'updatetime' => $time,
            'status' => $business_status['parted'],
            'status_note' => '',
            'wed_date' => $wed_date,
            'tradeno' => $tradeno,
            'trade_status' => $trade_status['ordered'],
            'hmsr' => '',
            'source_url' => '',
            'is_face' => DD($business, 'is_face', 0),
            'is_sign' => DD($business, 'is_sign', 0),
            'is_test' => DD($business, 'is_test', 0)
        );

        $this->erp_conn->insert('business', $insert_business_attr);
        $insert_business_id = $this->erp_conn->insert_id();

        // 插入商机扩展信息
        $insert_business_extr_attr = array(
            'bid' => $insert_business_id,
            'weddate_note' => '',
            'location' => '',
            'wed_place' => '',
            'findtype' => $business_find_shop_type['people_self'],
            'findnote' => DD($business, 'shopper_uid', 0),
            'wish_contact' => '',
            'moredesc' => ''
        );
        $this->erp_conn->insert('business_extra', $insert_business_extr_attr);

        // 插入商家信息
        $insert_shop_map_attr = array(
            'bid' => $insert_business_id,
            'shop_id' => DD($business, 'shopper_uid', 0),
            'status' => 1, // 已签约
            'allocatetime' => $time,
            'face_status' => 2, // 已见面
            'is_test' => DD($business, 'is_test', 0),
            'meettime' => $time,
            'facetime' => $time,
        );
        $this->erp_conn->insert('business_shop_map', $insert_shop_map_attr);

        // 插入合同信息
        list($contract_status, $contract_status_explan) = $this->contract->getContractStatus();
        list($contract_fund_status, $contract_fund_status_explan) = $this->contract->getFundStatus();
        $insert_contract_attr = array(
            'bid' => $insert_business_id,
            'uid' => DD($business, 'uid', 0),
            'username' => DD($business, 'username'),
            'mobile' => DD($business, 'mobile'),
            'contract_num' => DD($contract, 'contract_num'),
            'wed_date' => $wed_date,
            'wed_amount' => DD($payments, 'amount', 0),
            'type' => DD($contract, 'type', 1), // 合同类型(默认三方)
            'offline' => DD($contract, 'offline', 1), // 线下线上(默认线下)
            'alias' => DD($contract, 'alias'),
            'contract_status' => $contract_status['confirmed'],
            'archive_status' => DD($contract, 'archive', 1), // 未归档
            'funds_status' => !empty($payments) ? $contract_fund_status['paid_advance'] : $contract_fund_status['topay_advance'],
            'create_time' => $time,
            'sign_time' => $time,
            'shopper_id' => DD($business, 'shopper_uid', 0),
            'shopper_name' => DD($business, 'shopper_name'),
            'stop_reason' => '',
            'is_test' => DD($contract, 'is_test', 0)
        );
        $this->erp_conn->insert('sign_contract', $insert_contract_attr);
        $insert_contract_id = $this->erp_conn->insert_id();

        // 插入支付信息
        list($payment_status, $payment_status_explan) = $this->payment->getPaymentStatus();
        list($payment_mode, $payment_mode_explan) = $this->payment->getPayMode();
        list($payment_type, $payment_type_explan) = $this->payment->getPaymentType();
        $insert_payment_attr = array(
            'cid' => $insert_contract_id,
            'bid' => $insert_business_id,
            'sid' => 0,
            'ew_pay_id' => DD($payments, 'pay_id', 0),
            'serial_number' => date('YmdHis').rand(100000,999999),
            'status' => $payment_status['confirmed'],
            'amount' => DD($payments, 'amount', 0),
            'pay_mode' => DD($payments, 'mode', $payment_mode['other']),
            'fund_type' => DD($payments, 'type', $payment_type['advance']),
            'fund_describe' => DD($payments, 'typestring', $payment_type_explan['advance']),
            'create_time' => $time,
            'update_time' => $time,
            'pay_time' => DD($payments, 'pay_time', $time),
            'is_test' => DD($payments, 'is_test', 0),
            'note' => DD($payments, 'remark')
        );
        $this->erp_conn->insert('sign_contract_payment_details', $insert_payment_attr);

        return success(array('tradeno' => $tradeno, 'bid' => DD($business, 'bid', 0)));
    }
}
