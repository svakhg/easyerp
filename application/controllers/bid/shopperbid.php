<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 *商家招投标管理
 */
class Shopperbid extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('bid/Shopperdemand','bid');
        $this->load->model('bid/Detaildemand','detail');
    }

    public function index(){
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
        $this->load->view('bid_view/shopperbid_view',$infos);
    }

    /*
     * 需求列表
     *
     */
    public function bidContentList(){
        $inputs = $this->input->get();
        $this->load->model('bid');
        $this->bid->shopperContentList($inputs,2);
    }

    /*
     * 获取商家招投标订单列表
     *
     */
    public function bidOrderList(){
        $res = $this->bid->getOrderList();
        echo json_encode($res);exit;
    }

    /*
     * 分配商家
     * @author by Abel 2015/5/8
     * @method post
     * @param demand_id 需求id
     * @param shopper_user_ids 分配的商家id字符串 1,2,3,4
     */
    public function allocationShopper()
    {
        $input = $this->input->post();

        if(!isset($input['demand_id']) && !empty($input['shopper_user_ids']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        $data['bid_id'] = $input['demand_id'];
        $data['shopper_user_ids'] = $input['shopper_user_ids'];
//        print_r($data);die();
        //验证需求是否存在
        $item = $this->bid->getOneDemand($data);
        if(empty($item[0]))
        {
            echo json_encode(array('result' => 'fail','info' => '该需求不存在'));exit;
        }
        //验证是否已经审核，判断是否分配商家
        $check_demand = Detaildemand::checkDemandExamine($data);
        if($check_demand){
            echo json_encode(array('result' => 'fail','info' => '审核过的需求不能分配商家'));exit;
        }

        //分配商家
        $result = $this->detail->sendShopper($data);
        if($result === true)
        {
            //添加分配商家的日志
                $u_ids = $input['shopper_user_ids'];
                $id = $input['demand_id'];
                $data_log = $this->bid->getShoppersLog($u_ids,$id,1);

            echo json_encode(array('result' => 'succ','info' => '分配成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '分配失败'));exit;
        }
    }
    
  
    /*
     * 内部便签列表
     * */
	    
    public function innerNote(){
    	//分页
        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        $page = $page < 1 ? 1 : $page;
        $pagesize = $this->input->get("pagesize");
        $offset = ($page-1)*$pagesize;
        $dmid = $this->input->get("dmid");
        $info = $this->bid->noteList($dmid,$page,$pagesize,$offset);
        return success($info);
    }
    
/*
     * 添加便签
     * */
    public function innerNoteAdd(){
    	$inputs = $this->input->post();
        $id = $this->session->userdata("admin_id");
        $dmid = $inputs["dmid"];
        $init["demand_id"] = $dmid;
        $init["service_uid"] = $id;
        $init["note_content"] = $inputs["content"] ? $inputs["content"] : "";
        $init["create_time"] =date("Y-m-d H:i:s");
        $rows = $this->bid->noteAdd($init);
        if($rows==1)
        {
           /*
           *添加商家招投标需求日志
           */
            $dmid = $inputs["dmid"];   
            $data = $this->bid->ngsLog($dmid);
            $this->bid->demandlog($data['id'],0,'',$data['demand_id'],'添加内部便签','商家招投标管理-添加内部便签',0);

            echo json_encode(array('result' => 'succ','info' => '添加成功'));exit;
        }else{
            echo json_encode(array('result' => 'fail','info' => '添加失败'));exit;
        }   
    }
    
    
    
    
    /*
     * 沟通记录列表
     * */
    public function communicateRecord(){
        //获取分页参数
        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        $page = $page < 1 ? 1 : $page;
        $pagesize = $this->input->get("pagesize");
        $offset = ($page-1)*$pagesize;
        $dm_id= $this->input->get("dmid");
        $info = $this->bid->commList($dm_id,$page,$pagesize,$offset);
        return success($info);
    	
    }
/*
     * 添加沟通记录数据
     * */
    public function communicateRecordAdd(){
        //获取字段
        $inputs = $this->input->post();
        $init['demand_id'] = $inputs["dmid"] ? $inputs["dmid"] : 0; //demand表中的需求id
        $init['service_uid'] = $inputs["service_uid"];//service_uid 
        $init['client'] = $inputs["client"] ? $inputs["client"] : "";//沟通对象
        $init['title'] = $inputs["title"] ? $inputs["title"] : "";//沟通主题
        $init['content'] = $inputs["content"] ? $inputs["content"] : "";//沟通内容
        $init["start_time"] = $inputs["start_time"] ? $inputs["start_time"] : "";//沟通时间start_time
        $rows = $this->bid->commAdd($init);
       if($rows==1)
        {
            /*
           *添加商家招投标需求日志
           */
            $dmid = $inputs["dmid"];   
            $data = $this->bid->ngsLog($dmid);
            $this->bid->demandlog($data['id'],0,'',$data['demand_id'],'添加沟通记录','商家招投标管理-添加沟通记录数据',0);

            echo json_encode(array('result' => 'succ','info' => '添加成功'));exit;
        }else{
            echo json_encode(array('result' => 'fail','info' => '添加失败'));exit;
        }   
    }
    

    //修改沟通记录
    public function commEdit()
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
        $rows = $this->bid->commEdit($init,$id);
        if($rows==1){
            /*
           *添加商家招投标需求日志
           */
            $dmid = $inputs["dmid"];   
            $data = $this->bid->ngsLog($dmid);
            $this->bid->demandlog($data['id'],0,'',$data['demand_id'],'修改沟通记录','商家招投标管理-修改沟通记录数据',0);

            echo json_encode(array('result' => 'succ','info' => '修改成功'));exit;
        }else{
            echo json_encode(array('result' => 'fail','info' => '修改失败'));exit;
        }
    }
    
    /*
     * 收支记录列表
     * */
    public function payIn(){
        //获取分页参数
        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        $page = $page < 1 ? 1 : $page;
        $pagesize = $this->input->get("pagesize");
        $offset = ($page-1)*$pagesize;
        $dm_id= $this->input->get("id");
        $info = $this->bid->payList($dm_id,$page,$pagesize,$offset);
        return success($info);
    }
/*
     * 添加收支记录数据
     * */
    public function payInAdd(){
        $crr = date('YmdHis').rand(100000,999999);//流水号
        $uid = $this->session->userdata('admin_id');//当前id
        $arr = $this->erp_conn->where('id',$uid)->get("ew_erp_sys_user");
        $list = $arr->result_array();
        //获取字段
        $inputs = $this->input->post();
        $init['demand_id'] = $inputs["dmid"] ? $inputs["dmid"] : 0;        //demand表中的需求id
        $init['service_id'] = $list[0]['username'];   //操作人 
        $init['fund_type'] = $inputs["fund_type"] ? $inputs["fund_type"] : "";    //款项类型
        $init['pay_set_id'] = $inputs["pay_set_id"] ? $inputs["pay_set_id"] : "";     //支付方式
        $init['pay_amount'] = $inputs["pay_amount"] ? $inputs["pay_amount"] : ""; //支付金额
        $init['pay_man'] = $inputs["pay_man"] ? $inputs["pay_man"] : "";          //收（支）款人
        $init['comments'] = $inputs["comments"] ? $inputs["comments"] : "";       //备注
        $init["start_time"] = date('Y-m-d H:i:s'); //支付时间
        $init["serial_number"] = $crr;//$inputs["serial_number"] ? $inputs["serial_number"] : "";//流水号时间戳 四位随机数 id
        $init["inorout"] = $inputs["flagid"] ? $inputs["flagid"] : "";         //收支类型     1：收      2：支
        $rows = $this->bid->payAdd($init);
        if($rows==1)
        {
            /*
           *添加商家招投标需求日志
           */
           $dmid = $inputs["dmid"];   
           $data = $this->bid->ngsLog($dmid);
           $this->bid->demandlog($data['id'],0,'',$data['demand_id'],'添加收支数据','商家招投标管理-添加收支记录数据',0);

            echo json_encode(array('result' => 'succ','info' => '添加成功'));exit;
        }else{
            echo json_encode(array('result' => 'fail','info' => '添加失败'));exit;
        }   
    }

    /********
    * 
    *  获取需求日志记录列表
    *    
    ********/
    public function demandLog(){
        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        $page = $page < 1 ? 1 : $page;
        $pagesize = $this->input->get("pagesize");
        $offset = ($page-1)*$pagesize;
        $dm_id= $this->input->get("id");
        $info = $this->bid->logList($dm_id,$page,$pagesize,$offset);
        return success($info);
    }


}