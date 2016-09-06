<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Note extends App_Controller {
    public function __construct(){
        parent::__construct();
    }
    //添加内部标签
    public function add_note(){
		//获取id
		$id = $this->session->userdata("admin_id");		
		$inputs = $this->input->post();
		$dmid = $inputs["dmid"];
		$init["demand_id"] = $dmid;
		$init["service_uid"] = $id;
		$init["note_content"] = $inputs["content"] ? $inputs["content"] : "";
		$init["create_time"] =date("Y-m-d H:i:s");
		$rows = $this->erp_conn->insert('ew_demand_inner_note', $init);
		if($rows==1)
		{
			//添加需求日志
	        $did = $dmid;   
            $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
            //echo $this->ew_conn->last_query();die;
	        $data = $brr->row_array();
	       // print_r($data);die;
	        $this->load->model('demand_order_log_model'); 
            $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'添加便签','待审核需求-添加便签');
            
			return success('添加成功');
		}else{
			return failure('添加失败');
		}	
    }
    //获取便签列表
    public function get_note(){
	    //分页
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize");
		$offset = ($page-1)*$pagesize;
		$dmid = $this->input->get("dmid");
		//sql处理
	 	//$arr = $this->erp_conn->where("demand_id",$dmid)->order_by("id", "desc")->limit($pagesize)->offset($offset)->get('ew_demand_inner_note');
		$arr = $this->erp_conn->where("demand_id",$dmid)->order_by("ew_demand_inner_note.n_id", "desc")->limit($pagesize)->offset($offset)->select('ew_demand_inner_note.n_id,ew_demand_inner_note.note_content,ew_demand_inner_note.create_time,ew_demand_inner_note.service_uid,ew_erp_sys_user.username')->from('ew_demand_inner_note')->join('ew_erp_sys_user','ew_demand_inner_note.service_uid=ew_erp_sys_user.id','left')->get();
		//获取条数		
		$total = $this->erp_conn->where("demand_id",$dmid)->count_all_results("ew_demand_inner_note");
		$list = $arr->result_array();
		$info = array(
            'total' => $total,
            'rows' => $list
        );   
		return success($info);
    }

}

