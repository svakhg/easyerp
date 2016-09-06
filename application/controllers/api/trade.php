<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trade extends Base_Controller {

    public function __construct(){
        parent::__construct();
		$this->load->model('sys_func_model', 'func');
		$this->load->model("customer/record_model",'record');
		$this->load->model('sys_basesetting_model', 'baseset');
    }

    //ew主站添加需求。通知erp，erp判断用户是否存在客户档案
	public function customerIsExist()
	{
		$post = $this->input->post();
		if(empty($post))
		{
			return failure("参数错误");
		}
//		$post = array(
//			"uid" => 123,
//			"mobile" => "12345678901",
//			"nickname" => "昵称",
//			"from" => "网站端"
//		);
		$record = $this->record->getRecordByEwuid($post["uid"]);
		if(empty($record))
		{
			$init = array(
			"ew_uid" => $post["uid"],
			"cli_mobile" => $post["mobile"],
			"cli_nick" => $post["nickname"],
			);
			//客户来源
			$source = $this->baseset->getInfoByName($post["from"]);
			if(!empty($source))
			{
				$init["cli_source"] = $source["id"];
			}else
			{
				$auth_info = $this->func->getInfoByName("客户来源");
				$attribute = array(
					"setting_id" => $auth_info["id"],
					"name" => $post["from"],
					"order" => 0,
					"enable" => 1,
					"comment" => "网站端来源",
				);
				$result = $this->erp_conn->insert("erp_sys_basesetting", $attribute);
				$init["cli_source"] = $this->erp_conn->insert_id();
			}
			
			if(isset($init["cli_source"]) && $init["cli_source"] > 0)
			{
				$record_id = $this->record->addRecord($init);
			}
		}else
		{
			$record_id = $record["id"];
		}
		
		if(isset($record_id) && $record_id > 0)
		{
			return success($record_id);
		}else
		{
			return failure("通讯失败");
		}
		
	}
	
	//获知渠道
	public function channels()
	{
		$auth_info = $this->func->getInfoByName("获知渠道");
		if(!empty($auth_info))
		{
			$data =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
		}
		
		$data = isset($data) ? $data : array();
		return success($data);
	}
	
}
