<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contract extends App_Controller
{
	public function __construct()
    {
        parent::__construct();
		$this->load->model('contract/contract_model', 'contract');
		$this->load->model('contract/contract_ext_model', 'contract_ext');
		$this->load->model('contract/contract_payment_details_model', 'payment');
		$this->load->model('business/business_model', 'business');
        $this->load->model('business/business_shop_map_model' , 'shopmap');
		$this->load->model('commons/region_model' , 'region');
		$this->load->model('sys_user_model', 'sum');
		$this->load->model("business/common_model" , 'buscommon');
        $this->load->model('finance/refund_payment_map_model' , 'refund_pay_map');
		$this->load->helper('functions');// 载入公共函数
		$this->load->helper('array');
		$this->load->helper('ew_filter');
    }

	/**
	 * 合同列表
	 */
	public function contractlist($export = false)
	{
		// 获取请求参数
		$params = $this->input->get();

		//状态处理
		list($contractstatus, $contractstatus_explan) = $this->contract->getContractStatus();
		list($archivestatus, $archivestatus_explan) = $this->contract->getArchiveStatus();
		list($fundstatus, $fundstatus_explan) = $this->contract->getFundStatus();

        // 合同类型
        list($contract_type, $contract_type_explan) = $this->contract->getTypes();

		$rar = array_flip($archivestatus);
		$rcon = array_flip($contractstatus);
		$rfun = array_flip($fundstatus);

		//款项状态
		list($paymentstatus, $paymentstatus_explan) = $this->payment->getPaymentStatus();
		//款项类型
		list($paymenttype, $paymenttype_explan) = $this->payment->getPaymentType();

		//条件处理
		$cond = array();


		!empty($params['mobile']) && $cond['autowhere']['sign_contract.mobile like'] = '%'.$params['mobile'].'%';
		!empty($params['username']) && $cond['autowhere']['sign_contract.username like'] = '%'.$params['username'].'%';
		!empty($params['contract_num']) && $cond['autowhere']['contract_num'] = $params['contract_num'];
		!empty($params['contract_status']) && $cond['autowhere']['contract_status'] = $params['contract_status'];
		!empty($params['archive_status']) && $cond['autowhere']['archive_status'] = $params['archive_status'];
		!empty($params['funds_status']) && $cond['autowhere']['funds_status'] = $params['funds_status'];
		!empty($params['wed_place']) && $cond['autowhere']['wed_place like'] = '%'.$params['wed_place'].'%';
		!empty($params['shopper_name']) && $cond['autowhere']['shopper_name like'] = '%'.$params['shopper_name'].'%';
		//测试数据
		if($this->session->userdata('is_test') == 0)
		{
			$cond['autowhere']['sign_contract.is_test'] = 0;
		}
		!empty($params['tradeno']) && $cond['autowhere']['tradeno'] = $params['tradeno'];
        //顾问
		!empty($params['follower_uid']) && $cond['autowhere']['follower_uid'] = $params['follower_uid'];
        //运营
        if(!empty($params['operate_uid']) && is_numeric ($params['operate_uid'])){
            $cond['autowhere']['operate_uid'] = $params['operate_uid'];
        }
        if(!empty($params['offline']) && is_numeric ($params['offline'])){
            $cond['autowhere']['offline'] = $params['offline'];
        }
        if(!empty($params['type']) && is_numeric ($params['type'])){
            $cond['autowhere']['type'] = $params['type'];
        }
		//付款状态
		if(!empty($params['payment_status']) && $params['payment_status'] == 1)
		{
			$cond['autowhere']['funds_status <'] = $fundstatus['paid_advance'];
		}
		if(!empty($params['payment_status']) && $params['payment_status'] == 2)
		{
			$cond['autowhere']['funds_status >='] = $fundstatus['paid_advance'];
		}

        $cond['autowhere']['ew_sign_contract.is_del'] = 0;

        !empty($params['sign_time_start']) && $cond['sign_time_start'] = $params['sign_time_start'];
		!empty($params['sign_time_end']) && $cond['sign_time_end'] = $params['sign_time_end'];
		!empty($params['wed_date_start']) && $cond['wed_date_start'] = $params['wed_date_start'];
		!empty($params['wed_date_end']) && $cond['wed_date_end'] = $params['wed_date_end'];
		!empty($params['upload_time_start']) && $cond['upload_date_start'] = $params['upload_time_start'];
		!empty($params['upload_date_end']) && $cond['upload_date_end'] = $params['upload_time_start'];



		// 分页处理
		$limit = array();
		if(!empty($params['pagesize']) && !empty($params['page']))
        {
            if(is_numeric($params['pagesize']) && is_numeric($params['page']))
            {

                $start = ($params['page'] - 1) * $params['pagesize'];
                $limit = array('nums' => $params['pagesize'], 'start' => $start);
            }
        }

		// 获取记录
		$total = $this->contract->getOne($cond, array('count(bid) AS num'));
		$list = $this->contract->getAll($cond, $limit, array('sign_contract.id' => 'desc')); // 记录列表

		//获取店铺名称
		$shoperid_arr = array_flatten($list, 'shopper_id');
		$data_where["uids"] = implode(",", array_unique($shoperid_arr));
		$shoper_info_list = $this->buscommon->shoperInfo($data_where);
		$shoper_info = !empty($shoper_info_list["rows"]) ? $shoper_info_list["rows"] : array();
		$shoper_info = toHashmap($shoper_info, "uid");

		$data = array();
		foreach($list as $key => &$contract)
		{
			$data[$key]["id"] = $contract["id"];
			$data[$key]["refuse_reason"] = $contract['contract_status'] == $contractstatus['reject'] ?  $contract["refuse_reason"] : ''; //驳回原因
			$data[$key]["contract_num"] = $contract["contract_num"];
			$data[$key]["status_serial"] = $contract["contract_status"];
            $data[$key]["contract_status"] = isset($rcon[$contract["contract_status"]]) ? $contractstatus_explan[$rcon[$contract["contract_status"]]] : '';
			$data[$key]["archive_status"] = isset($rar[$contract["archive_status"]]) ? $archivestatus_explan[$rar[$contract["archive_status"]]] : '';
			$data[$key]["funds_status"] = isset($rfun[$contract["funds_status"]]) ? $fundstatus_explan[$rfun[$contract["funds_status"]]] : '';
			$data[$key]["shopper_name"] = $contract['shopper_name'];
			$data[$key]["wed_date"] = $contract['wed_date'] ? date("Y-m-d", $contract['wed_date']) : '';
			$data[$key]["wed_place"] = $contract['wed_place'];  //婚礼场地
			$data[$key]["username"] = $contract['username'];
			$data[$key]["mobile"] = $contract['mobile'];
			$data[$key]["wed_location"] = $contract['wed_location']; //婚礼地点
			$data[$key]["sign_time_month"] = $contract['sign_time'] ? date("Y-m", $contract['sign_time']) : "";
			$data[$key]["sign_time"] = $contract['sign_time'] ? date("Y-m-d H:i:s", $contract['sign_time']) : "";
			$data[$key]["wed_amount"] = $contract['wed_amount']; //初始预算
			$data[$key]["offline"] = $contract["offline"]; //线上线下(0：线上，1线下)
			$data[$key]["offline_text"] = $contract["offline"]==0 ? "线上" : "线下" ; //线上线下(0：线上，1线下)
			$data[$key]["type"] = $contract["type"];
			$data[$key]["type_text"] = $contract["type"]==1 ? "三方" : "双方" ;
			$data[$key]["upload_time"] = $contract['upload_time'] ? date("Y-m-d H:i:s", $contract['upload_time']) : "";  //提交时间
			$data[$key]["refuse_time"] = $contract['refuse_time'] ? date("Y-m-d H:i:s", $contract['refuse_time']) : "";  //驳回时间

			$data[$key]["tradeno"] = $contract["tradeno"]; //交易编号
			$data[$key]["archive_time"] = $contract['archive_time'] ? date("Y-m-d H:i:s", $contract['archive_time']) : ""; //归档时间
			$data[$key]["payment_status"] = $contract['funds_status'] < $fundstatus['paid_advance'] ? "未付款" : "已付款"; //付款状态

			//合同图片
			$data[$key]["number_img"] = $contract["number_img"] ? get_oss_image($contract["number_img"]).'@100.jpg' : '';
			$data[$key]["sign_img"] = $contract["sign_img"] ? get_oss_image($contract["sign_img"]).'@100.jpg' : '';

			//处理时间
			switch($contract["contract_status"])
			{
				case $contractstatus['confirmed'] : $data[$key]["handle_time"] = $data[$key]["sign_time"];break;
				case $contractstatus['reject'] : $data[$key]["handle_time"] = $data[$key]["refuse_time"];break;
				default : $data[$key]["handle_time"] = '';
			}

			//新人补贴和商家补贴暂时为空
			$data[$key]["bride_subsidy"] = "";
			$data[$key]["shoper_subsidy"] = "";

			//最终合同金额
			$contract_sum = $this->contract_ext->getOne(array("cid" => $contract["id"]), array('sum(amount) AS amount_sum'));
			$data[$key]["contract_sum"] = $contract_sum['amount_sum'];
			//收款金额
			$received_sum = $this->payment->getOne(array("cid" => $contract["id"], "status" => $paymentstatus["confirmed"], "fund_type <"=>$paymenttype["payback"]), array('sum(amount) AS amount_sum'));
			$data[$key]["received_sum"] = $received_sum['amount_sum'];
			//付款金额
			$payment_num = $this->payment->getOne(array("cid" => $contract["id"], "fund_type" => $paymenttype["payback"]), array('sum(amount) AS amount_sum'));
			$data[$key]["payment_num"] = $payment_num['amount_sum'];

			//新人顾问
			$admin = $this->sum->getInfoById($contract['follower_uid']);
			$data[$key]['follower'] = $admin ? $admin['username'] : '';
            //运营
            $operate = $this->sum->getInfoById($contract['operate_uid']);
            $data[$key]['operate'] = $operate ? $operate['username'] : '';
			//处理人
			if($contract['contract_status'] == $contractstatus['to_upload'] || $contract['contract_status'] == $contractstatus['to_confirm'])
			{
				$data[$key]['handler'] = '';
			}else{
				$handler = $this->sum->getInfoById($contract['sys_uid']);
				$data[$key]['handler'] = $handler ? $handler['username'] : '';
			}
			//归档人
			$archiver = $this->sum->getInfoById($contract['archive_uid']);
			$data[$key]['archiver'] = $archiver ? $archiver['username'] : '';

			//店铺名称
			if(isset($shoper_info[$contract['shopper_id']]))
			{
				$data[$key]['studio_name'] = $shoper_info[$contract['shopper_id']]['studio_name'];
			}
		}

		if($export)
		{
			return array('total' => $total['num'], 'rows' => $data);
		}
		return success(array('total' => $total['num'], 'rows' => $data));
	}

	/**
	 * 导出
	 */
	public function exportcsv()
	{
		$this->load->helper('excel_tools');
		$exporter = new ExportDataExcel('browser', date('Y-m-d') . '_sign_contract.xls');
		$exporter->initialize();
		$exporter->addRow(array(
			'合同编号',
			'合同状态',
			'归档状态',
            '合同渠道',
			'返款状态',
			'商家昵称',
			'婚礼日期',
			'客户姓名',
			'客户手机',
            '婚礼地点',
            '婚礼场地',
			'签约月份',
			'签约生效时间',
			'初始预算',
			'最终合同金额',
			'收款金额',
			'付款金额',
			'新人补贴',
			'商家补贴',
			'新人顾问',
            '运营',
		));

		$result = $this->contractlist(true);

//		var_dump($result); exit();
//		if(!isset($result['info']) || !is_array($result['info']))
//		{
//			exit('导出失败');
//		}
		$params = $this->input->get();

		$pagesize = !empty($params['pagesize']) ? $params['pagesize'] : 50;
		$page = ceil($result['total'] / $pagesize);

        $region_list = $this->region->getAll();
		for($i = 1; $i <= $page; $i++)
		{
			$_GET['pagesize'] = $pagesize;
			$_GET['page'] = $i;
			$contract = $this->contractlist(true);
			foreach ($contract['rows'] as $b)
			{
                $tp_location_text = '';
                $tp_location = explode(',' , $b['wed_location']);
                $tp_location_text .= isset($tp_location[0]) ? $region_list[$tp_location[0]].' ' : '';
                $tp_location_text .= isset($tp_location[1]) ? $region_list[$tp_location[1]].' ' : '';
                $tp_location_text .= isset($tp_location[2]) ? $region_list[$tp_location[2]] : '';
				$exporter->addRow(array(
					$b['contract_num'],
					$b['contract_status'],
					$b['archive_status'],
                    $b['offline_text'],
					$b['funds_status'],
					$b['shopper_name'],
					$b['wed_date'],
					$b['username'],
					$b['mobile'],
					$tp_location_text,
                    $b['wed_place'],
                    $b['sign_time_month'],
					$b['sign_time'],
					$b['wed_amount'],
					$b['contract_sum'],
					$b['received_sum'],
					$b['payment_num'],
					$b['bride_subsidy'],
					$b['shoper_subsidy'],
					$b['follower'],
                    $b['operate'],
				));
			}
		}
		$exporter->finalize();
	}

	/**
	 * 中间合同列表
	 *
	 */
	public function contractExtList()
	{
		$params = $this->input->get();
		//合同id
		$cid = $this->input->get("cid") ? $this->input->get("cid") : 0;
		$contract = $this->contract->findByCondition(array("id" => $cid));
		if(empty($contract))
		{
			return failure('签约信息不存在');
		}

		// 分页处理
		$limit = array();
		if(!empty($params['pagesize']) && !empty($params['page']))
        {
            if(is_numeric($params['pagesize']) && is_numeric($params['page']))
            {

                $start = ($params['page'] - 1) * $params['pagesize'];
                $limit = array('nums' => $params['pagesize'], 'start' => $start);
            }
        }
		//款项状态
		list($paymentstatus, $paymentstatus_explan) = $this->payment->getPaymentStatus();
		$rpay = array_flip($paymentstatus);
		//合同类型
		list($contracttype, $contracttype_explan) = $this->contract_ext->getContractType();
		$rcont = array_flip($contracttype);
		//中间合同状态
        list($contract_ext_status, $_) = $this->contract_ext->getContractExtStatus();

        //中间合同列表
		$total = $this->contract_ext->getOne(array("cid" => $cid), array('count(id) AS num'));
		$contract_ext = $this->contract_ext->getList(array("cid" => $cid), $limit, array("id" => "desc"));
		$sids = array_flatten($contract_ext, "id");

		$payment = $sids ? $this->payment->getList(array("sid" => $sids)) : array();
		$payment = toHashmap($payment, "sid");

		foreach ($contract_ext as $key => &$cone)
		{
			if(isset($payment[$cone["id"]]))
			{
				$cone["status"] = $payment[$cone["id"]]["status"];
			}else
			{
				$cone["status"] = 0;
			}

            //如果是增补合同，并且尾款未提交或尾款状态为未审核，已确认状态，则此中间合同状态为已确认
            //如果尾款被驳回，则此尾款之前提交的增补合同状态一并改为已驳回
            if($cone['contract_type'] == $contracttype['addition'])
            {
                if($cone['c_status'] == $contract_ext_status['submmited'])
                {
                    $cone['status'] = $paymentstatus['confirmed'];
                }
                elseif($cone['c_status'] == $contract_ext_status['reject'])
                {
                    $cone['status'] = $paymentstatus['reject'];
                }
            }
			$cone["status_detail"] = isset($rpay[$cone["status"]]) ? $paymentstatus_explan[$rpay[$cone["status"]]] : "";
			$cone["contract_type"] = isset($rcont[$cone["contract_type"]]) ? $contracttype_explan[$rcont[$cone["contract_type"]]] : "";
			$cone["create_time"] = $cone["create_time"] ? date("Y-m-d H:i:s",$cone["create_time"]) : "";
			$cone["sign_time"] = $cone["sign_time"] ? date("Y-m-d H:i:s",$cone["sign_time"]) : "";
            if(empty($cone['sign_time']) && $payment[$cone["id"]]['pay_time'] > 0)
            {
                $cone["sign_time"] = date("Y-m-d H:i:s",$payment[$cone["id"]]['pay_time']);
            }
			$cone["number_img"] = $cone["number_img"] ? get_oss_image($cone["number_img"]).'@100.jpg' : "";
			$cone["sign_img"] = $cone["sign_img"] ? get_oss_image($cone["sign_img"]).'@100.jpg' : "";
		}

		return success(array('total' => $total['num'], 'rows' => $contract_ext));

	}

	/**
	 * 收款明细
	 */
	public function receiveList()
	{
		//合同id
		$cid = $this->input->get("cid") ? $this->input->get("cid") : 0;
		$contract = $this->contract->findByCondition(array("id" => $cid));
		if(empty($contract))
		{
			return failure('签约信息不存在');
		}

		list($paymentstatus, $paymentstatus_explan) = $this->payment->getPaymentStatus(); //款项状态
		list($paymenttype, $paymenttype_explan) = $this->payment->getPaymentType();//款项类型
		list($paymode, $paymode_explan) = $this->payment->getPayMode();//支付方式

		$rpayt = array_flip($paymenttype);
		$rpaym = array_flip($paymode);

		$where_receive = array(
			"cid" => $cid,
			"bid" => $contract["bid"],
			"status" => $paymentstatus["confirmed"],
			"fund_type < " => $paymenttype["payback"],
		);

		$total = $this->payment->getOne($where_receive, array('count(id) AS num'));
		$receive = $this->payment->getList($where_receive);

		$sys_uids = array_flatten($receive, "sys_uid");
		$sys_user = $sys_uids ? $this->sum->findUsers(array("id" => $sys_uids)) : array();
		foreach ($receive as $key => &$rec)
		{
			$rec["pay_time"] = $rec["pay_time"] ? date("Y-m-d H:i:s", $rec["pay_time"]) : "";
            if(empty($rec["fund_describe"])){
                $rec["fund_type"] = isset($rpayt[$rec["fund_type"]]) ? $paymenttype_explan[$rpayt[$rec["fund_type"]]] : "";
            }else{
                $rec["fund_type"] = isset($rec["fund_describe"]) ? $rec["fund_describe"] : "";
            }
			$rec["pay_mode"] = isset($rpaym[$rec["pay_mode"]]) ? $paymode_explan[$rpaym[$rec["pay_mode"]]] : "";
			$rec["sys_user"] = isset($sys_user[$rec["sys_uid"]]) ? $sys_user[$rec["sys_uid"]]["username"] : "";
		}
		return success(array('total' => $total['num'], 'rows' => $receive));
	}

	/**
	 * 付款明细
	 */
	public function backList()
	{
		//合同id
		$cid = $this->input->get("cid") ? $this->input->get("cid") : 0;
		$contract = $this->contract->findByCondition(array("id" => $cid));
		if(empty($contract))
		{
			return failure('签约信息不存在');
		}

		list($paymentstatus, $paymentstatus_explan) = $this->payment->getPaymentStatus(); //款项状态
		list($paymenttype, $paymenttype_explan) = $this->payment->getPaymentType();//款项类型
		list($paymode, $paymode_explan) = $this->payment->getPayMode();//支付方式

		$rpaym = array_flip($paymode);

		$where_receive = array(
			"cid" => $cid,
			"fund_type " => $paymenttype["payback"],
		);

		$total = $this->payment->getOne($where_receive, array('count(id) AS num'));
		$back = $this->payment->getList($where_receive);

        //查看此合同是否有过双方的提现，如果有则显示到付款明细中
        $payment_ids = $payment_map_ids = $cash_info = array();
        $refund_payment_map_query = $this->refund_pay_map->findAll(array('cid' => $cid));
        foreach($refund_payment_map_query as $val)
        {
            $payment_ids[] = $val['payment_id'];
            $payment_ids[] = $val['sub_payment_id'];
            $payment_map_ids[$val['sub_payment_id']] = $val['payment_id'];
        }

        $backTwo = $cash_sys_uids = array();
        if(count($payment_ids) > 0)
        {
            //查询出双方提现中所涉及到的交易的信息
            $payment_infos = $this->payment->getList(array('id' => $payment_ids, 'status' => $paymentstatus['confirmed']));
            foreach($payment_infos as $pay)
            {
                if($pay['fund_type'] == $paymenttype['both_payback'])
                {
                    $cash_info[$pay['id']] = $pay;
                    if($pay['sys_uid'] > 0)
                    {
                        $cash_sys_uids[] = $pay['sys_uid'];
                    }
                }
                else
                {
                    $backTwo[] = $pay;
                }
            }
        }

        foreach($backTwo as $key => $val1)
        {
            if(!isset($cash_info[$payment_map_ids[$val1['id']]])){
                unset($backTwo[$key]);
                continue;
            }
        }

        $back = array_merge($back , $backTwo);
		$sys_uids = array_flatten($back, "sys_uid");
        $sys_uids = array_merge($sys_uids , $cash_sys_uids);
		$sys_user = $sys_uids ? $this->sum->findUsers(array("id" => $sys_uids)) : array();

		foreach ($back as $key => &$val)
		{
			$val["pay_time"] = $val["pay_time"] ? date("Y-m-d H:i:s", $val["pay_time"]) : "";
			$val["pay_mode"] = isset($rpaym[$val["pay_mode"]]) ? $paymode_explan[$rpaym[$val["pay_mode"]]] : "";
			$val["sys_user"] = isset($sys_user[$val["sys_uid"]]) ? $sys_user[$val["sys_uid"]]["username"] : "";
            if(isset($payment_map_ids[$val['id']]))
            {
                if(isset($cash_info[$payment_map_ids[$val['id']]]))
                {
                    $val['serial_number'] = $cash_info[$payment_map_ids[$val['id']]]['serial_number'];
                    $val['pay_time'] = date("Y-m-d H:i:s" , $cash_info[$payment_map_ids[$val['id']]]['pay_time']);
                    $val['note'] = $cash_info[$payment_map_ids[$val['id']]]['note'];

                    $tmp_mode = $cash_info[$payment_map_ids[$val['id']]]['pay_mode'];
                    $val['pay_mode'] = isset($rpaym[$tmp_mode]) ? $paymode_explan[$rpaym[$tmp_mode]] : "";

                    $tmp_sys_uid = $cash_info[$payment_map_ids[$val['id']]]['sys_uid'];
                    $val['sys_user'] = isset($sys_user[$tmp_sys_uid]) ? $sys_user[$tmp_sys_uid]["username"] : "";
                }
            }
		}

		return success(array('total' => $total['num'], 'rows' => $back));
	}


    /*
     * 收款录入
     *
     * */
    public function collecPayment(){
        $params = $this->input->post();
        //验证数据*/
        if(empty($params["pay_time"]))
        {
            return failure('请填写付款时间');
        }
        if(empty($params["pay_mode"]))
        {
            return failure('请选择支付方式');
        }
        if(empty($params["fund_describe"]))
        {
            return failure('请填写款项类型');
        }
        if(empty($params["amount"]))
        {
            return failure('请填写支付金额');
        }
        //合同id
        $cid = isset($params["cid"]) ? $params["cid"] : 0;
        $cid = intval($cid);
        $contract = $this->contract->getfindById($cid);
        if(empty($contract))
        {
            return failure('签约信息不存在');
        }

        //商机id
        $bid = isset($params["bid"]) ? $params["bid"] : 0;
        $bid = intval($bid);

        list($paymentstatus, $paymentstatus_explan) = $this->payment->getPaymentStatus(); //款项状态
        list($paymenttype, $paymenttype_explan) = $this->payment->getPaymentType();//款项类型
        list($fundstatus, $fundstatus_explan) = $this->contract->getFundStatus(); //返款状态
        $init = array(
            "cid" => $cid,
            "bid" => $bid,
            "sid" => 0,
            "ew_pay_id" => 0,
            "serial_number" => date('YmdHis').rand(100000,999999),
            "status" => $paymentstatus["confirmed"],
            "amount" => $params["amount"],
            "pay_mode" => $params["pay_mode"],
            "fund_type" => 8,
            "fund_describe" => isset($params["fund_describe"]) ? $params["fund_describe"] : 0,
            "voucher_img" => "",
            "create_time" => time(),
            "update_time" => time(),
            "pay_time" => strtotime($params["pay_time"]),
            "sys_uid" => $this->session->userdata('admin_id'),
            "refuse_reason" => "",
            "note" => isset($params["note"]) ? $params["note"] : "",
        );
        //将数据推送到主站
        $payment = array(
            'contract_num'=>$contract['contract_num'],//: 合同编号
            'type_string'=>$params['fund_describe'],//: 资金类型描述
            'amount'=>$params['amount'],//: 金额
            'mode'=>$params["pay_mode"],//：支付方式
            'payment_time'=>strtotime($params["pay_time"]),//: 支付时间（unixtime）
            'remark'=>$params["note"],//：付款备注
            'inorout'=>1// 1(收款) 2（回款）

        );

        $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/add-payment', $payment);
        $ret = json_decode($ret,true);
        if($ret['result'] == 'succ'){
            if($this->payment->add($init))
            {
                //修改合同表款项状态为已付款
                $upt_data = array(
                    "funds_status" => $fundstatus['paid_advance'],
                );
                $res = $this->contract->modify(array("id" => $cid), $upt_data);
                return success('收款录入成功');
            }else
            {
                return failure('收款录入失败');
            }
        }else{
            return failure($ret['msg']);
        }
    }




	/**
	 * 付款录入
	 */
	public function entryPayment()
	{
		// 获取请求参数
		$params = $this->input->post();
		$params = ew_filter_quote_html($params);

		//合同id
		$cid = isset($params["cid"]) ? $params["cid"] : 0;
		$cid = intval($cid);
		$contract = $this->contract->getfindById($cid);
		if(empty($contract))
		{
			return failure('签约信息不存在');
		}

		//商机id
		$bid = isset($params["bid"]) ? $params["bid"] : 0;
		$bid = intval($bid);
		$business = $this->business->findRow(array("id" => $bid));
		if(!$business)
		{
			// return failure("商机不存在");
		}

		//验证数据
		if(empty($params["pay_time"]))
		{
			return failure('请填写付款时间');
		}
		if(empty($params["pay_mode"]))
		{
			return failure('请选择支付方式');
		}
		if(empty($params["amount"]))
		{
			return failure('请填写支付金额');
		}
		if(!is_numeric($params["amount"]))
		{
			return failure('支付金额请填写数字');
		}

		list($paymentstatus, $paymentstatus_explan) = $this->payment->getPaymentStatus(); //款项状态
		list($paymenttype, $paymenttype_explan) = $this->payment->getPaymentType();//款项类型
		list($paymode, $paymode_explan) = $this->payment->getPayMode();//支付方式
		list($fundstatus, $fundstatus_explan) = $this->contract->getFundStatus(); //返款状态

		//付款金额不能大于收款金额
		$gained_sum = $this->erp_conn->select("sum(amount) as amount_sum")->from("ew_sign_contract_payment_details")->where(array('cid'=>$cid,'status'=>$paymentstatus["confirmed"]))->where('fund_type !=',$paymenttype['payback'])->get()->row_array();
		$gained_sum = $gained_sum["amount_sum"] ? $gained_sum["amount_sum"] : 0;
		$pay_sum = $this->erp_conn->select("sum(amount) as amount_sum")->from("ew_sign_contract_payment_details")->where("cid", $cid)->where("fund_type", $paymenttype['payback'])->get()->row_array();
		$pay_sum = $pay_sum["amount_sum"] ? $pay_sum["amount_sum"] : 0;
		if(floatcmp($params["amount"], ($gained_sum - $pay_sum), 2) > 0)
		{
			return failure('付款金额不能大于实收金额');
		}



        $init = array(
			"cid" => $cid,
			"bid" => $bid,
			"sid" => 0,
			"ew_pay_id" => 0,
			"serial_number" => date('YmdHis').rand(100000,999999),
			"status" => $paymentstatus["confirmed"],
			"amount" => $params["amount"],
			"pay_mode" => $params["pay_mode"],
			"fund_type" => $paymenttype["payback"],
			"voucher_img" => "",
			"create_time" => time(),
			"update_time" => time(),
			"pay_time" => strtotime($params["pay_time"]),
			"sys_uid" => $this->session->userdata('admin_id'),
			"refuse_reason" => "",
			"note" => $params["note"],
		);

        //将数据推送到主站
        $payment = array(
            'contract_num'=>$contract['contract_num'],//: 合同编号
            'type_string'=>isset($params['fund_describe']) ? $params['fund_describe'] : '',//: 资金类型描述
            'amount'=>$params['amount'],//: 金额
            'mode'=>$params["pay_mode"],//：支付方式
            'payment_time'=>strtotime($params["pay_time"]),//: 支付时间（unixtime）
            'remark'=>$params["note"],//：付款备注
            'inorout'=>2// 1(收款) 2（回款）

        );

        $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/add-payment', $payment);
        $ret = json_decode($ret,true);
        if($ret['result'] == 'succ'){
            if($this->payment->add($init))
            {
                //修改合同表款项状态为已付款
                $upt_data = array(
                    "funds_status" => $fundstatus['already_first_back'],
                );
                $res = $this->contract->modify(array("id" => $cid), $upt_data);
                return success('付款录入成功');
            }else
            {
                return failure('付款录入失败');
            }
        }else{
            return failure($ret['msg']);
        }

	}


	/**
	 * 合同归档
	 */
	public function archive()
	{
		//合同id
		$cid = $this->input->get("cid") ? $this->input->get("cid") : 0;
		$cid = intval($cid);
		$contract = $this->contract->findByCondition(array("id" => $cid));
		if(empty($contract))
		{
			return failure('签约信息不存在');
		}
		list($archivestatus, $archivestatus_explan) = $this->contract->getArchiveStatus();  //归档状态

		//同步主站合同归档
		$contract_num = $contract["contract_num"];
		$resp = $this->contract->syncArchive($contract_num);
//		$resp = TRUE;
		if(!$resp)
		{
			return failure("主站数据同步失败");
		}

		$upt_data = array(
			"archive_status" => $archivestatus["archived"],
			"archive_time" => time(),
		  	"archive_uid" => $this->session->userdata('admin_id'),
		);
		$res = $this->contract->modify(array("id" => $cid), $upt_data);
		if($res)
		{
			return success('修改成功');
		}else
		{
			return failure('修改失败');
		}
	}

	/**
	 * 中止合同
	 */
	public function stopContract()
	{
		// 获取请求参数
		$params = $this->input->post();
		$params = ew_filter_quote_html($params);
		//合同id
		$cid = $params["cid"] ? $params["cid"] : 0;
		$cid = intval($cid);
		$contract = $this->contract->findByCondition(array("id" => $cid));
		if(empty($contract))
		{
			return failure('签约信息不存在');
		}

		if(empty($params["reason"]))
		{
			return failure('请填写中止合同原因');
		}

		list($contractstatus, $contractstatus_explan) = $this->contract->getContractStatus();  //合同状态

		//同步主站中止合同
		$contract_num = $contract["contract_num"];
		$stop_reason = $params["reason"];
		$resp = $this->contract->syncStopContract($contract_num, $stop_reason);
		//$resp = TRUE;
		if(!$resp)
		{
			return failure("主站数据同步失败");
		}

		$upt_data = array(
			"contract_status" => $contractstatus["stop"],
			"stop_reason" => $params["reason"],
			"stop_time" => time(),
		);
		$res = $this->contract->modify(array("id" => $cid), $upt_data);
		if($res)
		{
			return success('修改成功');
		}else
		{
			return failure('修改失败');
		}
	}

	/**
	 * 全部返款完成
	 */
	public function completedContract()
	{
        $input = $this->input->get();
		//合同id
		$cid = $input['cid'] ? (int)$input['cid'] : 0 ;
		$contract = $this->contract->getfindById($cid);

        if(empty($contract))
		{
			return failure('签约信息不存在');
		}

		list($contractstatus, $contractstatus_explan) = $this->contract->getContractStatus();  //合同状态
		list($fundstatus, $fundstatus_explan) = $this->contract->getFundStatus(); //返款状态

		//同步主站返款完成
        $contract_num = $contract["contract_num"];
		$resp = $this->contract->syncComplete($contract_num);
//		$resp = TRUE;
		if(!$resp)
		{
			return failure("主站数据同步失败");
		}

		$upt_data = array(
			"funds_status" => $fundstatus["all_back"],
			"contract_status" => $contractstatus["completed"],
			"finish_time" => time(),
		);
		$res = $this->contract->modify(array("id" => $cid), $upt_data);
		if($res)
		{
			return success('修改成功');
		}else
		{
			return failure('修改失败');
		}
	}

//	================12月改版新加内容（合同与钱单独审核）==================
	/**
	 * 确认合同
	 * @return mixed
	 */
	public function confirmContract()
	{
		//合同id
		$cid = $this->input->get("cid") ? $this->input->get("cid") : 0;
		$cid = intval($cid);
		$contract = $this->contract->findByCondition(array("id" => $cid));
		if(empty($contract))
		{
			return failure('签约信息不存在');
		}

        $business_info = $this->business->findByCondition(array('id' => $contract['bid']));
        if($business_info['id'] <= 0)
        {
            return failure('此合同没有与商机关联');
        }

        if($contract['shopper_id'] <= 0)
        {
            return failure('此合同没有商家');
        }

		list($contractstatus, $contractstatus_explan) = $this->contract->getContractStatus();  //合同状态

		//同步主站确认合同
		$contract_num = $contract["contract_num"];
		$resp = $this->contract->syncConfirmContract($contract_num);
//		$resp = TRUE;
		if(!$resp)
		{
			return failure("主站数据同步失败");
		}

        //修改商机状态，修正此商机其他合同信息
        list($status , $status_explan) = $this->contract->getContractStatus();//合同状态
        list($fstatus , $funds_explan) = $this->contract->getFundStatus();//合同款项类型
        list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();//款项状态
        list($mode , $mode_explan) = $this->payment->getPayMode();//支付方式
        list($ptypes , $ptypes_explan) = $this->payment->getPaymentType();//款项类型
        list($trade , $trade_explan) = $this->business->getTradeStatus();//商机交易状态

        //给已丢单的策划师发短信
        $shop_ids = $this->erp_conn->select("shop_id")->where('bid', $contract['bid'])->where('shop_id !=', $contract['shopper_id'])->where('status',Business_shop_map_model::STATUS_NOT)->get('ew_business_shop_map')->result_array();
        //修改相同商机其他商家的签约信息为 无效 60
        $this->erp_conn->where('bid', $contract['bid'])->where('id !=', $contract['id'])->update("ew_sign_contract", array('contract_status' =>$status['invalid'], 'sign_time' => time()));
        //修改签约商家的分单状态为已签约 1 且已见面 2
        $this->erp_conn->where('bid', $contract['bid'])->where('shop_id =', $contract['shopper_id'])->update("ew_business_shop_map", array('status' => Business_shop_map_model::STATUS_SIGN,'face_status'=>Business_shop_map_model::FACE_STATUS_MEET));
        //修改相同商机其他商家的状态为已丢单  2
        $this->erp_conn->where('bid', $contract['bid'])->where('shop_id !=', $contract['shopper_id'])->where('status',Business_shop_map_model::STATUS_NOT)->update("ew_business_shop_map", array('status' => Business_shop_map_model::STATUS_LOST));
        //修改商机交易状态为已成单 3
        $this->erp_conn->where('id', $contract['bid'])->update("ew_business", array('trade_status' => $trade['ordered'],'updatetime' => time()));

		$upt_data = array(
		  "contract_status" => $contractstatus["confirmed"],
		  "sign_time" => time(),
		  "sys_uid" => $this->session->userdata('admin_id'),
		);

		$res = $this->contract->modify(array("id" => $cid), $upt_data);
		if($res)
		{
            if(!empty($shop_ids))
            {
                $ids = array();
                foreach($shop_ids as $v)
                {
                    $ids[] = $v['shop_id'];
                }
                $uids = implode(',' , $ids);
                $shoper_list = $this->buscommon->shoperInfo(array('uids' => $uids));
                $shoper_mobiles = array();
                foreach($shoper_list['rows'] as $sval)
                {
                    $shoper_mobiles[] = $sval['phone'];

                }

                $shoper_content = '您好，给您分配的新人' . $contract['username'] . '，已经确定不再需要您提供服务。温馨提示：您可以在易结商家版客户端订单管理中查看，订单号为'.$business_info['tradeno'].'。';
                $this->sms->send($shoper_mobiles , $shoper_content);
            }

			return success('修改成功');
		}else
		{
			return failure('修改失败');
		}
	}

	/**
	 * 驳回合同
	 * @return mixed
	 */
	public function rejectContract()
	{
		// 获取请求参数
		$params = $this->input->post();
		$params = ew_filter_quote_html($params);
		//合同id
		$cid = $params["cid"] ? $params["cid"] : 0;
		$cid = intval($cid);
		$contract = $this->contract->findByCondition(array("id" => $cid));
		if(empty($contract))
		{
			return failure('签约信息不存在');
		}

		if(empty($params["reason"]))
		{
			return failure('请填写驳回原因');
		}

		list($contractstatus, $contractstatus_explan) = $this->contract->getContractStatus();  //合同状态

		//同步主站驳回合同
		$contract_num = $contract["contract_num"];
		$stop_reason = $params["reason"];
		$resp = $this->contract->syncRejectContract($contract_num, $stop_reason);
		//$resp = TRUE;
		if(!$resp)
		{
			return failure("主站数据同步失败");
		}

		$upt_data = array(
		  "contract_status" => $contractstatus["reject"],
		  "refuse_reason" => $params["reason"],
		  "refuse_time" => time(),
		  "sys_uid" => $this->session->userdata('admin_id'),
		);
		$res = $this->contract->modify(array("id" => $cid), $upt_data);
		if($res)
		{
			return success('修改成功');
		}else
		{
			return failure('修改失败');
		}
	}

	/**
	 * 合同审核列表导出
	 */
	public function exportAudit()
	{
		$this->load->helper('excel_tools');
		$exporter = new ExportDataExcel('browser', date('Y-m-d') . '_audit_contract.xls');
		$exporter->initialize();
		$exporter->addRow(array(
		  '合同编号',
		  '合同状态',
		  '签约方式',
		  '商家昵称',
		  '店铺名称',
		  '提交时间',
		  '处理时间',
		  '审核人',
		  '驳回原因',
		  '交易编号',
		  '运营',
		));

		$result = $this->contractlist(true);

//		var_dump($result); exit();
//		if(!isset($result['info']) || !is_array($result['info']))
//		{
//			exit('导出失败');
//		}
		$params = $this->input->get();

		$pagesize = !empty($params['pagesize']) ? $params['pagesize'] : 50;
		$page = ceil($result['total'] / $pagesize);

		for($i = 1; $i <= $page; $i++)
		{
			$_GET['pagesize'] = $pagesize;
			$_GET['page'] = $i;
			$contract = $this->contractlist(true);
			foreach ($contract['rows'] as $b)
			{
				$exporter->addRow(array(
				  $b['contract_num'],
				  $b['contract_status'],
				  $b['type_text'],
				  $b['shopper_name'],
				  $b['studio_name'],
				  $b['upload_time'],
				  $b['handle_time'],
				  $b['handler'],
				  $b['refuse_reason'],
				  $b['tradeno'],
				  $b['operate'],
				));
			}
		}
		$exporter->finalize();
	}


	/**
	 * 合同归档列表导出
	 */
	public function exportArchive()
	{
		$this->load->helper('excel_tools');
		$exporter = new ExportDataExcel('browser', date('Y-m-d') . '_archive_contract.xls');
		$exporter->initialize();
		$exporter->addRow(array(
		  '合同编号',
		  '合同状态',
		  '归档状态',
		  '商家昵称',
		  '店铺名称',
		  '提交时间',
		  '归档时间',
		  '归档人',
		  '交易编号',
		));

		$result = $this->contractlist(true);

//		var_dump($result); exit();
//		if(!isset($result['info']) || !is_array($result['info']))
//		{
//			exit('导出失败');
//		}
		$params = $this->input->get();

		$pagesize = !empty($params['pagesize']) ? $params['pagesize'] : 50;
		$page = ceil($result['total'] / $pagesize);

		for($i = 1; $i <= $page; $i++)
		{
			$_GET['pagesize'] = $pagesize;
			$_GET['page'] = $i;
			$contract = $this->contractlist(true);
			foreach ($contract['rows'] as $b)
			{
				$exporter->addRow(array(
				  $b['contract_num'],
				  $b['contract_status'],
				  $b['archive_status'],
				  $b['shopper_name'],
				  $b['studio_name'],
				  $b['upload_time'],
				  $b['archive_time'],
				  $b['handler'],
				  $b['tradeno'],
				));
			}
		}
		$exporter->finalize();
	}
}
