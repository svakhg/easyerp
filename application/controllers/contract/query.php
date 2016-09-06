<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Query extends App_Controller
{
	public function __construct(){
        parent::__construct();
		$this->load->model('contract/contract_model', 'contract');
		$this->load->model('contract/contract_ext_model', 'contract_ext');
		$this->load->model('contract/contract_payment_details_model', 'payment');
		$this->load->model('sys_user_model', 'sum');
		$this->load->model('system/department_model', 'sdm');
        $this->load->model('finance/refund_payment_map_model' , 'refund_pay_map');
        $this->load->model('finance/finance_refund_model' , 'f_refund');

        $this->load->model("business/common_model" , 'buscommon');

        $this->load->helper('array');
        $this->load->helper('excel_tools');

    }
	
	/**
	 * 收支明细查询页面
	 */
	public function index()
	{

        //支付方式
        list($mode , $mode_explan) = $this->payment->getPayMode();
        $this->_data['mode'] = $mode;
        $this->_data['mode_explan'] = $mode_explan;

        //款项类型
        list($type , $type_explan) = $this->payment->getPaymentType();
        $this->_data['type'] = $type;
        $this->_data['type_explan'] = $type_explan;

        //签约方式
        list($sign_types , $sign_types_explan) = $this->contract->getTypes();
        $this->_data['sign_types'] = $sign_types;
        $this->_data['sign_types_explan'] = $sign_types_explan;

		$this->load->view('contract/confirm/query',$this->_data);
	}
    /**
    * 收款确认检索列表
    * @return mixed
    */
    public function search($export = false)
    {
        $params = $this->input->get();

        $conditions = array();
        //收支类型
         if($params['pay_type'] == 'pay')//支
         {
             $conditions['fund_type >= '] = 10;
         }else if($params['pay_type'] == 'colse'){//收
             $conditions['fund_type <'] = 10;
         }
        if ($params['pay_by']) {
            $conditions['pay_mode'] = $params['pay_by'];
        }
        //签约方式
        if ($params['sign_type']) {
            $conditions['type'] = $params['sign_type'];
        }


        $contract_num = isset($params['contract_num']) ? $params['contract_num'] : "";
        $serial_number = isset($params['serial_number']) ? $params['serial_number'] : "";
        if($contract_num){
            $search_payids = array();
            //根据合同编号获取合同id
            $contract_info = $this->contract->findByCondition(array('contract_num' => $contract_num));

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
        if($serial_number){
            $conditions['serial_number'] = $serial_number;
        }
        //两个表里的creat_time 是一样的
        if ($params['pay_time_start']) {
            $conditions['ew_sign_contract_payment_details.pay_time >='] = strtotime($params['pay_time_start']);
        }

        if ($params['pay_time_end']) {
            $conditions['ew_sign_contract_payment_details.pay_time <='] = strtotime($params['pay_time_end']);
        }

        //款项状态
        list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
        //收支明细列表只允许已确认的数据
        $conditions['ew_sign_contract_payment_details.status'] = $pstatus['confirmed'];

        // 分页处理
        $limit = array();
        if (!empty($params['pagesize']) && !empty($params['page'])) {
            if (is_numeric($params['pagesize']) && is_numeric($params['page'])) {

                $start = ($params['page'] - 1) * $params['pagesize'];
                $limit = array('nums' => $params['pagesize'], 'start' => $start);
            }
        }
        $sel_fields = '* ,ew_sign_contract_payment_details.create_time as pay_c_time,ew_sign_contract_payment_details.id as cid,type';
        $total = $this->payment->findExtra($conditions, array(), true , '*' , 'left');
        //导出数据
        if($export)
        {

            $data = $this->payment->findExtra($conditions, array(), false, $sel_fields , 'left');
            $formatData = $this->_getData($data);
            $exporter = new ExportDataExcel('browser', '收支表_' . date('Y-m-d') . '.xls');
            $exporter->initialize();
            $exporter->addRow(array('流水号','支付时间','收支类型','支付金额(元)','款项类型','支付方式','收款人','备注说明','记账人','处理时间','合同编号','签约方式'));
            foreach($formatData as $val)
            {
                $tmpdata = array(
                    $val['serial_number'],$val['pay_time'],$val['pay_type'],
                    $val['amount'],$val['fund_type'],$val['pay_mode'],
                    $val['pay_name'],$val['note'],$val['follow_username'],
                    $val['update_time'],$val['contract_num'],$val['sign_type'],
                );
                $exporter->addRow($tmpdata);
            }
            $exporter->finalize();
            exit();
        }
        else{
            $data = $this->payment->findExtra($conditions, $limit, false, $sel_fields , 'left');
           // print_r($data);die;
            $formatData = $this->_getData($data);
            //print_r($data);die;
            $info = array(
                'total' => $total,
                'rows' => $formatData
            );
            return success($info);

        }
    }
    public function _getData($data){
        //款项状态
        list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
        //支付方式
        list($mode , $mode_explan) = $this->payment->getPayMode();
        //款项类型
        list($type_fund , $type_explan) = $this->payment->getPaymentType();
        //签约方式
        list($sign_types , $sign_types_explan) = $this->contract->getTypes();
        $sign_types_flip = array_flip($sign_types);

        $tstatus = array_flip($pstatus);

        $mode = array_flip($mode);
        $type = array_flip($type_fund);
        $sys_uids = $shopper_id = $cash_shopper_ids = $cash_pay_ids = array();

        //获取双方提现的商户
        foreach($data as $val)
        {
            if($val['fund_type'] == $type_fund['both_payback'])
            {
                $cash_pay_ids[] = $val['id'];
            }
        }
        $cash_shopper_info = array();
        if(count($cash_pay_ids) > 0)
        {
            $cash_shopper_query = $this->f_refund->findAll(array('payment_id' => $cash_pay_ids));
            foreach($cash_shopper_query as $query)
            {
                $cash_shopper_ids[] = $query['shopper_id'];
            }
            $shop_infos = $this->buscommon->shoperInfo(array('uids' => implode(',' , $cash_shopper_ids)));
            foreach($shop_infos['rows'] as $shop)
            {
                $shop_map[$shop['uid']] = $shop;
            }
            foreach($cash_shopper_query as $query)
            {
                $cash_shopper_info[$query['payment_id']] = isset($shop_map[$query['shopper_id']]) ? $shop_map[$query['shopper_id']]['nickname'] : '';
            }
        }


        foreach($data as $key => &$order)
        {
            $sys_uids[] = $order['sys_uid'];
            if($order['fund_type'] < $type_fund["payback"]){  //收支类型
                $order['pay_type'] = '收';
                $order['pay_name'] = $order['shopper_name'];

                if($order['fund_type'] == $type_fund['both_payback'])
                {
                    $order['pay_name'] = isset($cash_shopper_info[$order['id']]) ? $cash_shopper_info[$order['id']] : '';
                }
            }else{
                $order['pay_type'] = '支';
                $order['pay_name'] = '';
            }

            $order['status'] = isset($tstatus[$order['status']]) ? $pstatus_explan[$tstatus[$order['status']]] : ''; //款项状态
            $order['pay_mode'] = isset($mode[$order['pay_mode']]) ? $mode_explan[$mode[$order['pay_mode']]] : ''; //支付方式
            if(empty($order['fund_describe'])){
                $order['fund_type'] = isset($type[$order['fund_type']]) ? $type_explan[$type[$order['fund_type']]] : ''; //款项类型
            }else{
                $order['fund_type'] = isset($order['fund_describe']) ? $order['fund_describe'] : ''; //款项类型
            }

//            $order['fund_type'] = isset($type[$order['fund_type']]) ? $type_explan[$type[$order['fund_type']]] : ''; //款项类型
//            $order['fund_type'] .= '-' . $order['fund_describe'];
            //处理时间
            if($order['update_time']==0){
                $order['update_time']='-';
            }else{
                $order['update_time'] = date('Y-m-d H:i:s',$order['update_time']);
            }
            //提交时间
            if($order['pay_c_time']==0){
                $order['pay_c_time']='-';
            }else{
                $order['pay_c_time'] = date('Y-m-d H:i:s',$order['pay_c_time']);
            }
			//到账时间
            if($order['pay_time']==0){
                $order['pay_time']='-';
            }else{
                $order['pay_time'] = date('Y-m-d H:i:s',$order['pay_time']);
            }
            $shopper_id[]= $order['shopper_id'];
            $order['contrant']=array(
                'shopper_name'=>$order['shopper_name'],
                'username'=>$order['username'],
                'wed_date'=>$order['wed_date'],
                'wed_location'=>$order['wed_location'],
                'wed_amount'=>$order['wed_amount'],
                'sign_img'=>$order['sign_img'],
                'number_img'=>$order['number_img'],
            );
            $order['sign_type'] = isset($sign_types_flip[$order['type']]) ? $sign_types_explan[$sign_types_flip[$order['type']]] : '';
        }
        if(count($sys_uids) > 0)
        {
            $sys_users = $this->user->findUsers(array('id' => $sys_uids));
        }
        foreach($data as $key => &$order)
        {

            $order['follow_username'] = isset($sys_users[$order['sys_uid']]) ? $sys_users[$order['sys_uid']]['username'] : '-'; //记账人

        }
        return $data;
    }

    /**
     * 导出收支列表
     */
    public function exportcsv()
    {
        $this->search(true);
    }




}