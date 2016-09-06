<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group extends App_Controller {


	public function __construct(){
        parent::__construct();
    }

	public function index()
	{
        $this->load->view('customer/group_view.php', $this->_data);
	}
	
	public function group_lst()
	{
		$this->load->model("customer/group_model",'team');
		//获取分页参数
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize") ? $this->input->get("pagesize") : Group_model::PAGESIZE;
		//获取列表
		$infos = $this->team->getAllGroups($page, $pagesize);
		
		//搜索
		$find = $this->input->get("find") != "" ? $this->input->get("find") : "";
		//var_dump($find);die;
		if($find != "")
		{
			$infos = $this->team->getTeamByWhere($find,$page, $pagesize);
		}
		return success($infos);
	}

	//添加分组
	public function add()
	{
		//获取字段
		$inputs = $this->input->post();
		
		$init['team_num'] = $inputs["team_num"] ? $inputs["team_num"] : 0;
		$init['team_name'] = $inputs["team_name"] ? $inputs["team_name"] : "";
		$init['comment'] = $inputs["comment"] ? $inputs["comment"] : "";
		
		//添加条件字段
		$init["amount_start"] = isset($inputs["amount_start"]) ? $inputs["amount_start"] : "";
		$init["amount_end"] = isset($inputs["amount_end"]) ? $inputs["amount_end"] : "";
		$init["created_start"] = isset($inputs["created_start"]) ? $inputs["created_start"] : "";
		$init["created_end"] = isset($inputs["created_end"]) ? $inputs["created_end"] : "";
		$init["payment_start"] = isset($inputs["payment_start"]) ? $inputs["payment_start"] : "";
		$init["payment_end"] = isset($inputs["payment_end"]) ? $inputs["payment_end"] : "";
		$init["wedding_start"] = isset($inputs["wedding_start"]) ? $inputs["wedding_start"] : "";
		$init["wedding_end"] = isset($inputs["wedding_end"]) ? $inputs["wedding_end"] : "";
		$init["serves"] = isset($inputs["serves"]) ? $inputs["serves"] : "";
		$init["source"] = isset($inputs["source"]) ? $inputs["source"] : "";
		$init["wedding_country"] = isset($inputs["wedding_country"]) ? $inputs["wedding_country"] : 0;
		$init["wedding_province"] = isset($inputs["wedding_province"]) ? $inputs["wedding_province"] : 0;
		$init["wedding_city"] = isset($inputs["wedding_city"]) ? $inputs["wedding_city"] : 0;
		if(empty($init['team_num']) || empty($init['team_name']) || empty($init['comment']))
		{
			return failure("表单必填项信息不完全");
		}
		
		$this->load->model("customer/group_model",'team');
		//验证分组编号（字母,数字,'-','_'）
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules("team_num", '分组编号', 'required|alpha_dash');
		if($this->form_validation->run() == FALSE)
		{
			return failure("分组编号格式不正确");
		}

		//判断唯一性
		$team_num = $this->team->getTeamByNum($init['team_num']);
		if(!empty($team_num))
		{
			return failure("分组编号已经存在");
		}
		$team_name = $this->team->getTeamByName($init['team_name']);
		if(!empty($team_name))
		{
			return failure("分组名称已经存在");
		}
		//添加操作
		$res = $this->team->addTeam($init);
		
		if(!empty($res))
		{
			return success('添加成功');
		}else
		{
			return failure("添加失败");
		}
	}
	
	//修改分组
	public function edit()
	{
		//获取字段
		$inputs = $this->input->post();		
		$init['team_num'] = $inputs["team_num"] ? $inputs["team_num"] : 0;
		$init['team_name'] = $inputs["team_name"] ? $inputs["team_name"] : "";
		$init['comment'] = $inputs["comment"] ? $inputs["comment"] : "";
		
		//修改条件字段
		$init["amount_start"] = isset($inputs["amount_start"]) ? $inputs["amount_start"] : "";
		$init["amount_end"] = isset($inputs["amount_end"]) ? $inputs["amount_end"] : "";
		$init["created_start"] = isset($inputs["created_start"]) ? $inputs["created_start"] : "";
		$init["created_end"] = isset($inputs["created_end"]) ? $inputs["created_end"] : "";
		$init["payment_start"] = isset($inputs["payment_start"]) ? $inputs["payment_start"] : "";
		$init["payment_end"] = isset($inputs["payment_end"]) ? $inputs["payment_end"] : "";
		$init["wedding_start"] = isset($inputs["wedding_start"]) ? $inputs["wedding_start"] : "";
		$init["wedding_end"] = isset($inputs["wedding_end"]) ? $inputs["wedding_end"] : "";
		$init["serves"] = isset($inputs["serves"]) ? $inputs["serves"] : "";
		$init["source"] = isset($inputs["source"]) ? $inputs["source"] : "";
		$init["wedding_country"] = isset($inputs["wedding_country"]) ? $inputs["wedding_country"] : 0;
		$init["wedding_province"] = isset($inputs["wedding_province"]) ? $inputs["wedding_province"] : 0;
		$init["wedding_city"] = isset($inputs["wedding_city"]) ? $inputs["wedding_city"] : 0;
		if(empty($init['team_num']) || empty($init['team_name']) || empty($init['comment']))
		{
			return failure("表单必填项信息不完全");
		}
		
		$id = intval($inputs["id"]);
		if($id <= 0)
		{
			return failure("参数错误");
		}
		
		$this->load->model("customer/group_model",'team');
		//判断唯一性
		$team_num = $this->team->getTeamByNum($init['team_num']);
		if(!empty($team_num) && $team_num["id"]!=$id)
		{
			return failure("分组编号已经存在");
		}
		$team_name = $this->team->getTeamByName($init['team_name']);
		if(!empty($team_name) && $team_name["id"]!=$id)
		{
			return failure("分组名称已经存在");
		}
		
		//修改操作
		$team_info = $this->team->getTeamById($id);
		if(empty($team_info))
		{
			return failure("修改的记录不存在");
		}
		
		$res = $this->team->editTeam($id, $init);
		
		if(!empty($res))
		{
			return success('修改成功');
		}else
		{
			return failure("修改失败");
		}
	}
	
	//删除分组
	public function del()
	{
		$ids = $this->input->post("ids");
		if($ids == "")
		{
			return failure("参数错误");
		}
		
		//修改操作
		$this->load->model("customer/group_model",'team');
		
		$ids = explode(",", trim($ids, ","));
		if(empty($ids))
		{
			return failure("请选择要删除的记录");
		}
		
		$res = $this->team->delTeams($ids);
		
		if(!empty($res))
		{
			return success('删除成功');
		}else
		{
			return failure("删除失败");
		}
		
	}
}
