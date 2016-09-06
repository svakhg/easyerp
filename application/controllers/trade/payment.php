<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends App_Controller {
    public function __construct(){
        parent::__construct();
    }
    /*
     ***添加婚礼需求支付纪录数据
     */
    public function pay_add(){
    
    	$crr = date('YmdHis').rand(100000,999999);//流水号
  		$uid = $this->session->userdata('admin_id');//当前id
  		$arr = $this->erp_conn->where('id',$uid)->get("ew_erp_sys_user");
        $list = $arr->result_array();
        //获取字段
        $inputs = $this->input->post();
        $init['demand_id'] = $inputs["dmid"] ? $inputs["dmid"] : 0;        //demand表中的需求id
        $init['contract_id'] = isset($inputs['sid']) ? $inputs['sid'] : 0;//合同id
        $init['service_id'] = $list[0]['username'];   //操作人 
        $init['fund_type'] = $inputs["fund_type"] ? $inputs["fund_type"] : "";    //款项类型
        $init['pay_set_id'] = $inputs["pay_set_id"] ? $inputs["pay_set_id"] : "";     //支付方式
        $init['pay_amount'] = $inputs["pay_amount"] ? $inputs["pay_amount"] : ""; //支付金额
        $init['pay_man'] = $inputs["pay_man"] ? $inputs["pay_man"] : "";          //收（支）款人
        $init['comments'] = $inputs["comments"] ? $inputs["comments"] : "";       //备注
        $init["start_time"] = date('Y-m-d H:i:s'); //支付时间
		$init["serial_number"] = $crr;//$inputs["serial_number"] ? $inputs["serial_number"] : "";//流水号时间戳 四位随机数 id
		$init["inorout"] = $inputs["flagid"] ? $inputs["flagid"] : "";         //收支类型     1：收      2：支
        //添加操作
        $rows = $this->erp_conn->insert('ew_demand_payment_record', $init);
        if(empty($rows))
        {
            return failure("添加失败");
        }
         //添加需求日志
            $did = $init['demand_id'];   
            $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
            $data = $brr->row_array();
            $this->load->model('demand_order_log_model'); 
            $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'添加收支记录','待审核需求-添加收支记录');
        return success('添加成功');
    }

    /*
     * 
     *    获取支付纪录数据列表
     *    
     */
    public function pay_list(){
        //获取分页参数
        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        $page = $page < 1 ? 1 : $page;
        $pagesize = $this->input->get("pagesize");
        $offset = ($page-1)*$pagesize;
        $dm_id= $this->input->get("id");
        $input_get = $this->input->get();
        if(isset($input_get['sid'])){
            $this->erp_conn->where('ew_demand_payment_record.contract_id',$input_get['sid']);
        }
        $arr = $this->erp_conn
            ->where('ew_demand_payment_record.demand_id',isset($input_get['demand_id'])?$input_get['demand_id']:$input_get['id'])
            ->order_by("ew_demand_payment_record.start_time", "desc")
            ->limit($pagesize)
            ->offset($offset)
            ->select('*')
            ->from('ew_demand_payment_record')
            ->join('ew_erp_sys_basesetting','ew_demand_payment_record.pay_set_id=ew_erp_sys_basesetting.id')
            ->get();
	    // echo $this->erp_conn->last_query();exit();
        if(isset($input_get['sid'])){
            $this->erp_conn->where('ew_demand_payment_record.contract_id',$input_get['sid']);
        }
	    $total = $this->erp_conn->where('ew_demand_payment_record.demand_id',$dm_id)->count_all_results("ew_demand_payment_record");
        $list = $arr->result_array();
        $infos = array();
        foreach ($list as $key => $value) {
			 $infos[$key]['id']             = $value['p_id'];
             $infos[$key]['demand_id']      = $value['demand_id']; 
             $infos[$key]['service_id']     = $value['service_id'];
             $infos[$key]['fund_type']      = $value['fund_type'];
             $infos[$key]['pay_set_id']     = $value['name'];
             $infos[$key]['pay_amount']     = $value['pay_amount'];
             $infos[$key]['pay_man']        = $value['pay_man'];
             $infos[$key]['comments']       = $value['comments'];
		     $infos[$key]['serial_number']  = $value['serial_number'];
		     $infos[$key]['inorout']        = $value['inorout'];
             $infos[$key]["start_time"]     = $value['start_time'];
             if($infos[$key]['inorout']==1){
                 $infos[$key]['inorout']="收";
             }
             if($infos[$key]['inorout']==2){
                 $infos[$key]['inorout']="支";
             }
        }
        $info = array(
            'total' => $total,
            'rows' => $infos
        );
		return success($info);
		
    }

	/********
    * 
    *  获取需求日志记录列表
    *    
    ********/
    public function demand_log(){
        //获取分页参数
        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        $page = $page < 1 ? 1 : $page;
        $pagesize = $this->input->get("pagesize");
        $offset = ($page-1)*$pagesize;
        $dmid= $this->input->get("id");
        $arr = $this->erp_conn->where('demand_id',$dmid)->order_by('id','desc')->limit($pagesize)->offset($offset)->get('ew_demand_order_log');
        $total = $this->erp_conn->where('demand_id',$dmid)->count_all_results("ew_demand_order_log");
        $list = $arr->result_array();
        $info = array();
        $info = array(
            'total' => $total,
            'rows' => $list
        );
		return success($info);
		
    }
}
?>