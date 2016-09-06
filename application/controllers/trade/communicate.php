<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Communicate extends App_Controller {
	public function __construct(){
		parent::__construct();
		//$this->load->database();
	}
    

	//获取沟通记录列表
	public function comm_list(){
		
		//获取分页参数
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize");
		$offset = ($page-1)*$pagesize;
		$dm_id= $this->input->get("dmid");
		
        $arr = $this->erp_conn->where('ew_demand_communicate_record.demand_id',$dm_id)->order_by("ew_demand_communicate_record.start_time", "desc")->limit($pagesize)->offset($offset)->select('*')->from('ew_demand_communicate_record')->join('ew_erp_sys_user','ew_demand_communicate_record.service_uid=ew_erp_sys_user.id','left')->get();


		$total = $this->erp_conn->where('demand_id',$dm_id)->count_all_results("ew_demand_communicate_record");
		$list = $arr->result_array();

		$infos = array();
		foreach ($list  as $key => $value) {
			$infos[$key]['id']           = $value['c_id'];
			$infos[$key]['dmid']           = $value['demand_id'];
			$infos[$key]['title']         = $value['title'];
			$infos[$key]['client']        = $value['client'];
			$infos[$key]['content']       = $value['content'];
			$infos[$key]['start_time']   = $value['start_time'];
			$infos[$key]['service_uid']   = $value['service_uid'];
			$infos[$key]['text']          = $value['username'];
		}
		$info = array(
		    'total' => $total,
		    'rows' => $infos
	    );
	       
		return success($info);

	}

	//获取沟通记录沟通人的数据
	public function comm_man(){
	
		$list = $this->erp_conn->select('id,username')->where('status',1)->get('ew_erp_sys_user')->result_array();
        $infos = array();
		foreach ($list  as $key => $value) {
			$infos[$key]['id']   = $value['id'];
			$infos[$key]['text'] = $value['username'];
		}
		return success(array('username'=>$infos),TRUE);
	}


	//添加沟通记录数据
	public function comm_add(){
		//获取字段
		$inputs = $this->input->post();
		$init['demand_id'] = $inputs["dmid"] ? $inputs["dmid"] : 0; //demand表中的需求id
		$init['service_uid'] = $inputs["service_uid"];//service_uid 
		$init['client'] = $inputs["client"] ? $inputs["client"] : "";//沟通对象
		$init['title'] = $inputs["title"] ? $inputs["title"] : "";//沟通主题
		$init['content'] = $inputs["content"] ? $inputs["content"] : "";//沟通内容
		$init["start_time"] = $inputs["start_time"] ? $inputs["start_time"] : "";//沟通时间start_time
		//添加操作
		$rows = $this->erp_conn->insert('ew_demand_communicate_record', $init);
		if(empty($rows))
		{
			return failure("添加失败");
		}
		 //添加需求日志
	        $did = $init['demand_id'];   
            $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
	        $data = $brr->row_array();
	        $this->load->model('demand_order_log_model'); 
            $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'添加沟通记录','待审核需求-添加沟通记录');
            
		return success('添加成功');
	}



	//修改沟通记录
	public function comm_edit()
	{

		//获取字段
		$inputs = $this->input->post();
		$id = intval($inputs["id"]);//修改记录的id
		if($id <= 0)
		{
			return failure("参数错误");
		}
		$init['demand_id'] = $inputs["dmid"] ? $inputs["dmid"] : 0; //demand表中的需求id
		$init['service_uid'] = $inputs["service_uid"];//service_uid 
		$init['client'] = $inputs["client"] ? $inputs["client"] : "";//沟通客户的uid
		$init['title'] = $inputs["title"] ? $inputs["title"] : "";//沟通主题
		$init['content'] = $inputs["content"] ? $inputs["content"] : "";//沟通内容
		$init["start_time"] = $inputs["start_time"];//沟通时间
		//修改操作
		$rows = $this->erp_conn->where('c_id',$id)->update('ew_demand_communicate_record', $init);
		
		if(empty($rows)){
			return failure("修改失败");
		}
		 //添加需求日志
	        $did = $init['demand_id'];   
           $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
	        $data = $brr->row_array();
	        $this->load->model('demand_order_log_model'); 
            $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'修改沟通记录','待审核需求-修改沟通记录');
            
		return success('修改成功');
	}
	
}
?>