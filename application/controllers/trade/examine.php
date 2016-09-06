<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Examine extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('demand/demand_contract_model','demand');
        $this->load->model('ew/demand_content_model','content');
        $this->load->model('ew/demand_order_model','order');
        $this->load->model('ew/demand_qa_model','qa');
        $this->load->model('baidupush/baidupush');
	    $this->load->model("customer/record_model",'record');
        $this->load->model('sys_basesetting_model','baseset');
        $this->load->model('demand_order_log_model','order_log');
        $this->load->model('demand/demand_contract_model','contract');
        $this->load->model('demand/demands','demands');
    }

	//待审核需求页
    public function index()
    {
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
        $this->load->view('trade/examine_view',$infos);
    }

    /**
     * 获取待审核需求列表
     */
    public function getlist()
    {
        $inputs = $this->input->get();
        $pagesize = isset($inputs['pagesize']) ? intval($inputs['pagesize']) : 10;
        $page = isset($inputs['page']) ? intval($inputs['page']) : 1;
        if(isset($inputs['status'])){
            if($inputs['status'] == ''){
                $status = 'all';
            }else{
                if($inputs['status'] == 'no'){
                    $status = 0;
                }elseif($inputs['status'] == 'yes'){
                    $status = 4;
                }
            }
        }

        //地点组装1
        $country = isset($inputs['country']) ? $inputs['country'] : '';
        $province = isset($inputs['province']) ? $inputs['province'] : '';
        $city = isset($inputs['city']) ? $inputs['city'] : '';
        $wed_location = implode(',',array($country,$province,$city));

        if(isset($inputs['shopper_alias']) && $inputs['shopper_alias'] != '')
        {
            $shopper_alias = mb_substr($inputs['shopper_alias'],0,-2);//一站式的策划师or单项的四大金刚的别名
        }
        else
        {
            if(isset($inputs['alias_code']) && $inputs['alias_code'] != '')
            {
                $shopper_alias = $inputs['alias_code'];//预算类型
            }
            else
            {
                $shopper_alias = '';
            }
        }

        //查询条件
        $keys = array(
            'status' => ''.$status.'',//待审核的状态
            'pagesize' => $pagesize,
            'page' => $page,
            'counselor_uid' => isset($inputs['counselor_uid']) ? intval($inputs['counselor_uid']) : '',//新人顾问
            'channel' => isset($inputs['channel']) ? intval($inputs['channel']) : '',
            'cli_source' => isset($inputs['cli_source']) ? trim($inputs['cli_source']) : '',
            'mode' => isset($inputs['mode']) ? $inputs['mode'] : '',//找商家方式
            'remander_id' => isset($inputs['remander_id']) ? $inputs['remander_id'] : '',//交易提示id
            'type' => isset($inputs['type']) ? $inputs['type'] : '',//需求类型
            'add_from' => isset($inputs['add_from']) ? $inputs['add_from'] : '',//添加时间 开始
            'add_to' => isset($inputs['add_to']) ? $inputs['add_to'] : '',//添加时间 结束
            'wed_from' => isset($inputs['wed_from']) ? $inputs['wed_from'] : '',//查询婚期时间 开始
            'wed_to' => isset($inputs['wed_to']) ? $inputs['wed_to'] : '',//查询婚期时间 结束
            'condition' => isset($inputs['condition']) ? trim($inputs['condition']) : '',//条件查询
            'condition_text' => isset($inputs['condition_text']) ? trim($inputs['condition_text']) : '',//条件查询域
            'wed_location' => (isset($inputs['country']) && ($inputs['country'] != '')) ? $wed_location : '',//婚礼地点
            'cli_tag' => isset($inputs['cli_tag']) ? $inputs['cli_tag'] : '',//客户标签
            'shopper_alias' => $shopper_alias,
            'budget' =>  isset($inputs['budget']) ? $inputs['budget'] : '',//预算
        );

        $keys_final = array();
        foreach($keys as $key => $v)
        {
            if($v != ''){
                $keys_final[$key] = $v;
            }
        }
        $result = $this->demands->DemandList($keys_final);

        if(empty($result['rows']))
        {
            return success(array('total'=>0,'rows'=>array()));
        }
//        print_r($result['rows']);die();

        foreach($result['rows'] as &$v)
        {
            $this->lang->load('date','chinese');
            $this->load->helper('date');
            $v['compare_time'] = compare_to_now($v['create_time']);
            $v['expired'] = intval((time() - strtotime($v['create_time']))/3600) >= 21 ? 1 : 0;

            //找商家类型名字
            switch($v['shopper_alias']){
                case 'wedplanners':
                    $v['shopper_alias_name'] = '找策划师';
                    break;
                case 'wedmaster':
                    $v['shopper_alias_name'] = '找主持人';
                    break;
                case 'makeup':
                    $v['shopper_alias_name'] = '找化妆师';
                    break;
                case 'wedvideo':
                    $v['shopper_alias_name'] = '找婚礼摄像';
                    break;
                case 'wedphotoer':
                    $v['shopper_alias_name'] = '找婚礼摄影';
                    break;
                case 'sitelayout':
                    $v['shopper_alias_name'] = '找场地布置';
                    break;
            }

            //获取单项需求的预算金额 查询qa表里的amount 、hspz_amount(摄像：婚纱拍照)、wdy_amount(摄影：婚礼前的爱情微电影); hlgp_amount(婚礼跟拍);
            $qa_amount = $this->ew_conn->where('content_id',$v['id'])->get('demand_qa')->result_array();

            if($v['type'] == 2){//判断是否是单项
                foreach($qa_amount as $val){

                    if($val['alias']=='amount'){

                        $v['budget'] = $val['answer'];

                    }elseif($val['alias']=='hspz_amount'){

                        $v['budget'] = $val['answer'];

                    }elseif($val['alias']=='wdy_amount'){

                        $v['budget'] = $val['answer'];

                    }elseif($val['alias']=='hlgp_amount'){

                        $v['budget'] = $val['answer'];
                    }

                }
            }
//            print_r($result);die();

        }
        return success($result);
    }

    /**
     * 分配顾问
     * param ：demand_id 需求id
     * param ：user_id   新人用户id
     */
    public function send_consultant(){
        //获取待分配的需求id
        $demand_id = $this->input->post('ids');
        $demand_ids = explode(',', $demand_id);
        foreach($demand_ids as $v){
           $ret = $this->curl->get($this->config->item('ew_domain').'erp/demand/demand-by-id?id='.$v);
           $ret = json_decode($ret,true);
           if(empty($ret)){
               return failure('参数错误！');
           }
           //获取新人的id
           $user_id = $this->input->post('param');
           $result = $this->curl->post($this->config->item('ew_domain').'erp/demand/change-content-counselor', array('id' => $v, 'counselor_uid' => $user_id));
        
            //添加需求日志
	         $did = $v;
             $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
	         $data = $brr->row_array();
	         $this->load->model('demand_order_log_model'); 
             $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'分配顾问','待审核需求_分配顾问');
        }
        return $result ? success('分配新人成功！') : failure('分配新人失败！');
    }



    /**
     * 为需求做标记操作
     * param : demand_ids = 1,2,3
     */
    public function marking()
    {
        //获取待分配的需求ids
        $demand_ids = $this->input->post('ids');
        $demand_ids = explode(',', $demand_ids);
        //获取标记的id
        $mark_id = $this->input->post('param');
        $i = 0;
        foreach($demand_ids as $v){
            //检查需求是否存在
            $ret = $this->curl->get($this->config->item('ew_domain').'erp/demand/demand-by-id?id='.$v);
            $ret = json_decode($ret,true);
            if(empty($ret)){
                return failure('参数错误！');
            }

            $result = $this->curl->post($this->config->item('ew_domain').'erp/demand/change-content-remander', array('id' => $v, 'remander_id' => $mark_id));
            $i++;
            
             //添加需求日志
	        $did = $v;   
            $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
	        $data = $brr->row_array();
	        $this->load->model('demand_order_log_model'); 
            $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'标记操作','待审核需求_标记操作');
        }
        return $i ? success('标记成功！') : failure('标记失败！');
    }

    /**
     * 审核需求操作
     * demand_ids 是id
     */
    public function examining()
    {
        //获取待分配的需求ids
        $demand_ids_str = $this->input->post('ids');
        $demand_ids = explode(',', $demand_ids_str);
        if(!is_array($demand_ids)){
            $demand_ids = array(0 => $demand_ids);
        }

        foreach($demand_ids as $v){
            //检查需求是否存在
            $ret = $this->curl->get($this->config->item('ew_domain').'erp/demand/demand-by-id?id='.$v);
            $ret = json_decode($ret,true);
            if(empty($ret)){
                return failure('参数错误！');
            }
            if($ret['status'] != 0 && $ret['status'] !=4){//跳出未审核的需求
                continue;
            }
            //检查该需求是否有订单，获取订单

            $orders = $this->curl->get($this->config->item('ew_domain').'erp/demand/orders-by-id?id='.$v);
            $orders_arr = json_decode($orders,true);
            //发送短信
            foreach($orders_arr as $_v){
                if($_v['shopper_alias'] == "wedplanners"){//策划师文案与四大金刚不同
                    $phone = $this->content->getPhoneByUid($_v['shopper_user_id']);
                    $msg = "您有一条客户咨询，请您马上登录易结，前往订单管理>跟进中的订单中查看客户信息";
                    try{
                        $this->sms->send(array($phone),$msg);
                    }catch(Exception $ex){

                    }
                }else{
                    $phone = $this->content->getPhoneByUid($_v['shopper_user_id']);
                    $msg = "您有一条客户咨询，请您马上登录易结，前往订单管理中查看，48小时内接单有效";
                    try{
                        $this->sms->send(array($phone),$msg);
                    }catch(Exception $ex){
                        
                    }
                }
            }
            if(!empty($orders)){
                if($ret['mode'] == 1){
                    $status = 11;//招投标
                }elseif($ret['mode'] == 2){
                    $status = 41;//指定商家
                }
                if($ret['type'] == 1)//新人一站式流程修改（一站式审核通过直接变为41）
                {
                    $status = 41;
                }
                $this->curl->post($this->config->item('ew_domain').'erp/demand/change-content-status', array('id' => $v, 'status' => 1));
                $this->curl->post($this->config->item('ew_domain').'erp/demand/change-order-status', array('content_id' => $v, 'status' => $status));

                //百度推送 begin---------------------------------------------------------------------
                $order_list = $this->order->getOrdersByContentId($v,"id, status, shopper_user_id, shopper_alias");
                foreach($order_list as $val){
                    $data['demand_id'] =  $ret['demand_id'];
                    $data['id'] =  $val['id'];
                    $data['status'] =  $val['status'];
                    $data['shopper_user_id'] =  $val['shopper_user_id'];
                    $data['shopper_alias'] =  $val['shopper_alias'];
                    $this->baidupush->BaiduPushForErp($data);
                }
                //百度推送 end------------------------------------------------------------------------


                //记录当前客服的信息
                $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "examine demand id=".$v);
            }else{
                return failure("勾选的需求".$v."还没有分配商家！");
            }
        
            //添加需求日志
	        $did = $v;   
            $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
	        $data = $brr->row_array();
	        $this->load->model('demand_order_log_model'); 
            $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'审核需求','待审核需求_审核需求');

         }   
            
        return  success('审核成功！');

    }

    /**
     * 转招投标
     * param ：$demand_id 需求id
     */
    public function change_mode()
    {
        //获取需求id
        $demand_id = $this->input->post('ids');
        $demand_ids = explode(',', $demand_id);
        $change_mode = $this->input->post('param');

        foreach ($demand_ids as $v) {
            
        
          $ret = $this->curl->get($this->config->item('ew_domain').'erp/demand/demand-by-id?id='.$v);
          $ret = json_decode($ret,true);
          if(empty($ret)){
              return failure('参数错误！');
          }
          if($ret['mode'] == 1){
              return failure('已经是招投标了！');
          }
          $result = $this->curl->post($this->config->item('ew_domain').'erp/demand/change-content-mode', array('id'=> $v,'mode' => 1,'change_mode' => $change_mode));
          //记录当前客服的信息
          $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "change mode id=".$v);
        
          //添加需求日志
	        $did = $demand_id;   
            $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
	        $data = $brr->row_array();
	        $this->load->model('demand_order_log_model');
            $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'转招投标','待审核需求_转招投标');
        
        }
        return $result ? success('转投标成功！') : failure('转投标失败！');
    }

    /**
     * 关闭需求操作 相应的所有order将被关闭
     * param ： demand_ids需求的id字符串
     */
    public function close_demand()
    {
        //获取待关闭的需求ids
        $demand_ids = $this->input->post('ids');
        $demand_id = explode(',', $demand_ids);
        $demand_end = $this->input->post('param');
        
        foreach($demand_id as $v){
          //检查需求是否存在
          $res = $this->curl->get($this->config->item('ew_domain').'erp/demand/demand-by-id?id='.$v);
          $res = json_decode($res,true);
          if(empty($res)){
              return failure('参数错误！');
          }
          //关闭需求
          $ret = $this->curl->post($this->config->item('ew_domain').'erp/demand/demand-close', array('id' => $v,'demand_end' => $demand_end,'type' => $res['type']));
          $ret = json_decode($ret,true);
          //记录当前客服的信息
          $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "close demand id=".$v);
          //记录操作日志
          $did = $v;
          $brr = $this->ew_conn->where('id',$did)->select('id,demand_id')->from('ew_demand_content')->get();
          $data = $brr->row_array();
          $this->load->model('demand_order_log_model');
          $this->demand_order_log_model->demandlog($data['id'],0,'',$data['demand_id'],'关闭交易','待审核需求_关闭交易');
        
        }
	        
        return success('关闭成功！');
    }

    /**
     * 为需求分配商家
     * param ： ids //需求的id
     * param ： param 商家的信息id json
     */
    public function send_shopper(){
        //获取需求ids
        $demand_id = $this->input->post('ids');

        //检查需求是否存在
        $ret = $this->curl->get($this->config->item('ew_domain').'erp/demand/demand-by-id?id='.$demand_id);
        $ret = json_decode($ret,true);
        $mode = $ret['mode'];
        $param = $this->input->post('param');

        $base_arr = json_decode($param, TRUE);

        $shoper_ids = array();
        foreach($base_arr as $key => $ids)
        {
            if(!empty($ids))
            {
                foreach ($ids as $id)
                {
                    $shoper_ids[$key][] = array(
                        "id" => $id
                    );
                }
            }
        }
       
        $info = $this->curl->post($this->config->item('ew_domain').'erp/demand/insert-send-shopper', array('id' => $demand_id,'wed_info' => $shoper_ids,'mode'=>$mode));
        $info = json_decode($info,true);

        if($info['result'] == 'succ'){
            //记录当前客服的信息
            $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "send shoper id=".$demand_id);

              //添加需求日志
	        $did = $demand_id;
            $brr = $this->ew_conn->where('ew_demand_content.id',$did)->select('ew_demand_order.id,ew_demand_order.content_id,ew_demand_content.demand_id,ew_demand_order.order_id')->from('ew_demand_content')->join('ew_demand_order','ew_demand_content.id=ew_demand_order.content_id')->get();
	        $data = $brr->row_array();
	        $this->load->model('demand_order_log_model');
            $this->demand_order_log_model->demandlog($data['content_id'],$data['id'],$data['order_id'],$data['demand_id'],'分配商家','待审核需求_分配商家');

   
            return success('分配成功！');
        }elseif($info['result'] == 'fail'){
            return failure('分配失败了！');
        }
    }

    /*
     * 移除商家
     * method post
     * param
     * demand_id 需求id
     * order_ids 要移除商家id的列表 1,2,3,4,5
     */
    public function shoppersDel(){
        $input = $this->input->post();
       // print_r($input);die;
        if(!isset($input['demand_id']) || empty($input['demand_id']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }

        if(!isset($input['order_ids']) || empty($input['order_ids']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }

        $params['dmid'] = $input['demand_id'];
        $params['shopper_user_ids'] = $input['order_ids'];
         
        //移除操作
        $this->load->model('demand/demand_contract_model','demand');
        $result = $this->demand->removeShopperDel($params);
        if($result==1){
            //添加移除商家日志
            $this->load->model('demand_order_log_model');
             $this->demand_order_log_model->demandlog($params['dmid'],0,0,0,'移除商家','移除未审核需求策划师下的商家');
            echo json_encode(array('result' => 'succ','info' => '移除成功'));exit;
        }else{
             echo json_encode(array('result' => 'succ','info' => '移除失败'));exit;
        }
    }

    
    /*
     * 移除未审核需求策划师下的商家
     * method post
     * param
     * demand_id 需求id
     * order_ids 要移除商家id的列表 1,2,3,4,5
     * wedtype 录入类型
     */
    public function wedmasterDel(){
        $input = $this->input->post();
       // print_r($input);die;
        if(!isset($input['demand_id']) || empty($input['demand_id']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }

        if(!isset($input['order_ids']) || empty($input['order_ids']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }

        $params['dmid'] = $input['demand_id'];
        $params['shopper_user_ids'] = $input['order_ids'];
         
        if($input['wedtype'] == 1){//一站式
            $this->load->model('demand/demand_contract_model','demand');
           $res = $this->demand->checkDemandType($params);
           if($res==1){
              echo json_encode(array('result' => 'fail','info' => '该需求的商家信息不能删除'));exit;
           }
        }

        if($input['wedtype']==2){   //单项式
           $this->load->model('demand/demand_contract_model','demand');
           $res = $this->demand->checkDemandTypes($params);
           if($res == 1){
              echo json_encode(array('result' => 'fail','info' => '该需求的商家信息不能删除'));exit;
           }
        }

        //移除操作
        $this->load->model('demand/demand_contract_model','demand');
        $result = $this->demand->removeDemandShopper($params);
        if($result==1){
            //添加移除商家日志
            $this->load->model('demand_order_log_model');
             $this->demand_order_log_model->demandlog($params['dmid'],0,0,0,'移除商家','移除未审核需求策划师下的商家');
            echo json_encode(array('result' => 'succ','info' => '移除成功'));exit;
        }else{
             echo json_encode(array('result' => 'succ','info' => '移除失败'));exit;
        }
    }

    //获取一站式详情页
    public function examine_demand(){
			$id = $this->input->get("id") ? $this->input->get("id") : 0;
			$this->_data['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
			$this->_data['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
			$order_status = $this->input->get('order_status');
			$be_status = $this->input->get('be_status');
			$order_status = isset($order_status) ? $order_status : '';
			$be_status = isset($be_status) ? $be_status : '';
			$this->_data['be_status'] = $be_status;
			$this->_data['order_status'] = $order_status;
			$config = $this->_data["config"];
//			$ewapi_url = $config["ew_domain"]."erp/demand/demand-detail?id=".$id."&order_status=".$order_status;
//			$demand_list = $this->curl->get($ewapi_url);
//			$demand = json_decode($demand_list, TRUE);
            $demand = $this->demands->demand_detail(array('id' => $id));
			$question_arr = array();
			if(isset($demand['question']) && !empty($demand['question'])) {
				foreach ($demand["question"] as $key => $ques) {
					foreach ($ques as $k => $v) {
						$question_arr[$key][$v["alias"]] = $v;
					}
				}
			}

            //通过shopper_alias然后查询出alias_code
            $qa_info = $this->ew_conn->select('id as alias_code')->where('option_alias',$demand['base']['shopper_alias'])->limit(1)->get('options')->row_array();
            $this->_data['qa_info'] = $qa_info;

			//客户标签
			$demand["base"]["cli_tag"] = isset($demand["base"]["cli_tag"]) ? str_replace("||", ",", $demand["base"]["cli_tag"]) : "";
			//新人顾问
			$couselor_uid = isset($demand['base']['counselor_uid']) ? $demand['base']['counselor_uid'] : '';
			$consultant_info = $this->erp_conn->select("id, username")->where('id',$couselor_uid)->get(Sys_user_model::TBL)->row_array();
			$demand['base']['consultant_name'] = !empty($consultant_info['username']) ? $consultant_info['username'] : '';
			$demand['base']['consultant_id'] = !empty($consultant_info['id']) ? $consultant_info['id'] : '';

			$this->_data["base"] = $demand["base"];
			$this->_data["wedplanners"] = isset($demand["question"]["wedplanners"]) ? $demand["question"]["wedplanners"] : array();
			$this->_data["wedmaster"] = isset($question_arr["wedmaster"]) ? $question_arr["wedmaster"] : array();
			$this->_data["makeup"] = isset($question_arr["makeup"]) ? $question_arr["makeup"] : array();
			$this->_data["wedphotoer"] = isset($question_arr["wedphotoer"]) ? $question_arr["wedphotoer"] : array();
			$this->_data["wedvideo"] = isset($question_arr["wedvideo"]) ? $question_arr["wedvideo"] : array();
			$this->_data["sitelayout"] = isset($question_arr["sitelayout"]) ? $question_arr["sitelayout"] : array();


			//新人顾问
			$this->_data["consultant"] = $this->erp_conn->select("id, username")->get(Sys_user_model::TBL)->result_array();
			//客户来源
			$auth_info = $this->func->getInfoByName("客户来源");
			$this->_data["cli_source"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
			//民族
			$this->_data["nation"] = $this->erp_conn->select("id, nation")->get("ew_erp_nation")->result_array();
			//获知渠道
			$auth_info = $this->func->getInfoByName("获知渠道");
			$this->_data["channel"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
			//支付方式
			$sql = "SELECT `id`,`setting_id`, `name`,`enable`,`order` FROM (`ew_erp_sys_basesetting`) WHERE `setting_id` = 176  and `order` = 22 and `enable`= 1";
			$arr = $this->erp_conn->query($sql);
			$this->_data['list']= $arr->result_array();
			
			$mode = $this->input->get("wedtype");

			$this->_data["mode"] = $mode;

			//在线签约
			$contract = $this->contract->getContract(array('demand_id' => $id));
			if(isset($contract[0])){
				$this->_data['contract'] = $contract[0];
			}else{
				$this->_data['contract'] = array(
							'contract_code'=>'',
							'money'=>'',
							'wed_date'=>($this->_data['base']['wed_date_sure']==1)?$this->_data['base']['wed_date']:'',
							'wed_location'=>!empty($this->_data['base']['wed_location'])?$this->_data['base']['wed_location']:'',
							'wed_place'=>!empty($this->_data['base']['wed_place'])?$this->_data['base']['wed_place']:'',
							'comment'=>'',
						);
			}
			//获取已中标的商家
			$bingoShopper = $this->contract->getBingoShopper(array('demand_id' => $id));
			if(isset($bingoShopper[0])){
				$this->_data['bingoShopper'] = $bingoShopper[0];
			}else{
				$this->_data['bingoShopper'] = array(
						'order_id' => '',
						'nickname' => '',
						'phone' => ''
					);
			}
			
			//获取网站色系
			$base_color = $this->func->getInfoByName("色系类型");
			if(! empty($base_color)){
				$this->_data["color"] =  $this->baseset->getInfosBySetting_id($base_color["id"], "id, name");
			}
			 //获取婚礼形容词
			$base_adj = $this->func->getInfoByName("婚礼形容词");
			$this->_data["adj"] =  $this->baseset->getInfosBySetting_id($base_adj["id"], "id, name");
			//商家信息列表
			$this->_data["serves"] = "1435";
			//print_r($this->_data);die;
            $this->load->view('trade/examine_demand_view',$this->_data);

    }
    //获取单项式详情页
    public function examine_individual(){
			$id = $this->input->get("id") ? $this->input->get("id") : 0;
			$this->_data['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
			$this->_data['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
			$order_status = $this->input->get('order_status');
			$be_status = $this->input->get('be_status');
			$order_status = isset($order_status) ? $order_status : '';
			$be_status = isset($be_status) ? $be_status : '';
			$this->_data['be_status'] = $be_status;
			$this->_data['order_status'] = $order_status;
			$config = $this->_data["config"];
            $demand = $this->demands->demand_detail(array('id' => $id));
			$question_arr = array();
			if(isset($demand['question']) && !empty($demand['question'])) {
				foreach ($demand["question"] as $key => $ques) {
					foreach ($ques as $k => $v) {
						$question_arr[$key][$v["alias"]] = $v;
					}
				}
			}

            //通过shopper_alias然后查询出alias_code
            $qa_info = $this->ew_conn->select('id as alias_code')->where('option_alias',$demand['base']['shopper_alias'])->limit(1)->get('options')->row_array();
            $this->_data['qa_info'] = $qa_info;

			//客户标签
			$demand["base"]["cli_tag"] = isset($demand["base"]["cli_tag"]) ? str_replace("||", ",", $demand["base"]["cli_tag"]) : "";
			//新人顾问
			$couselor_uid = isset($demand['base']['counselor_uid']) ? $demand['base']['counselor_uid'] : '';

			$consultant_info = $this->erp_conn->select("id, username")->where('id',$couselor_uid)->get(Sys_user_model::TBL)->row_array();
			$demand['base']['consultant_name'] = !empty($consultant_info['username']) ? $consultant_info['username'] : '';
			$demand['base']['consultant_id'] = !empty($consultant_info['id']) ? $consultant_info['id'] : '';

			$this->_data["base"] = $demand["base"];
			$this->_data["wedplanners"] = isset($demand["question"]["wedplanners"]) ? $demand["question"]["wedplanners"] : array();
			$this->_data["wedmaster"] = isset($question_arr["wedmaster"]) ? $question_arr["wedmaster"] : array();
			$this->_data["makeup"] = isset($question_arr["makeup"]) ? $question_arr["makeup"] : array();
			$this->_data["wedphotoer"] = isset($question_arr["wedphotoer"]) ? $question_arr["wedphotoer"] : array();
			$this->_data["wedvideo"] = isset($question_arr["wedvideo"]) ? $question_arr["wedvideo"] : array();
			$this->_data["sitelayout"] = isset($question_arr["sitelayout"]) ? $question_arr["sitelayout"] : array();


			//新人顾问
			$this->_data["consultant"] = $this->erp_conn->select("id, username")->get(Sys_user_model::TBL)->result_array();
			//客户来源
			$auth_info = $this->func->getInfoByName("客户来源");
			$this->_data["cli_source"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
			//民族
			$this->_data["nation"] = $this->erp_conn->select("id, nation")->get("ew_erp_nation")->result_array();
			//获知渠道
			$auth_info = $this->func->getInfoByName("获知渠道");
			$this->_data["channel"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
			//支付方式
			$sql = "SELECT `id`,`setting_id`, `name`,`enable`,`order` FROM (`ew_erp_sys_basesetting`) WHERE `setting_id` = 176  and `order` = 22 and `enable`= 1";
			$arr = $this->erp_conn->query($sql);
			$this->_data['list']= $arr->result_array();
			
			$mode = $this->input->get("wedtype");
			$this->_data["mode"] = $mode;
			//在线签约
			$contract = $this->contract->getContract(array('demand_id' => $id));
			if(isset($contract[0])){
				$this->_data['contract'] = $contract[0];
			}else{
				$this->_data['contract'] = array(
							'contract_code'=>'',
							'money'=>'',
							'wed_date'=>($this->_data['base']['wed_date_sure']==1)?$this->_data['base']['wed_date']:'',
							'wed_location'=>!empty($this->_data['base']['wed_location'])?$this->_data['base']['wed_location']:'',
							'wed_place'=>!empty($this->_data['base']['wed_place'])?$this->_data['base']['wed_place']:'',
							'comment'=>'',
						);
			}
			//获取已中标的商家
			$bingoShopper = $this->contract->getBingoShopper(array('demand_id' => $id));
			if(isset($bingoShopper[0])){
				$this->_data['bingoShopper'] = $bingoShopper[0];
			}else{
				$this->_data['bingoShopper'] = array(
						'order_id' => '',
						'nickname' => '',
						'phone' => ''
					);
			}
            //获取网站色系
			$base_color = $this->func->getInfoByName("色系类型");
			if(! empty($base_color)){
				$this->_data["color"] =  $this->baseset->getInfosBySetting_id($base_color["id"], "id, name");
			}
			 //获取婚礼形容词
			$base_adj = $this->func->getInfoByName("婚礼形容词");
			$this->_data["adj"] =  $this->baseset->getInfosBySetting_id($base_adj["id"], "id, name");
            //商家信息列表
            if(!empty($infos['sitelayout'])){
                $data[] = "1427";
            }
            if(!empty($this->_data['wedmaster'])){
                $data[] = "1424";
            }
            if(!empty($this->_data['makeup'])){
                $data[] = "1425";
            }
            if(!empty($this->_data['wedphotoer'])){
                $data[] = "1423";
            }
            if(!empty($this->_data['wedvideo'])){
                $data[] = "1426";
            }
            if(!empty($data)){
                $this->_data['serves'] = implode(',',$data);
            }else{

                $this->_data['serves']="";
            }

            $this->load->view('trade/examine_individual_view', $this->_data);
    }
	 //需求对应商家列表
    public function shoper_review()
    {
        $inputs = $this->input->get();
        $id = $this->input->get('id');
        $nickname= $this->input->get('nickname');// 昵称
        $phone = $this->input->get('phone');// 手机号
        $studio_name = $this->input->get('studio_name');//商铺名称
        $arr = "SELECT ew_user_shopers.uid,ew_demand_content.id,ew_demand_order.order_step_end,ew_demand_order.shopper_alias,ew_demand_order.id as orderid,ew_user_shopers.address,ew_demand_order.recommend_letter,ew_demand_order.status,ew_users.nickname,ew_demand_order.time_21,ew_demand_order.time_46,ew_users.phone,ew_user_shopers.studio_name from ew_user_shopers join ew_users on ew_user_shopers.uid = ew_users.uid join ew_demand_order on ew_demand_order.shopper_user_id = ew_users.uid join ew_demand_content on ew_demand_content.id = ew_demand_order.content_id where ew_demand_content.id= ".$id."";
        $lista=$this->ew_conn->query($arr);
        //echo $arr;die;
        $list = $lista->result_array();    
        $infos = array();
        foreach ($list  as $key => $value) {
            $infos[$key]['id']               = $value['orderid']; //用户商家表id
            $infos[$key]['uid']              = $value['uid']; //用户uid
            $infos[$key]['shopper_alias']    = $value['shopper_alias']; //类型别名
            $infos[$key]['nickname']         = $value['nickname']; //名称
            $infos[$key]['address']          = $value['address']; //地址
            $infos[$key]['phone']             = $value['phone']; //手机
            $infos[$key]['studio_name']       = $value['studio_name']; //商铺名称
            
        }
        $info = array(
            'rows' => $infos
        );
        return success($info);  
    }


    /**
     * 一站式修改
     * @return mixed
     */
    public function demand_update(){
        //接收参数
        $inputs = $this->input->post();

        $ret = $this->demands->demand_update_exe($inputs);

        if($ret['result'] == 'succ')
        {
            return success("修改成功");
        }
        else{
            return failure($ret['info']);
        }

    }
    /**
     * 单项修改
     * @return mixed
     */
    public function individual_update(){
        //接收参数
        $inputs = $this->input->post();

        $ret = $this->demands->individual_update_exe($inputs);

        if($ret['result'] == 'succ')
        {
            return success("修改成功");
        }
        else{
            return failure($ret['info']);
        }
    }

}
