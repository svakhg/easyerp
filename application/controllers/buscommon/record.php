<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 沟通记录及内部备注
 */

class Record extends App_Controller {


	public function __construct(){
        parent::__construct();
		$this->load->model('business/business_model', 'business');
		$this->load->model('business/business_record_model', 'record');
		$this->load->model('business/business_shop_map_model', 'shop_map');
		$this->load->helper('array');
		$this->load->helper('ew_filter');
    }
	
	/**
	 * 添加记录或备注
	 * @return type
	 */
	public function addRecord()
	{
		$uid = $this->session->userdata("admin_id");		
		$inputs = $this->input->post();
		$inputs['content'] = ew_filter_quote_html($inputs['content']);
		$bid = $inputs["bid"];
		$bid = intval($bid);
		
		$business = $this->business->findRow(array("id" => $bid));
		if(!$business)
		{
			return failure("商机不存在");
		}
		
		//获取沟通记录中的可见商家
		if($inputs["type"] == 0)
		{
			list($bus_status,$bus_status_explan) = $this->business->getBusinessStatus();
			list($trade_status,$trade_status_explan) = $this->business->getTradeStatus();
			if($business['trade_status'] == $trade_status['ordered']){//已成单(2-1)
				$shop_map = $this->erp_conn
					->where(array("bid" => $bid,"status" => Business_shop_map_model::STATUS_SIGN))
					->get("business_shop_map")->result_array();
				$shopper_ids_visible = isset($shop_map[0]) ? $shop_map[0]['shop_id'] : "" ;
			}else{
				if($business['status'] == $bus_status['parted']){//4-2
					if(!isset($inputs['visible_shopers'])){
						return failure("shopper_ids_visible error!");
					}
					$shopper_ids_visible = implode(",",$inputs['visible_shopers']);
				}else{
					$shopper_ids_visible = "";
				}
			}
		}else{
			$shopper_ids_visible = "";
		}

		//先入库，获取到插入的id，再调用主站借口，调失败则删除该条信息
		$init = array(
			"uid" => $uid,
			"status" => isset($inputs["type"]) ? intval($inputs["type"]) : 0,  //0为沟通记录  1为内部备注
			"content" => isset($inputs["content"]) ? $inputs["content"] : "",
			"created" => time(),
			"record_time" => isset($inputs['record_time']) ? strtotime($inputs['record_time']) : 0 ,
			"bid" => $bid,
			"shopper_ids_visible" => $shopper_ids_visible,
		);
		$erp_id = $this->record->increase($init);
		
		//调用主站接口，同步沟通记录数据
		if($inputs["type"] == 0 && $erp_id!=0)
		{
            $uname = $this->session->userdata('admin');
            $umobile = $this->session->userdata('admin_mobile');
			$resp = $this->record->syncAddRecord($erp_id, $business["tradeno"], $inputs["content"], $uname, $umobile, $shopper_ids_visible);
			if(!$resp)
			{
				$this->erp_conn->delete("records",array("id"=>$erp_id));
				return failure("主站数据同步失败");
			}
		}
		
		if($erp_id!=0){
			return success('添加成功');
		}else{
			return failure("添加失败");
		}
	}
	
	/**
	 * 获取沟通记录和备注列表
	 * @return type
	 */
	public function recordList()
	{
		//商机id
		$bid = $this->input->get("bid", 0);
		$bid = intval($bid);
		if(!$this->business->findRow(array("id" => $bid)))
		{
			return failure("商机不存在");
		}
		
		//分页
		$inputs = $this->input->get();
		$page = (isset($inputs['page']) && $inputs['page'] > 1) ? $inputs['page'] : 1;
        $pagesize = isset($inputs['pagesize']) ? $inputs['pagesize'] : 10;
		
		//取列表
		$where["bid"] = $bid;
		$where["status"] = isset($inputs["type"]) ? $inputs["type"] : '';
		$record = $this->record->getList($where, $page, $pagesize);
		
		//记录人uid
		$record_info = $record["rows"] ? $record["rows"] : array();
		$user_list = array();
		if(!empty($record_info))
		{
			$uids = array_flatten($record_info, "uid");
			$uids = array_unique($uids);

			$user_list = $this->erp_conn->select("id as uid, username, role_id")->where_in("id", $uids)->get("ew_erp_sys_user")->result_array();
			$user_list = toHashmap($user_list, "uid");
		}
		
		
		if(!empty($record["rows"]))
		{
			foreach ($record["rows"] as &$val)
			{
                $val['content'] = str_replace('\n', '' , $val['content']);
				$val["created_time"] = date("Y-m-d H:i:s", $val["created"]);
				$val['record_time_format'] = date("Y-m-d H:i:s",$val['record_time']);
				if(isset($user_list[$val["uid"]]))
				{
					$val = array_merge($val, $user_list[$val["uid"]]);
				}
			}
		}
		
		return success($record);
	}
	
	public function updateRecord()
	{
		$inputs = $this->input->post();
		$inputs['content'] = ew_filter_quote_html($inputs['content']);
		
		
		$id = isset($inputs["id"]) ? $inputs["id"] : 0;
		$id = intval($id);
		
		$record = $this->record->getRow($id);
		if(!$record)
		{
			return failure("记录不存在");
		}

		$content = isset($inputs["content"]) ? $inputs["content"] : "";
		if(empty($content))
		{
			return failure("请填写内容");
		}
		
		//沟通记录同步主站
		$business = $this->business->findRow(array("id" => $record["bid"]));
        $shopper_ids_visible = "";
		if($record["status"] == 0)
		{
			//获取沟通记录中的可见商家
			list($bus_status,$bus_status_explan) = $this->business->getBusinessStatus();
			list($trade_status,$trade_status_explan) = $this->business->getTradeStatus();
			if($business['trade_status'] == $trade_status['ordered']){//已成单(2-1)
				$shop_map = $this->erp_conn
					->where(array("bid" =>  $record["bid"],"status" => Business_shop_map_model::STATUS_SIGN))
					->get("business_shop_map")->result_array();
				$shopper_ids_visible = isset($shop_map[0]) ? $shop_map[0]['shop_id'] : "" ;
			}else{
				if($business['status'] == $bus_status['parted']){//4-2
					if(!isset($inputs['visible_shopers'])){
						return failure("shopper_ids_visible error!");
					}
					$shopper_ids_visible = implode(",",$inputs['visible_shopers']);
				}else{
					$shopper_ids_visible = "";
				}
			}

            $uname = $this->session->userdata('admin');
            $umobile = $this->session->userdata('admin_mobile');
			$resp = $this->record->syncEditRecord($record["id"], $business["tradeno"], $content, $uname, $umobile, $shopper_ids_visible);
			if(!$resp)
			{
				return failure("主站数据同步失败");
			}
		}
		$upt["content"] = $content;
		$upt["shopper_ids_visible"] = $shopper_ids_visible;
        $upt["record_time"] = strtotime($inputs['record_time']);

        if(!$this->record->updateRow($id, $upt))
		{
			return failure("修改失败");
		}else
		{
			return success('修改成功');
		}
	}
}