<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Record extends App_Controller {


	public function __construct(){
        parent::__construct();
		$this->load->model("commons/region_model",'region');
		$this->load->model('sys_basesetting_model','basesetting');
    }

	public function index()
	{
        $this->load->view('customer/record_view.php', $this->_data);
	}
	
	public function record_lst()
	{
		//获取列表
		$this->load->model("customer/record_model",'record');
		
		//获取分页参数
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize") ? $this->input->get("pagesize") : Record_model::PAGESIZE;
		$offset = ($page-1)*$pagesize;
		
		//搜索
		$where = array();
		//客户来源
		$cli_source = $this->input->get("cli_source") ? $this->input->get("cli_source") : 0;
		if($cli_source)
		{
			$where["cli_source"] = $cli_source;
		}
		//获知渠道
		$channel = $this->input->get("channel") ? $this->input->get("channel") : 0;
		if($channel)
		{
			$where["channel"] = $channel;
		}
		//添加时间
		$start_time = $this->input->get("start_time") ? $this->input->get("start_time") : "";
		$end_time = $this->input->get("end_time") ? $this->input->get("end_time") : "";
		if(!empty($start_time) && !empty($end_time))
		{
			$where["created >="] = $start_time;
			$where["created <="] = $end_time;
		}elseif(!empty ($start_time))
		{
			$where["created >="] = $start_time;
		}elseif (!empty ($end_time))
		{
			$where["created <="] = $end_time;
		}
		//所在省份
		$province = $this->input->get("province") ? $this->input->get("province") : 0;
		if($province)
		{
			$where["province"] = $province;
		}
		//所在市区
		$city = $this->input->get("city") ? $this->input->get("city") : 0;
		if($city)
		{
			$where["city"] = $city;
		}
		//关键字查询
		$keywords = $this->input->get("keywords") ? $this->input->get("keywords") : "";
		if($keywords != "")
		{
			$result = $this->record->getRecordByKeyword($keywords, $page, $pagesize, $where);
			$list = $result["rows"];
			$total = $result["total"];
		}else
		{
			$list = $this->erp_conn->limit($pagesize)->offset($offset)->where($where)->get(Record_model::TBL)->result_array();
			$total = $this->erp_conn->count_all_results(Record_model::TBL);
		}
		foreach ($list as $key => &$row)
		{
			//消费金额和交易次数暂时写死数据
			$row["amount"] = 1234;
			$row["trade_num"] = 22;
			//客户来源
			$cli_info = $this->basesetting->getInfoById($row['cli_source']);
			$row["cli_source_detail"] = $cli_info ? $cli_info["name"] : "";
			//获知渠道
			$cli_info = $this->basesetting->getInfoById($row['channel']);
			$row["channel_detail"] = $cli_info ? $cli_info["name"] : "";
			//所在国家
			$region = $this->region->getRegionById($row["country"]);
			$row["country_detail"] = $region ? $region["name"]: "";
			//所在省
			$region = $this->region->getRegionById($row["province"]);
			$row["province_detail"] = $region ? $region["name"] : "";
			//所在市
			$region = $this->region->getRegionById($row["city"]);
			$row["city_detail"] = $region ? $region["name"] : "";
		}
		$info = array(
            'total' => $total,
            'rows' => $list
			);
		return success($info);
	}

	//添加档案记录
	public function add()
	{
		$this->load->model("customer/record_model",'record');
		//获取字段
		$inputs = $this->input->post();
		
		if(empty($inputs['cli_name']) || empty($inputs['cli_source']) || empty($inputs['channel']))
		{
			return failure("表单必填项信息不完全");
		}
		
		//验证手机号唯一
		$record_info = $this->record->getRecordByPhone($inputs["cli_mobile"]);
		if(!empty($record_info))
		{
			return failure("手机号已存在");
		}
			
		//添加操作
		$res = $this->record->addRecord($inputs);
		
		if($res > 0)
		{
			return success('添加成功');
		}else
		{
			return failure("添加失败");
		}
	}
	
	//修改档案记录
	public function edit()
	{
		$this->load->model("customer/record_model",'record');
		//获取字段
		$inputs = $this->input->post();
		
		$id = intval($inputs["id"]);
		if($id <= 0)
		{
			return failure("参数错误");
		}
		
		//验证手机号唯一
		$record_info = $this->record->getRecordByPhone($inputs["cli_mobile"]);
		if(!empty($record_info) && $record_info["id"]!=$id)
		{
			return failure("手机号已存在");
		}
		
		//修改操作
		$record_info = $this->record->getRecordById($id);
		if(empty($record_info))
		{
			return failure("修改的记录不存在");
		}
		
		$res = $this->record->editRecord($id, $inputs);
		
		if(!empty($res))
		{
			return success('修改成功');
		}else
		{
			return failure("修改失败");
		}
	}
	
	//删除操作
	public function del()
	{
		$ids = intval($this->input->post("ids"));
		if($ids <= 0)
		{
			return failure("参数错误");
		}
		
		//删除操作
		$this->load->model("customer/record_model",'record');
		
		$ids = explode(",", trim($ids, ","));
		if(empty($ids))
		{
			return failure("请选择要删除的记录");
		}
		
		$res = $this->record->delRecords($ids);
		
		if(!empty($res))
		{
			return success('删除成功');
		}else
		{
			return failure("删除失败");
		}
	}
	
	//数据字典
	public function dictionary()
	{
		$this->load->model('sys_basesetting_model','baseset');
		//客户来源
		$auth_info = $this->func->getInfoByName("客户来源");
		$data["cli_source"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name as text");
		
		//获知渠道
		$auth_info = $this->func->getInfoByName("获知渠道");
		$data["channel"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name as text");
		
		//学历
		$data["cli_edu"] = array(
			array(
				"id" => 1,
				"text" => "小学"
			),
			array(
				"id" => 2,
				"text" => "初中"
			),
			array(
				"id" => 3,
				"text" => "高中"
			),
			array(
				"id" => 4,
				"text" => "专科"
			),
			array(
				"id" => 5,
				"text" => "本科"
			),
			array(
				"id" => 6,
				"text" => "硕士"
			),
			array(
				"id" => 7,
				"text" => "博士"
			),
			array(
				"id" => 8,
				"text" => "博士后"
			),
		);
		
		//民族
		$data["cli_race"] = $this->erp_conn->select("id, nation as text")->get('erp_nation')->result_array();
		
		//血型
		$data["cli_blood"] = array(
			array(
				"id" => 1,
				"text" => "O型"
			),
			array(
				"id" => 2,
				"text" => "A型"
			),
			array(
				"id" => 3,
				"text" => "B型"
			),
			array(
				"id" => 4,
				"text" => "AB型"
			)
		);
		
		//地区联级-国家
		$this->load->model("commons/region_model",'region');
		$region = $this->region->getInfoByPid(0, "id, name as text");
		$data["country"] = $region;
		
		//所在省份
		$ch_info = $this->region->getRegionByName("中国");
		$province = $this->region->getInfoByPid($ch_info["id"], "id, name as text");
		$data["province"] = $province;
		
		//标签

		//print_r($data);die;
		return success($data);
	}
	
	//获取所有标签（不带分页）
	public function all_tag()
	{
		$this->load->model("customer/tag_model",'tag');
		$name = $this->input->get("find") ? $this->input->get("find") : "";
		if($name != "")
		{
			$result = $this->erp_conn->select("id, tag_name as text")->like("tag_name", $name)->get(Tag_model::TBL)->result_array();
		}else{
			$result = $this->erp_conn->select("id, tag_name as text")->get(Tag_model::TBL)->result_array();
		}
		
		
		return success($result);
	}
}
