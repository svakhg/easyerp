<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Roles extends App_Controller {

    public function __construct(){
        parent::__construct();
    }

	//角色管理
    public function index()
    {
        $this->load->view('system/roles/index');
    }
	
	//角色列表
	public function role_list()
	{
		$this->load->model("system/roles_model",'roles');
		$this->load->model("sys_user_model",'user');
		//获取分页参数
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize") ? $this->input->get("pagesize") : Roles_model::PAGESIZE;
		$offset = ($page-1)*$pagesize;
		
		//搜索
		$where = array();
		$like = array();

		$role_name = $this->input->get("role_name") ? $this->input->get("role_name") : "";
		if($role_name)
		{
			$like["role_name"] = $role_name;
		}
		$start_time = $this->input->get("start_time") ? $this->input->get("start_time") : "";
		$end_time = $this->input->get("end_time") ? $this->input->get("end_time") : "";
		if(!empty($start_time) && !empty($end_time))
		{
			$where["create_time >="] = $start_time;
			$where["create_time <="] = $end_time;
		}elseif(!empty ($start_time))
		{
			$where["create_time >="] = $start_time;
		}elseif (!empty ($end_time))
		{
			$where["create_time <="] = $end_time;
		}
		
		//取列表
		$query = $this->erp_conn->where($where)->like($like)->limit($pagesize)->offset($offset)->get(Roles_model::TBL);
        $list = $query->result_array();
		$total = $this->erp_conn->where($where)->like($like)->count_all_results(Roles_model::TBL);
		foreach ($list as $key => &$val)
		{
			$user_info = $this->user->getInfoById($val["creator_id"]);
			$val["creator_id"] = $user_info ? $user_info["username"] : "";
		}
		$info = array(
            'total' => $total,
            'rows' => $list
        );
		return success($info);
	}
	
	//添加角色
	public function add()
	{
		$this->load->model("system/roles_model",'roles');
		//获取id
		$id = $this->session->userdata("admin_id");
		//获取字段
		$inputs = $this->input->post();
		$init['role_name'] = $inputs["role_name"] ? $inputs["role_name"] : 0;
		$init['role_comment'] = $inputs["role_comment"] ? $inputs["role_comment"] : "";
		$init['creator_id'] = $id;
		$init["create_time"] = date('Y-m-d H:i:s',time());
		$init["func_id"] = "";
		if(empty($init['role_name']) || empty($init['role_comment']))
		{
			return failure("表单必填项信息不完全");
		}
		//判断唯一性
		$role_info = $this->roles->getInfoByName($init['role_name']);
		if(!empty($role_info))
		{
			return failure("角色名称已经存在");
		}
		//添加操作
		$rows = $this->erp_conn->insert(Roles_model::TBL, $init);
		if(empty($rows))
		{
			return failure("添加失败");
		}
		return success('添加成功');
		
	}
	
	//修改角色
	public function edit()
	{
		$this->load->model("system/roles_model",'roles');
		//获取字段
		$inputs = $this->input->post();
		$id = intval($inputs["id"]);
		if($id <= 0)
		{
			return failure("参数错误");
		}
		
		$init['role_name'] = $inputs["role_name"] ? $inputs["role_name"] : 0;
		$init['role_comment'] = $inputs["role_comment"] ? $inputs["role_comment"] : "";
		if(empty($init['role_name']) || empty($init['role_comment']))
		{
			return failure("表单必填项信息不完全");
		}
		//判断唯一性
		$role_info = $this->roles->getInfoByName($init['role_name']);
		if(!empty($role_info) && $role_info["id"]!=$id)
		{
			return failure("角色名称已经存在");
		}
		
		//修改操作
		$team_info_id = $this->roles->getInfoById($id);
		if(empty($team_info_id))
		{
			return failure("修改的记录不存在");
		}
		//修改操作
		$rows = $this->erp_conn->where('id', $id)->update(Roles_model::TBL, $init);
		if(empty($rows))
		{
			return failure("修改失败");
		}
		return success('修改成功');
		
	}
	
	//删除
	public function del()
	{
		$ids = $this->input->post("ids");
		if($ids == "")
		{
			return failure("参数错误");
		}
		
		//删除操作
		$this->load->model("system/roles_model",'roles');
		
		$ids = explode(",", trim($ids, ","));
		if(empty($ids))
		{
			return failure("请选择要删除的记录");
		}
		
		$res = $this->roles->delRoles($ids);
		
		if(!empty($res))
		{
			return success('删除成功');
		}else
		{
			return failure("删除失败");
		}
		
	}
	
	//获取权限列表
	public function auth_list()
	{
		$tree_arr = $this->func->permission_lst();

		$CI = & get_instance();
        return $CI->output->set_content_type('application/json')->set_output(json_encode($tree_arr));
	}
	
	//角色授权
	public function authorize()
	{
		$this->load->model("system/roles_model",'roles');
		$id = $this->input->post("id");
		$auth = $this->input->post("auth");
		//修改数据
		$data = array(
			"func_id" => $auth,
		);
		if(!empty($auth))
		{
			$this->erp_conn->where("id", $id)->update(Roles_model::TBL, $data);
			return success('角色授权成功');
		}
		return failure("角色授权失败");
	}
	
}
