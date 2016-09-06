<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appdemand extends App_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->helper('array');
        $this->load->model('sys_trademarksetting_model', 'trademark');
		$this->load->model('sys_basesetting_model','baseset');
        $this->load->model('sys_user_model', 'user');
		$this->load->model("customer/record_model",'record');
    }
	
	//app需求草稿预览页
    public function index()
	{
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
        $this->load->view('trade/appdemend_view',$infos);
    }
	
	//需求列表
	public function getList()
	{
		//分页
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
		$offset = ($page-1)*$pagesize;
		
		$where ='';
        $where.="1=1 ";
		
		$arr = "select id,username,phone,status,remarks,created,shopperalias,wed_date,wed_location,wed_place,shopperuid from ew_demand_draft where ".$where." order by id desc limit ".$offset.",".$pagesize;
		$lista=$this->ew_conn->query($arr);
//		echo $this->erp_conn->last_query();
        $list = $lista->result_array();
		//获取条数		
        $count = "select count(*) as num from  ew_demand_draft where ".$where;
        $totala=$this->ew_conn->query($count)->row_array(); 
        $total = $totala['num'];
		
		//处理数组
		$info = array();
		$shopperalias = array();
		$shopperuid = array();
		foreach ($list as $key => $val)
		{
			if(!empty($val["shopperalias"]) && !in_array($val["shopperalias"], $shopperalias))
			{
				$shopperalias[] = $val["shopperalias"];
			}
			if(!empty($val["shopperuid"]) && !in_array($val["shopperuid"], $shopperuid))
			{
				$shopperuid[] = $val["shopperuid"];
			}
		}
		//商家类型
		$list_type = array();
		if($shopperalias)
		{
			$shopperalias = implode("','", $shopperalias);
			$sql_type = "select id,option_type,option_alias,option_name from ew_options where option_type = 1 and option_alias in ('". $shopperalias . "')";
			$list_type = $this->ew_conn->query($sql_type)->result_array();
		}
		$type_hash = array();
		foreach($list_type as $type)
		{
			$type_hash[$type["option_alias"]] = $type;
		}
		
		//商家昵称
		$list_name = array();
		if($shopperuid)
		{
			$shopperuid = implode(",", $shopperuid);
			$sql_name = "select uid,nickname from ew_users where type = 2 and uid in (" . $shopperuid . ")";
			$list_name = $this->ew_conn->query($sql_name)->result_array();
		}
		$name_hash = array();
		foreach($list_name as $name)
		{
			$name_hash[$name["uid"]] = $name;
		}	
		
		foreach ($list as $key => &$v)
		{
			//商家类型
			$type_info = isset($type_hash[$v["shopperalias"]]) ? $type_hash[$v["shopperalias"]] : array();
			$v["shoppertype"] = $type_info ? $type_info["option_name"] : "";
			//商家昵称
			$user_info = isset($name_hash[$v["shopperuid"]]) ? $name_hash[$v["shopperuid"]] : array();
			$v["shoppername"] = $user_info ? $user_info["nickname"] : "";
		}
		$info = array(
            'total' => $total,
            'rows' => $list
        );
		return success($info);
	}
}

