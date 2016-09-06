<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tag extends App_Controller {


	public function __construct(){
        parent::__construct();
    }

	public function index()
	{
        $this->load->view('customer/tag_view.php', $this->_data);
	}
	
	public function tag_lst()
	{
		$this->load->model("customer/tag_model",'tag');
		//获取分页参数
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize") ? $this->input->get("pagesize") : Tag_model::PAGESIZE;
		//获取列表
		$infos = $this->tag->getAllTags($page, $pagesize);
		
		//搜索
		$find = $this->input->get("find") != ""  ? $this->input->get("find") : "";
		//var_dump($find);die;
		if($find != "")
		{
			$infos = $this->tag->getTagsByWhere($find,$page, $pagesize);
		}
		return success($infos);
	}

	//添加标签
	public function add()
	{
		//获取字段
		$inputs = $this->input->post();
		
		$init['order'] = $inputs["order"] ? $inputs["order"] : 0;
		$init['tag_name'] = $inputs["tag_name"] ? $inputs["tag_name"] : "";
		$init['comment'] = $inputs["comment"] ? $inputs["comment"] : "";
		if(empty($init['order']) || empty($init['tag_name']))
		{
			return failure("表单信息不完全");
		}
		if($init['order'] > 99999999)
		{
			return failure("标签编号请输入0~99999999的数字");
		}
		
		$this->load->model("customer/tag_model",'tag');
		//判断标签名唯一性
		$tag_info = $this->tag->getTagByName($init['tag_name']);
		if(!empty($tag_info))
		{
			return failure("标签名已存在");
		}
		
		//添加操作
		$res = $this->tag->addTag($init);
		
		if(!empty($res))
		{
			return success('添加成功');
		}else
		{
			return failure("添加失败");
		}
	}
	
	//修改标签
	public function edit()
	{
		//获取字段
		$inputs = $this->input->post();
		$init['order'] = $inputs["order"] ? $inputs["order"] : 0;
		$init['tag_name'] = $inputs["tag_name"] ? $inputs["tag_name"] : "";
		$init['comment'] = $inputs["comment"] ? $inputs["comment"] : "";
		if(empty($init['order']) || empty($init['tag_name']))
		{
			return failure("表单信息不完全");
		}
		if($init['order'] > 99999999)
		{
			return failure("标签编号请输入0~99999999的数字");
		}
		
		$id = intval($inputs["id"]);
		if($id <= 0)
		{
			return failure("参数错误");
		}
		
		$this->load->model("customer/tag_model",'tag');
		//判断标签名唯一性
		$tag_row = $this->tag->getTagByName($init['tag_name']);
		if(!empty($tag_row) && $tag_row["id"]!=$id)
		{
			return failure("标签名已存在");
		}
		
		
		//修改操作
		$tag_info = $this->tag->getTagById($id);
		if(empty($tag_info))
		{
			return failure("修改的记录不存在");
		}
		
		$res = $this->tag->editTag($id, $init);
		
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
		$ids = $this->input->post("ids");
		if($ids == "")
		{
			return failure("参数错误");
		}
		
		//修改操作
		$this->load->model("customer/tag_model",'tag');
		
		$ids = explode(",", trim($ids, ","));
		if(empty($ids))
		{
			return failure("请选择要删除的记录");
		}
		
		$res = $this->tag->delTags($ids);
		
		if(!empty($res))
		{
			return success('删除成功');
		}else
		{
			return failure("删除失败");
		}
	}
}
