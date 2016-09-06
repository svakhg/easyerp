<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detail extends App_Controller
{
	public function __construct(){
        parent::__construct();
		$this->load->model('business/business_model', 'business');
		$this->load->model('business/business_extra_model' , 'business_extra');
		$this->load->model("business/common_model",'buscommon');
		$this->load->model('contract/contract_model', 'contract');
		$this->load->model('contract/contract_ext_model', 'contract_ext');
		$this->load->model('contract/contract_payment_details_model', 'payment');
		$this->load->model('sys_user_model', 'sum');
		$this->load->model('system/department_model', 'sdm');
		$this->load->model('commons/region_model' , 'region');
		$this->load->helper('functions');// 载入公共函数
        $this->load->helper('array');
    }
	
	/**
	 * 合同详情页显示
	 */
	public function index()
	{
		//合同id
		$cid = $this->input->get("cid") ? $this->input->get("cid") : 0;
		$contract = $this->contract->findByCondition(array("id" => $cid));
		if(empty($contract))
		{
			echo "<script>alert('签约信息不存在');</script>";die;
		}
		
		
		//状态处理
		list($contractstatus, $contractstatus_explan) = $this->contract->getContractStatus();  //合同状态
		list($archivestatus, $archivestatus_explan) = $this->contract->getArchiveStatus();  //归档状态
		list($fundstatus, $fundstatus_explan) = $this->contract->getFundStatus(); //返款状态
		list($contracttype, $contracttype_explan) = $this->contract_ext->getContractType(); //合同类型
		list($paymentstatus, $paymentstatus_explan) = $this->payment->getPaymentStatus(); //款项状态
		list($paymenttype, $paymenttype_explan) = $this->payment->getPaymentType();//款项类型
		list($paymode, $paymode_explan) = $this->payment->getPayMode();//支付方式
		$this->_data['contractstatus'] = $contractstatus;
		$this->_data['contractstatus_explan'] = $contractstatus_explan;
		$this->_data['archivestatus_explan'] = $archivestatus_explan;
		$this->_data['archivestatus'] = $archivestatus;
		$this->_data['archivestatus_explan'] = $archivestatus_explan;
		$this->_data['paymode'] = $paymode;
		$this->_data['paymode_explan'] = $paymode_explan;
		
		$rar = array_flip($archivestatus);
		$rcon = array_flip($contractstatus);
		$rfun = array_flip($fundstatus);
		$rcont = array_flip($contracttype);
		$pays = array_flip($paymentstatus);
		$payt = array_flip($paymenttype);
		$paym = array_flip($paymode);
		
		//商机表信息
		$business = $this->business->findByCondition(array("id" => $contract["bid"]));
		$contract["tradeno"] = $business ? $business["tradeno"] : '';
		$contract["business_num"] = $business ? $this->business->formatBid($business['id'] , $business['createtime']) : '';
        //顾问
		$admin = $business ? $this->sum->getInfoById($business['follower_uid']) : array();
		$contract['follower'] = $admin ? $admin['username'] : '';
		//运营
        $operate = $business ? $this->sum->getInfoById($business['operate_uid']) : array();
        $contract['operate'] = $operate ? $operate['username'] : '';
		
		//基本信息
		$base_info["archive_status"] = $contract['archive_status'];
		$base_info["contract_status"] = $contract["contract_status"];
		$base_info["contract_num"] = $contract['contract_num'];
		$base_info["contract_status_detail"] = isset($rcon[$contract["contract_status"]]) ? $contractstatus_explan[$rcon[$contract["contract_status"]]] : '--';
		$base_info["archive_status_detail"] = isset($rar[$contract["archive_status"]]) ? $archivestatus_explan[$rar[$contract["archive_status"]]] : '--';
		$base_info["fundstatus_serial"] = $contract["funds_status"];
		$base_info["funds_status"] = isset($rfun[$contract["funds_status"]]) ? $fundstatus_explan[$rfun[$contract["funds_status"]]] : '--';
		$base_info["ctype"] = "线上婚礼策划";  //现有数据都是线上婚礼策划
		$base_info["tradeno"] = $contract['tradeno'];
		$base_info["business_num"] = $contract["business_num"];
		$base_info["follower"] = $contract['follower'];
        $base_info["operate"] = $contract['operate'];
        $base_info["type"] = $contract["type"];
        $base_info["offline_text"] = $contract["offline"] == 0 ? "线上" : "线下" ;//0：线上，1：线下
		
		//三方合同信息
		$third_contract["shopper_name"] = $contract["shopper_name"];
		$third_contract["shoper_type"] = "策划师"; //商家类型暂时写固定值
		$third_contract["create_time"] = $contract['create_time'] ? date("Y-m-d H:i:s", $contract['create_time']) : '--';
        $third_contract["upload_time"] = $contract['upload_time'] ? date("Y-m-d H:i:s", $contract['upload_time']) : '--';
		$third_contract["sign_time"] = $contract['sign_time'] ? date("Y-m-d H:i:s", $contract['sign_time']) : '--';
		$third_contract["archive_time"] = $contract['archive_time'] ? date("Y-m-d H:i:s", $contract['archive_time']) : '--';
		$third_contract["stop_time"] = $contract['stop_time'] ? date("Y-m-d H:i:s", $contract['stop_time']) : '--';
		$third_contract["finish_time"] = $contract['finish_time'] ? date("Y-m-d H:i:s", $contract['finish_time']) : '--';
        $third_contract["refuse_time"] = $contract['refuse_time'] ? date("Y-m-d H:i:s", $contract['refuse_time']) : '--';
		$third_contract["wed_date"] = $contract["wed_date"] ? date("Y-m-d", $contract["wed_date"]) : '';
		//婚礼地点
		$region_list = $this->region->getAll();
		$tp_location_text = '';
		$tp_location = explode(',' , $contract['wed_location']);
		$tp_location_text .= isset($tp_location[1]) ? $region_list[$tp_location[1]].'-' : '';
		$tp_location_text .= isset($tp_location[2]) ? $region_list[$tp_location[2]] : '';
		$tp_location_text = $contract["wed_place"] ? $tp_location_text.' : '.$contract["wed_place"] : '';
		$third_contract["wed_place"] = $tp_location_text;
		
		$third_contract["wed_amount"] = $contract['wed_amount'];
		$third_contract["number_img"] = $contract["number_img"] ? get_oss_image($contract["number_img"]).'@100.jpg' : '';
		$third_contract["sign_img"] = $contract["sign_img"] ? get_oss_image($contract["sign_img"]).'@100.jpg' : '';
        $third_contract["payment_status"] = $contract['funds_status'] < $fundstatus['paid_advance'] ? "未付款" : "已付款"; //付款状态

        //中间合同状态
        list($contract_ext_status, $_) = $this->contract_ext->getContractExtStatus();
		//款项信息
//		$contract_sum = $this->contract_ext->findByCondition(array("cid" => $contract["id"] , 'c_status' => $contract_ext_status['submmited']), 'sum(amount) AS amount_sum');
//		$contract_sum = $this->contract_ext->findContractJoinExtra(array("ew_sign_contract_ext.cid" => $contract["id"], "sign_contract_payment_details.status" => $paymentstatus["confirmed"]), array(), FALSE, 'sum(ew_sign_contract_ext.amount) AS amount_sum');
		$contract_sum = $this->erp_conn->select("sum(amount) as amount_sum")->from("ew_sign_contract_ext")->where(array('cid'=>$contract["id"],'c_status'=>$contract_ext_status['submmited']))->get()->result_array();
		$gained_sum = $this->erp_conn->select("sum(amount) as amount_sum")->from("ew_sign_contract_payment_details")->where(array('cid'=>$contract["id"],'status'=>$paymentstatus["confirmed"]))->where('fund_type !=',$paymenttype['payback'])->get()->result_array();
		$fund_info["contract_sum"] = $contract_sum[0]['amount_sum'] ? $contract_sum[0]['amount_sum'] : 0;
		$fund_info["discount_amount"] = $contract['discount_amount'] ? $contract['discount_amount'] : 0;
		$fund_info["should_amount"] = $fund_info["contract_sum"] - $fund_info["discount_amount"];
		$fund_info["gained_sum"] = $gained_sum[0]['amount_sum'] ? $gained_sum[0]['amount_sum'] : 0;
		
		//商机基本信息
		list($source, $source_explan) = $this->business->getBusinessSource();		
		list($customer, $customer_explan) = $this->business->getCustomerIdentify();
		list($ordertype, $ordertype_explan) = $this->business->getBusinessType();
		list($budget, $budget_explan) = $this->business->getWedBudget();
		list($findtype, $findtype_explan) = $this->business->getFindShopType();
		list($wedtype, $wedtype_explan) = $this->business->getWedType();
		list($status, $status_explan) = $this->business->getBusinessStatus();
		list($tradestatus, $tradestatus_explan) = $this->business->getTradeStatus();
		$this->_data['customer'] = $customer;
		$this->_data['customer_explan'] = $customer_explan;
		$this->_data['source'] = $source;
		$this->_data['source_explan'] = $source_explan;
		$this->_data['ordertype'] = $ordertype;
		$this->_data['ordertype_explan'] = $ordertype_explan;
		$this->_data['budget'] = $budget;
		$this->_data['budget_explan'] = $budget_explan;
		$this->_data['findtype'] = $findtype;
		$this->_data['findtype_explan'] = $findtype_explan;
		$this->_data['wedtype'] = $wedtype;
		$this->_data['wedtype_explan'] = $wedtype_explan;
		$this->_data['status'] = $status;
		$this->_data['status_explan'] = $status_explan;
		$this->_data['tradestatus'] = $tradestatus;
		$this->_data['tradestatus_explan'] = $tradestatus_explan;
		$this->_data['fundstatus'] = $fundstatus;
		$this->_data['fundstatus_explan'] = $fundstatus_explan;
		$this->_data['usertype'] = $this->business->getUserType();
		
		$business["usertype"] = isset($business["usertype"]) ? $business["usertype"] : '';
		$business["username"] = isset($business["username"]) ? $business["username"] : '';
		$business["userpart"] = isset($business["userpart"]) ? $business["userpart"] : 0;
		$business["mobile"] = isset($business["mobile"]) ? $business["mobile"] : '';
		$business["tel"] = isset($business["tel"]) ? $business["tel"] : '';
		$business["weixin"] = isset($business["weixin"]) ? $business["weixin"] : '';
		$business["qq"] = isset($business["qq"]) ? $business["qq"] : '';
		$business["other_contact"] = isset($business["other_contact"]) ? $business["other_contact"] : '';
		$business["wed_date_detail"] = isset($business["wed_date"]) ? date("Y-m-d", $business["wed_date"]) : ""; //婚礼日期
		
		//客户需求
		$bus_extra_info = $this->business_extra->findRow(array("bid" => $contract["bid"]), "bid, weddate_note, location, wed_place, wed_place_area, wed_type, guest_from, guest_to, desk_from, desk_to, price_from, price_to, budget, budget_note, findtype, findnote, wish_contact, moredesc");
		$bus_extra_info = !empty($bus_extra_info) ? $bus_extra_info : $this->business_extra->prepareData();
		
		
		
		$this->_data["bid"] = $contract["bid"];
		$this->_data["cid"] = $contract["id"];
		$this->_data["base_info"] = $base_info;
		$this->_data["third_contract"] = $third_contract;
		$this->_data["fund_info"] = $fund_info;
		$this->_data["info"] = $business;
		$this->_data["info_extra"] = $bus_extra_info;
		// print_R($this->_data);exit;
		$this->load->view('contract/detail', $this->_data);
	}
}