<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Perfect extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper('array');
        $this->load->model('sys_trademarksetting_model', 'trademark');
		$this->load->model('sys_basesetting_model','baseset');
        $this->load->model('sys_user_model', 'user');
		$this->load->model("customer/record_model",'record');
        $this->load->model('ew/demand_content_model','content');
    }

	//完善需求页
    public function index()
	{
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
        $this->load->view('trade/perfect_view',$infos);
    }

    /**
     * 获取待完善需求的列表
     */
    public function getlist()
    {
		//分页
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize");
		$offset = ($page-1)*$pagesize;
		$add_from = $this->input->get("add_from");
		
		$add_to = $this->input->get("add_to");

		$where ='';
        $where.="1=1 ";
		//时间
        if(!empty($add_from)&&!empty($add_to)){//出方案时间都不为空
            $where.=" and date(create_time) >="."'$add_from'"." and date(create_time) <="."'$add_to'";
        }
        if(!empty($add_from)&&empty($add_to)){//出方案时间1不为空
            $where.=" and date(create_time) >="."'$add_from'";
        }
        if(empty($add_from)&&!empty($add_to)){//出方案时间2不为空
            $where.=" and date(create_time) <="."'$add_to'";
        }
       //echo $where;
		$arr = "select id,json_content,create_time,update_time from ew_demand_draft where ".$where." order by id desc limit ".$offset.",".$pagesize."";
		//echo $this->erp_conn->last_query();
		$lista=$this->erp_conn->query($arr);
        $list = $lista->result_array();
		//获取条数		
        $count = "select count(*) from  ew_demand_draft where ".$where;
        $totala=$this->erp_conn->query($count);
        $totalaa = $totala->result_array(); 
        $total = $totalaa[0]['count(*)'];	
		$infos = array();
		foreach ($list  as $key => $value) {
			$infos[$key]['id'] = $value['id']; 
			$infos[$key]['create_time'] = $value['create_time']; 
			$infos[$key]['json_content'] = json_decode($value['json_content'],true);
			$infos[$key]['status'] = isset($infos[$key]['json_content']['base']['status'])?($infos[$key]['json_content']['base']['status']):"";
			//需求来源方式 (1: 招投标, 2: 指定商家)
			$infos[$key]['mode'] = isset($infos[$key]['json_content']['base']['mode'])?($infos[$key]['json_content']['base']['mode']):"";
			//新人顾问用户ID
			$infos[$key]['counselor_uid'] = isset($infos[$key]['json_content']['base']['counselor_uid'])?($infos[$key]['json_content']['base']['counselor_uid']):"";
			//客户姓名
			$infos[$key]['cli_name'] = isset($infos[$key]['json_content']['base']['cli_name'])?($infos[$key]['json_content']['base']['cli_name']):"";
			//客户来源
			$infos[$key]['cli_source'] = isset($infos[$key]['json_content']['base']['cli_source'])?($infos[$key]['json_content']['base']['cli_source']):"";
			//获知渠道
			$infos[$key]['channel'] = isset($infos[$key]['json_content']['base']['channel'])?($infos[$key]['json_content']['base']['channel']):"";
			//手机号
			$infos[$key]['cli_mobile'] = isset($infos[$key]['json_content']['base']['cli_mobile'])?($infos[$key]['json_content']['base']['cli_mobile']):"";
			//婚礼日期
			$infos[$key]['wed_date'] = isset($infos[$key]['json_content']['wed_info']['wed_date'])?($infos[$key]['json_content']['wed_info']['wed_date']):"";
			//需求类型
			$infos[$key]['type'] = isset($infos[$key]['json_content']['wed_info']['type'])?($infos[$key]['json_content']['wed_info']['type']):"";
			//预算金额
			$infos[$key]['budget'] = isset($infos[$key]['json_content']['wed_info']['budget'])?($infos[$key]['json_content']['wed_info']['budget']):"";
			
		}
		$info = array(
            'total' => $total,
            'rows' => $infos
        );
		return success($info);
		
    }
  
	  //获取一站式详情页的数据
	public function perfect_demand_view()
	{ 	
		$infos = array();
		//根据需求id获取详情页的数据
		$id = $this->input->get("id") ? $this->input->get("id") : 0;
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;

		$arr = $this->erp_conn->where('id',$id)->get("ew_demand_draft");
		$list = $arr->result_array();
		
		foreach ($list  as $key => $value) {
			$infos['base']['id'] = $value['id']; 
			$infos[$key]['json_content'] = json_decode($value['json_content'],true);
			$infos['base']['status'] = isset($infos[$key]['json_content']['base']['status'])?($infos[$key]['json_content']['base']['status']):"";
			//需求来源方式 (1: 招投标, 2: 指定商家)
			$infos['base']['mode'] = isset($infos[$key]['json_content']['base']['mode'])?($infos[$key]['json_content']['base']['mode']):"";
			//新人顾问用户ID
			$infos['base']['counselor_uid'] = isset($infos[$key]['json_content']['base']['counselor_uid'])?($infos[$key]['json_content']['base']['counselor_uid']):"";
			//客户姓名
			$infos['base']['cli_name'] = isset($infos[$key]['json_content']['base']['cli_name'])?($infos[$key]['json_content']['base']['cli_name']):"";
			//客户来源
			$infos['base']['cli_source'] = isset($infos[$key]['json_content']['base']['cli_source'])?($infos[$key]['json_content']['base']['cli_source']):"";
			//获知渠道
			$infos['base']['channel'] = isset($infos[$key]['json_content']['base']['channel'])?($infos[$key]['json_content']['base']['channel']):"";
			//手机号
			$infos['base']['cli_mobile'] = isset($infos[$key]['json_content']['base']['cli_mobile'])?($infos[$key]['json_content']['base']['cli_mobile']):"";
			//性别
			$infos['base']['cli_gender'] = isset($infos[$key]['json_content']['base']['cli_gender'])?($infos[$key]['json_content']['base']['cli_gender']):"";
			
			
			$infos['base']['cli_birth'] = isset($infos[$key]['json_content']['base']['cli_birth'])?($infos[$key]['json_content']['base']['cli_birth']):"";
			
			
			$infos['base']['cli_edu'] = isset($infos[$key]['json_content']['base']['cli_edu'])?($infos[$key]['json_content']['base']['cli_edu']):"";
			
			
			$infos['base']['cli_tel'] = isset($infos[$key]['json_content']['base']['cli_tel'])?($infos[$key]['json_content']['base']['cli_tel']):"";
			
			
			$infos['base']['cli_mobile'] = isset($infos[$key]['json_content']['base']['cli_mobile'])?($infos[$key]['json_content']['base']['cli_mobile']):"";
			
			
			$infos['base']['cli_nation'] = isset($infos[$key]['json_content']['base']['cli_nation'])?($infos[$key]['json_content']['base']['cli_nation']):"";
			
			
			$infos['base']['cli_weixin'] = isset($infos[$key]['json_content']['base']['cli_weixin'])?($infos[$key]['json_content']['base']['cli_weixin']):"";
			
			
			$infos['base']['cli_qq'] = isset($infos[$key]['json_content']['base']['cli_qq'])?($infos[$key]['json_content']['base']['cli_qq']):"";
			
			
			$infos['base']['cli_weibo'] = isset($infos[$key]['json_content']['base']['cli_weibo'])?($infos[$key]['json_content']['base']['cli_weibo']):"";
			
			
			$infos['base']['cli_postcode'] = isset($infos[$key]['json_content']['base']['cli_postcode'])?($infos[$key]['json_content']['base']['cli_postcode']):"";
			
			$infos['base']['cli_email'] = isset($infos[$key]['json_content']['base']['cli_email'])?($infos[$key]['json_content']['base']['cli_email']):"";
			
			$infos['base']['cli_othercontect'] = isset($infos[$key]['json_content']['base']['cli_othercontect'])?($infos[$key]['json_content']['base']['cli_othercontect']):"";
			
			$infos['base']['cli_location'] = isset($infos[$key]['json_content']['base']['cli_location'])?($infos[$key]['json_content']['base']['cli_location']):"";
			
			$infos['base']['cli_address'] = isset($infos[$key]['json_content']['base']['cli_address'])?($infos[$key]['json_content']['base']['cli_address']):"";
			
			$infos['base']['cli_hope_contect_time'] = isset($infos[$key]['json_content']['base']['cli_hope_contect_time'])?($infos[$key]['json_content']['base']['cli_hope_contect_time']):"";
			$infos['base']['cli_hope_contect_way'] = isset($infos[$key]['json_content']['base']['cli_hope_contect_way'])?($infos[$key]['json_content']['base']['cli_hope_contect_way']):"";
			
			$infos['base']['cli_tag'] = isset($infos[$key]['json_content']['base']['cli_tag'])?($infos[$key]['json_content']['base']['cli_tag']):"";
			$infos["base"]["cli_tag"] = isset($infos["base"]["cli_tag"]) ? str_replace("||", ",", $infos["base"]["cli_tag"]) : "";
		
			$infos['base']['comment'] = isset($infos[$key]['json_content']['base']['comment'])?($infos[$key]['json_content']['base']['comment']):"";
			
			//婚宴类型
			$infos['base']['wed_party_type'] = isset($infos[$key]['json_content']['wed_info']['wed_party_type'])?($infos[$key]['json_content']['wed_info']['wed_party_type']):"";
			$infos['base']['wed_location'] = isset($infos[$key]['json_content']['wed_info']['wed_location'])?($infos[$key]['json_content']['wed_info']['wed_location']):"";
			$infos['base']['wed_place'] = isset($infos[$key]['json_content']['wed_info']['wed_place'])?($infos[$key]['json_content']['wed_info']['wed_place']):"";
			$infos['base']['people_num'] = isset($infos[$key]['json_content']['wed_info']['people_num'])?($infos[$key]['json_content']['wed_info']['people_num']):"";
			$infos['base']['wed_date_sure'] = isset($infos[$key]['json_content']['wed_info']['wed_date_sure'])?($infos[$key]['json_content']['wed_info']['wed_date_sure']):"";
			
			//婚礼日期
			$infos['base']['wed_date'] = isset($infos[$key]['json_content']['wed_info']['wed_date'])?($infos[$key]['json_content']['wed_info']['wed_date']):"";
			//需求类型
			$infos['base']['type'] = isset($infos[$key]['json_content']['wed_info']['type'])?($infos[$key]['json_content']['wed_info']['type']):"";
			//预算金额
			$infos['base']['budget'] = isset($infos[$key]['json_content']['wed_info']['budget'])?($infos[$key]['json_content']['wed_info']['budget']):"";
			 
		}
		//商家信息列表
		$infos["serves"] = "1435";
		$infos['wedplanners'] = $infos[0]['json_content']['wedplanners'];
		//民族
		$infos["nation"] = $this->erp_conn->select("id, nation")->get("ew_erp_nation")->result_array();
		//新人顾问
		$infos["consultant"] = $this->erp_conn->select("id, username")->get(Sys_user_model::TBL)->result_array();
		//客户来源
		$auth_info = $this->func->getInfoByName("客户来源");
		$infos["cli_source"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
		//获知渠道
		$auth_info = $this->func->getInfoByName("获知渠道");
		$infos["channel"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
		//获取网站色系
        $base_color = $this->func->getInfoByName("色系类型");
        if(! empty($base_color)){
            $infos["color"] =  $this->baseset->getInfosBySetting_id($base_color["id"], "id, name");
        }
         //获取婚礼形容词
        $base_adj = $this->func->getInfoByName("婚礼形容词");
        $infos["adj"] =  $this->baseset->getInfosBySetting_id($base_adj["id"], "id, name");
		//print_r($infos);die;
		$this->load->view('trade/perfect_demand_view',$infos);
		
	}
	//获取单项式详情页的数据
	public function perfect_individual_view()
	{ 
	    $infos = array();
		$id = $this->input->get("id") ? $this->input->get("id") : 0;
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
		$arr = $this->erp_conn->where('id',$id)->get("ew_demand_draft");
		$list = $arr->result_array();
		
		foreach ($list  as $key => $value) {
			$infos['base']['id'] = $value['id']; 
			$infos[$key]['json_content'] = json_decode($value['json_content'],true);
			$infos['base']['status'] = isset($infos[$key]['json_content']['base']['status'])?($infos[$key]['json_content']['base']['status']):"";
			//需求来源方式 (1: 招投标, 2: 指定商家)
			$infos['base']['mode'] = isset($infos[$key]['json_content']['base']['mode'])?($infos[$key]['json_content']['base']['mode']):"";
			//新人顾问用户ID
			$infos['base']['counselor_uid'] = isset($infos[$key]['json_content']['base']['counselor_uid'])?($infos[$key]['json_content']['base']['counselor_uid']):"";
			//客户姓名
			$infos['base']['cli_name'] = isset($infos[$key]['json_content']['base']['cli_name'])?($infos[$key]['json_content']['base']['cli_name']):"";
			//客户来源
			$infos['base']['cli_source'] = isset($infos[$key]['json_content']['base']['cli_source'])?($infos[$key]['json_content']['base']['cli_source']):"";
			//获知渠道
			$infos['base']['channel'] = isset($infos[$key]['json_content']['base']['channel'])?($infos[$key]['json_content']['base']['channel']):"";
			//手机号
			$infos['base']['cli_mobile'] = isset($infos[$key]['json_content']['base']['cli_mobile'])?($infos[$key]['json_content']['base']['cli_mobile']):"";
			//性别
			$infos['base']['cli_gender'] = isset($infos[$key]['json_content']['base']['cli_gender'])?($infos[$key]['json_content']['base']['cli_gender']):"";
			
			
			$infos['base']['cli_birth'] = isset($infos[$key]['json_content']['base']['cli_birth'])?($infos[$key]['json_content']['base']['cli_birth']):"";
			
			
			$infos['base']['cli_edu'] = isset($infos[$key]['json_content']['base']['cli_edu'])?($infos[$key]['json_content']['base']['cli_edu']):"";
			
			
			$infos['base']['cli_tel'] = isset($infos[$key]['json_content']['base']['cli_tel'])?($infos[$key]['json_content']['base']['cli_tel']):"";
			
			
			$infos['base']['cli_mobile'] = isset($infos[$key]['json_content']['base']['cli_mobile'])?($infos[$key]['json_content']['base']['cli_mobile']):"";
			
			
			$infos['base']['cli_nation'] = isset($infos[$key]['json_content']['base']['cli_nation'])?($infos[$key]['json_content']['base']['cli_nation']):"";
			
			
			$infos['base']['cli_weixin'] = isset($infos[$key]['json_content']['base']['cli_weixin'])?($infos[$key]['json_content']['base']['cli_weixin']):"";
			
			
			$infos['base']['cli_qq'] = isset($infos[$key]['json_content']['base']['cli_qq'])?($infos[$key]['json_content']['base']['cli_qq']):"";
			
			
			$infos['base']['cli_weibo'] = isset($infos[$key]['json_content']['base']['cli_weibo'])?($infos[$key]['json_content']['base']['cli_weibo']):"";
			
			
			$infos['base']['cli_postcode'] = isset($infos[$key]['json_content']['base']['cli_postcode'])?($infos[$key]['json_content']['base']['cli_postcode']):"";
			
			$infos['base']['cli_email'] = isset($infos[$key]['json_content']['base']['cli_email'])?($infos[$key]['json_content']['base']['cli_email']):"";
			
			$infos['base']['cli_othercontect'] = isset($infos[$key]['json_content']['base']['cli_othercontect'])?($infos[$key]['json_content']['base']['cli_othercontect']):"";
			
			$infos['base']['cli_location'] = isset($infos[$key]['json_content']['base']['cli_location'])?($infos[$key]['json_content']['base']['cli_location']):"";
			
			$infos['base']['cli_address'] = isset($infos[$key]['json_content']['base']['cli_address'])?($infos[$key]['json_content']['base']['cli_address']):"";
			
			$infos['base']['cli_hope_contect_time'] = isset($infos[$key]['json_content']['base']['cli_hope_contect_time'])?($infos[$key]['json_content']['base']['cli_hope_contect_time']):"";
			$infos['base']['cli_hope_contect_way'] = isset($infos[$key]['json_content']['base']['cli_hope_contect_way'])?($infos[$key]['json_content']['base']['cli_hope_contect_way']):"";
			
			$infos['base']['cli_tag'] = isset($infos[$key]['json_content']['base']['cli_tag'])?($infos[$key]['json_content']['base']['cli_tag']):"";
			$infos["base"]["cli_tag"] = isset($infos["base"]["cli_tag"]) ? str_replace("||", ",", $infos["base"]["cli_tag"]) : "";
		
			$infos['base']['comment'] = isset($infos[$key]['json_content']['base']['comment'])?($infos[$key]['json_content']['base']['comment']):"";
			//婚宴类型
			$infos['base']['wed_party_type'] = isset($infos[$key]['json_content']['wed_info']['wed_party_type'])?($infos[$key]['json_content']['wed_info']['wed_party_type']):"";
			$infos['base']['wed_location'] = isset($infos[$key]['json_content']['wed_info']['wed_location'])?($infos[$key]['json_content']['wed_info']['wed_location']):"";
			$infos['base']['wed_place'] = isset($infos[$key]['json_content']['wed_info']['wed_place'])?($infos[$key]['json_content']['wed_info']['wed_place']):"";
			$infos['base']['people_num'] = isset($infos[$key]['json_content']['wed_info']['people_num'])?($infos[$key]['json_content']['wed_info']['people_num']):"";
			$infos['base']['wed_date_sure'] = isset($infos[$key]['json_content']['wed_info']['wed_date_sure'])?($infos[$key]['json_content']['wed_info']['wed_date_sure']):"";
			
			//婚礼日期
			$infos['base']['wed_date'] = isset($infos[$key]['json_content']['wed_info']['wed_date'])?($infos[$key]['json_content']['wed_info']['wed_date']):"";
			//需求类型
			$infos['base']['type'] = isset($infos[$key]['json_content']['wed_info']['type'])?($infos[$key]['json_content']['wed_info']['type']):"";
			//预算金额
			$infos['base']['budget'] = isset($infos[$key]['json_content']['wed_info']['budget'])?($infos[$key]['json_content']['wed_info']['budget']):"";
			 
		}
		$infos['sitelayout'] = isset($infos[0]['json_content']['sitelayout']) ? $infos[0]['json_content']['sitelayout'] : array();//场地布置 
		$infos['wedmaster'] = isset($infos[0]['json_content']['wedmaster']) ? $infos[0]['json_content']['wedmaster'] : array();//主持人 1424
		$infos['makeup'] = isset($infos[0]['json_content']['makeup']) ? $infos[0]['json_content']['makeup'] : array() ;//婚礼造型 1425
		$infos['wedphotoer'] = isset($infos[0]['json_content']['wedphotoer']) ? $infos[0]['json_content']['wedphotoer'] : array();//婚礼摄影
		$infos['wedvideo'] = isset($infos[0]['json_content']['wedvideo']) ? $infos[0]['json_content']['wedvideo'] : array() ;//婚礼摄像 1426
		//民族
		$infos["nation"] = $this->erp_conn->select("id, nation")->get("ew_erp_nation")->result_array();
		//新人顾问
		$infos["consultant"] = $this->erp_conn->select("id, username")->get(Sys_user_model::TBL)->result_array();
		//客户来源
		$auth_info = $this->func->getInfoByName("客户来源");
		$infos["cli_source"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
		//获知渠道
		$auth_info = $this->func->getInfoByName("获知渠道");
		$infos["channel"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
		 //获取网站色系
        $base_color = $this->func->getInfoByName("色系类型");
        if(! empty($base_color)){
            $infos["color"] =  $this->baseset->getInfosBySetting_id($base_color["id"], "id, name");
        }
		//获取婚礼形容词
        $base_adj = $this->func->getInfoByName("婚礼形容词");
        $infos["adj"] =  $this->baseset->getInfosBySetting_id($base_adj["id"], "id, name");
		//print_r($infos);die;
		//商家信息列表
		if(!empty($infos['sitelayout'])){
			$data[] = "1427";
		}
		if(!empty($infos['wedmaster'])){
			$data[] = "1424";
		}
		if(!empty($infos['makeup'])){
			$data[] = "1425";
		}
		if(!empty($infos['wedphotoer'])){
			$data[] = "1423";
		}
		if(!empty($infos['wedvideo'])){
			$data[] = "1426";
		}
		if(!empty($data)){
		    $infos['serves'] = implode(',',$data);
	    }else{

	    	$infos['serves']="";
	    }
		
		 
		//print_r($infos);die;
	    $this->load->view('trade/perfect_individual_view',$infos);

	}

   //保存一站式待完善的需求的数据
	public function save_stay_demand()
	{
		  
	
		$inputs = $this->input->post();
        //处理接受数组
        $base_arr = json_decode($inputs["baseinfo"], TRUE);
		$demand_id = $base_arr[0]['demand_id'];
        $base = array();
        @array_walk($base_arr, function($item) use(&$base){
            $base = array_merge_recursive($base, $item);
        });
        if((!isset($base['mode']) || empty($base['mode']))&&$inputs['submitype']==1){
            return failure('请选择招投标或是指定商家');
        }       
	
        $wed_info_arr = json_decode($inputs["wedbaseinfo"], TRUE);
        $wed_info = array();
        @array_walk($wed_info_arr, function($item) use(&$wed_info){
            $wed_info = array_merge_recursive($wed_info, $item);
        });
        $wedplanner_arr = json_decode($inputs["onestation"], TRUE);
        $wedplanner = array();
        @array_walk($wedplanner_arr, function($item) use(&$wedplanner){
            $wedplanner = array_merge_recursive($wedplanner, $item);
        });

        //判断手机号是否存在客户档案
        $phone = $base["cli_mobile"];
        if(!empty($phone))
        {
            $record_info = $this->record->getRecordByPhone($phone);
            if(empty($record_info))
            {
                $record_id = $this->record->addRecord($base);
            }
        }
        
        //处理需求数组
        //客户来源
        $cli_source = isset($base["cli_source"]) ? $base["cli_source"] : 0;
//      $source_info = $this->baseset->getInfoById($cli_source);
//      $cli_source_detail = $source_info ? $source_info["name"] : "";
        //获知渠道
        $channel = isset($base["channel"]) ? $base["channel"] : 0;
//      $channel_info = $this->baseset->getInfoById($channel);
//      $channel_detail = $channel_info ? $channel_info["name"] : "";
        //性别
        $sex = isset($base["cli_gender"]) ? $base["cli_gender"] : 0;
        switch ((int)$sex)
        {
            case 1:$cli_gender = "男";break;
            case 2:$cli_gender = "女";break;
            default:$cli_gender = "";break;
        }
        //学历
        $cli_edu_detail = isset($base["cli_edu"]) ? $base["cli_edu"] : 0;
        /*switch ($cli_edu)
        {
            case 1:$cli_edu_detail = "小学";break;
            case 2:$cli_edu_detail = "初中";break;
            case 3:$cli_edu_detail = "高中";break;
            case 4:$cli_edu_detail = "专科";break;
            case 5:$cli_edu_detail = "本科";break;
            case 6:$cli_edu_detail = "硕士";break;
            case 7:$cli_edu_detail = "博士";break;
            case 8:$cli_edu_detail = "博士后";break;
            default:$cli_edu_detail = "";break;
        }*/
        //民族
        $nation = isset($base["cli_nation"]) ? $base["cli_nation"] : 0;
        $nation_info = $this->erp_conn->where("id", $nation)->get('erp_nation')->row_array();
        $cli_nation = $nation_info ? $nation_info["nation"] : "";
        //客户通讯地址
        $cli_country = isset($base["cli_country"]) ? $base["cli_country"] : 0;
        $cli_province = isset($base["cli_province"]) ? $base["cli_province"] : 0;
        $cli_city = isset($base["cli_city"]) ? $base["cli_city"] : 0;
        $cli_location = $cli_country . ',' . $cli_province . ',' . $cli_city;
        //客户标签
        $tag_id_str = isset($base["tag"]) ? $base["tag"] : "";




       // print_r($tag_id_str);die;
        $tag_id_arr = explode(",", $tag_id_str);
       // print_r($tag_id_arr);die;
        $cli_tag_arr = array();
        foreach ($tag_id_arr as $tag_id)
        {
            $tag_info = $this->erp_conn->where("id", $tag_id)->get("erp_client_tag")->row_array();
            if($tag_info)
            {
                $cli_tag_arr[] = $tag_info["tag_name"];
            }
        }
        $cli_tag = (!empty($cli_tag_arr)) ? implode('||', $cli_tag_arr) : "";

       // if($inputs['submitype']==1){
          //  $cli_tag = $cli_tag;

       // }else{
            $cli_tag = $tag_id_str;

       // }
       // print_r($cli_tag);die;
        //婚礼日期
        $wed_date_sure = isset($wed_info["wed_date_sure"]) ? $wed_info["wed_date_sure"] : 0;
        if($wed_date_sure == 1)
        {
            if(!isset($wed_info["wed_date"]) || empty($wed_info["wed_date"])&&$inputs['submitype']==1){
                return failure('请填写婚礼日期');
            }
            $wed_date = $wed_info["wed_date"];
        }else
        {
            $wed_date = isset($wed_info["wed_date_notsure"]) ? $wed_info["wed_date_notsure"] : "";
        }
        //婚礼地点
        $wed_country = isset($wed_info["wed_country"]) ? $wed_info["wed_country"] : 0;
        $wed_province = isset($wed_info["wed_province"]) ? $wed_info["wed_province"] : 0;
        $wed_city = isset($wed_info["wed_city"]) ? $wed_info["wed_city"] : 0;
        $wed_location = $wed_country . ',' . $wed_province . ',' . $wed_city;

        //婚礼色系
        $color = isset($wedplanner["color"]) ? $wedplanner["color"] : array();
        if(!is_array($color))
        {
            $color = array($color);
        }
        $color_detail = implode("||", $color);

        //婚礼形容词
        $ideal = isset($wedplanner["ideal"]) ? $wedplanner["ideal"] : array();
        if(!is_array($ideal))
        {
            $ideal = array($ideal);
        }
        $ideal_detail = implode("||", $ideal);
        //希望重点投入
        $emphasis = isset($wedplanner["emphasis"]) ? $wedplanner["emphasis"] : array();
        if(!is_array($emphasis))
        {
            $emphasis = array($emphasis);
        }
        $emphasis_detail = implode("||", $emphasis);
        //婚礼案例
        $opus = isset($wedplanner["opus"]) ? $wedplanner["opus"] : array();
        if(!is_array($opus))
        {
            $opus = array($opus);
        }
        $opus_detail = implode("||", $opus);
        
        //商家信息id
        $id_arr = $wedplanner["bsnesids"];
        //print_r($id_arr);
        if(empty($id_arr) && $inputs['submitype']==1){
             return failure("请选择商家信息");
        }
            $shoper_ids = array();
            foreach($id_arr as $key => $ids)
            {
                if($base['mode'] == 2 && count($ids) > 1&&$inputs['submitype']==1){
                    return failure('指定商家只能选定一个策划师');
                }
                if(!empty($ids))
                {
                    foreach ($ids as $id)
                    {
                        $shoper_ids[$key][] = array(
                            "id" => $id
                        );
                        $sms_user[] = $id;
                    }
                }
            }


    	       if(empty($shoper_ids)&&$inputs['submitype']==1){
                
                     return failure("请选择商家信息");
                }
                if(empty($shoper_ids)&&$inputs['submitype']==3){
                     $sms_user[] = array(0);
                }
        //期望联系方式
        $way = isset($base["cli_hope_contect_way"]) ? $base["cli_hope_contect_way"] : array();
        if(!is_array($way))
        {
            $way = array($way);
        }
        $cli_way = implode(",", $way);
        $cli_hope_contect_way = rtrim($cli_way, ",");
      
        //提交需求数组
        $entry = array(
            "base" => array(
                "status" => isset($inputs["submitype"]) ? $inputs["submitype"] : 3,
                "mode" => isset($base["mode"]) ? $base["mode"] : 0,
                "counselor_uid" => isset($base["counselor_uid"]) ? $base["counselor_uid"] : 0,
                "cli_name" => isset($base["cli_name"]) ? $base["cli_name"] : "",
                "cli_source" => $cli_source,
                "channel" => $channel,
                "cli_gender" => $cli_gender,
                "cli_birth" => isset($base["cli_birth"]) ? $base["cli_birth"] : "",
                "cli_edu" => $cli_edu_detail,
                "cli_mobile" => isset($base["cli_mobile"]) ? $base["cli_mobile"] : "",
                "cli_tel" => isset($base["cli_tel"]) ? $base["cli_tel"] : "",
                "cli_nation" => $cli_nation,
                "cli_weixin" => isset($base["cli_weixin"]) ? $base["cli_weixin"] : "",
                "cli_qq" => isset($base["cli_qq"]) ? $base["cli_qq"] : "",
                "cli_weibo" => isset($base["cli_weibo"]) ? $base["cli_weibo"] : "",
                "cli_postcode" => isset($base["cli_postcode"]) ? $base["cli_postcode"] : "",
                "cli_email" => isset($base["cli_email"]) ? $base["cli_email"] : "",
                "cli_othercontect" => isset($base["cli_othercontect"]) ? $base["cli_othercontect"] : "",
                "cli_location" => $cli_location,
                "cli_address" => isset($base["cli_address"]) ? $base["cli_address"] : "",
                "cli_hope_contect_time" => isset($base["cli_hope_contect_time"]) ? $base["cli_hope_contect_time"] : "",
                "cli_hope_contect_way" => $cli_hope_contect_way,
                "cli_tag" => $cli_tag,
                "comment" => isset($base["comment"]) ? $base["comment"] : "",
            ),
            "wed_info" => array(
                "type" => 1,
                "wed_date_sure" => $wed_date_sure,
                "wed_date" => $wed_date,
                "wed_location" => $wed_location,
                "wed_place" => isset($wed_info["wed_place"]) ? $wed_info["wed_place"] : "",
                "wed_party_type" => isset($wed_info["wed_party_type"]) ? $wed_info["wed_party_type"] : "",
                "people_num" => isset($wed_info["people_num"]) ? $wed_info["people_num"] : "",
                "budget" => isset($wed_info["budget"]) ? $wed_info["budget"] : "",
            ),
            "wedplanners" => array(
                array(
                    'question' => '关于婚礼，以下哪种描述更符合您的要求？',
                    'word' => '自我描述',
                    'alias' => 'description',
                    'datatype' => 'radio',
                    'answer' => isset($wedplanner["description"]) ? $wedplanner["description"] : "",
                  ),
                array(
                    'question' => '您期待自己的婚礼现场是？',
                    'word' => '风格偏好',
                    'alias' => 'style',
                    'datatype' => 'radio',
                    'answer' => isset($wedplanner["style"]) ? $wedplanner["style"] : "",
                  ),
                array(
                    'question' => '您希望自己的婚礼现场的主色系是？',
                    'word' => '色系偏好',
                    'alias' => 'color',
                    'datatype' => 'radio',
                    'answer' => $color_detail,
                  ),
                array(
                    'question' => '请选择2-5个词描述您理想的婚礼：',
                    'word' => '婚礼形容词',
                    'alias' => 'ideal',
                    'datatype' => 'checkbox',
                    'answer' => $ideal_detail,
                  ),
                array(
                    'question' => '在婚礼筹备时，您希望重点投入的是？',
                    'word' => '婚礼希望重点投入',
                    'alias' => 'emphasis',
                    'datatype' => 'checkbox',
                    'answer' => $emphasis_detail,
                  ),
                array(
                    'question' => '在婚礼过程中，您最看重的是？',
                    'word' => '婚礼过程最看重',
                    'alias' => 'importance',
                    'datatype' => 'radio',
                    'answer' => isset($wedplanner["importance"]) ? $wedplanner["importance"] : "",
                  ),
                array(
                    'question' => '请描述您的喜好，以便策划师更好地了解您并为您提供更满意的婚礼方案：',
                    'word' => '更多描述',
                    'alias' => 'moreinfo',
                    'datatype' => 'textarea',
                    'answer' => isset($wedplanner["moreinfo"]) ? $wedplanner["moreinfo"] : "",
                  ),
                array(
                    'question' => '如果您在易结上看到了喜欢的婚礼案例，请输入链接地址：',
                    'word' => '心仪案例',
                    'alias' => 'opus',
                    'datatype' => 'text',
                    'answer' => $opus_detail,
                  ),
            ),
            "shopper_ids" => $shoper_ids
        );
       //print_R($entry);die;
        if($inputs['submitype']==1){
			//保存
			 //向主站提交需求,返回主站id
			$config = $this->_data["config"];
			//print_r($config);die;
			$ewapi_url = $config["ew_domain"]."erp/demand/insert-one-demand";
			//print_r($ewapi_url);die;
			$result = $this->curl->post($ewapi_url, $entry);
			//print_r($result);die;
			$result_arr = json_decode($result, TRUE);
			//print_r($result_arr);die;
			if(!empty($result_arr)){
				$ew_uid = isset($result_arr['onestop'][0]["uid"]) ? $result_arr['onestop'][0]["uid"] : 0;
			}else{			
				$ew_uid  = "0";
			}

			//发短信
			$msg = "您有一条客户咨询，请您马上登录易结，前往订单管理中查看，48小时内接单有效。";
			if(!empty($sms_user)){
				foreach($sms_user as $k => $v){
					$phone_arr[] = $this->content->getPhoneByUid($v);
				}
			}
			$this->sms->send($phone_arr,$msg);

			//更新客户表的ew_uid
			if(isset($record_id) && $record_id > 0)
			{
				$data["ew_uid"] = $ew_uid;
				$res = $this->erp_conn->where('id', $record_id)->update(Record_model::TBL, $data);
			}
           // echo $result_arr['onestop'][0]['demandID'];
			if(isset($result_arr['onestop'][0]['demandID']))
			{
                
			   //删除之前的那条是数据
			   $rows = $this->erp_conn->where('id',$demand_id)->delete('ew_demand_draft');
			   //echo $this->erp_conn->last_query();die;
				return success('保存成功');
			}else
			{
			   //return failure($result_arr['message']);

			   return failure("保存失败");
			}
		}else{

			$data['json_content'] = json_encode($entry,true);
			//保存草稿
			$data['create_time'] = date("Y:m:d H:i:s");
			$rows = $this->erp_conn->insert('ew_demand_draft', $data);
			if(empty($rows))
			{
				return failure("保存失败");     
			}else{
				//删除之前的那条是数据
				$rows = $this->erp_conn->where('id',$demand_id)->delete('ew_demand_draft');
				//echo $this->erp_conn->last_query();die;
				return success("保存成功");
			}

		}

	}
	//单项式保存待完善的需求
	public function save_individual_demand(){
   
		//接受参数
		$inputs = $this->input->post();
        //处理接受数组
        $base_arr = json_decode($inputs["baseinfo"], TRUE);
		$demand_id = $base_arr[0]['demand_id'];
        $base = array();
        @array_walk($base_arr, function($item) use(&$base){
            $base = array_merge_recursive($base, $item);
        });
        if((!isset($base['mode']) || empty($base['mode']))&&$inputs['submitype']==1){
            return failure('请选择招投标或是制定商家');
        }
        $wed_info_arr = json_decode($inputs["wedbaseinfo"], TRUE);
        $wed_info = array();
        @array_walk($wed_info_arr, function($item) use(&$wed_info){
            $wed_info = array_merge_recursive($wed_info, $item);
        });
        $multwed_arr = json_decode($inputs["multwedneed"], TRUE);
        $multwed = array();
        @array_walk($multwed_arr, function($item) use(&$multwed){
            $multwed = array_merge_recursive($multwed, $item);
        });

        //判断手机号是否存在客户档案
        $phone = $base["cli_mobile"];
        if(!empty($phone))
        {
            $record_info = $this->record->getRecordByPhone($phone);
            if(empty($record_info))
            {
                $record_id = $this->record->addRecord($base);
            }
        }

        //处理需求数组
        //客户来源
        $cli_source = isset($base["cli_source"]) ? $base["cli_source"] : 0;
        //获知渠道
        $channel = isset($base["channel"]) ? $base["channel"] : 0;

        //性别
        $sex = isset($base["cli_gender"]) ? $base["cli_gender"] : 0;
        switch ((int)$sex)
        {
            case 1:$cli_gender = "男";break;
            case 2:$cli_gender = "女";break;
            default:$cli_gender = "";break;
        }
        //学历
        $cli_edu = isset($base["cli_edu"]) ? $base["cli_edu"] : 0;
        switch ($cli_edu)
        {
            case 1:$cli_edu_detail = "小学";break;
            case 2:$cli_edu_detail = "初中";break;
            case 3:$cli_edu_detail = "高中";break;
            case 4:$cli_edu_detail = "专科";break;
            case 5:$cli_edu_detail = "本科";break;
            case 6:$cli_edu_detail = "硕士";break;
            case 7:$cli_edu_detail = "博士";break;
            case 8:$cli_edu_detail = "博士后";break;
            default:$cli_edu_detail = "";break;
        }
        //民族
        $nation = isset($base["cli_nation"]) ? $base["cli_nation"] : 0;
        $nation_info = $this->erp_conn->where("id", $nation)->get('erp_nation')->row_array();
        $cli_nation = $nation_info ? $nation_info["nation"] : "";
        //客户通讯地址
        $cli_country = isset($base["cli_country"]) ? $base["cli_country"] : 0;
        $cli_province =  isset($base["cli_province"]) ? $base["cli_province"] : 0;
        $cli_city = isset($base["cli_city"]) ? $base["cli_city"] : 0;
        $cli_location = $cli_country . ',' . $cli_province . ',' . $cli_city;
        //客户标签
        $tag_id_str = isset($base["cli_tag"]) ? $base["cli_tag"] : "";
        $tag_id_arr = explode(",", $tag_id_str);
        $cli_tag_arr = array();
        foreach ($tag_id_arr as $tag_id)
        {
            $tag_info = $this->erp_conn->where("id", $tag_id)->get("erp_client_tag")->row_array();
            if($tag_info)
            {
                $cli_tag_arr[] = $tag_info["tag_name"];
            }
        }
        $cli_tag = (!empty($cli_tag_arr)) ? implode('||', $cli_tag_arr) : "";
        //if($inputs['submitype']==1){
           // $cli_tag = $cli_tag;

        //}else{
            $cli_tag = $tag_id_str;

       // }
        //婚礼日期
        $wed_date_sure = isset($wed_info["wed_date_sure"]) ? $wed_info["wed_date_sure"] : 0;
        if($wed_date_sure == 1)
        {
            if(!isset($wed_info["wed_date"]) || empty($wed_info["wed_date"])&&$inputs['submitype']==1){
                return failure('请填写婚礼日期');
            }
            $wed_date = $wed_info["wed_date"];
        }else
        {
            $wed_date = isset($wed_info["wed_date_notsure"]) ? $wed_info["wed_date_notsure"] : "";
        }
        //婚礼地点
        $wed_country = isset($wed_info["wed_country"]) ? $wed_info["wed_country"] : 0;
        $wed_province = isset($wed_info["wed_province"]) ? $wed_info["wed_province"] : 0;
        $wed_city = isset($wed_info["wed_city"]) ? $wed_info["wed_city"] : 0;
        $wed_location = $wed_country . ',' . $wed_province . ',' . $wed_city;
        //摄影师服务
        $photoer_service = isset($multwed["wedphotoer_service"]) ? $multwed["wedphotoer_service"] : array();
        if(!is_array($photoer_service))
        {
             $photoer_service = array($photoer_service);
        }
        //摄像师服务
        $video_service = isset($multwed["wedvideo_service"]) ? $multwed["wedvideo_service"] : array();
        if(!is_array($video_service))
        {
             $video_service = array($video_service);
        }

        //婚礼色系
        $color = isset($multwed["color"]) ? $multwed["color"] : array();
        if(!is_array($color))
        {
            $color = array($color);
        }
        $color_detail = implode("||", $color);

        //婚礼形容词
        $ideal = isset($multwed["ideal"]) ? $multwed["ideal"] : array();
        if(!is_array($ideal))
        {
            $ideal = array($ideal);
        }
        $ideal_detail = implode("||", $ideal);
        //主持人性别要求
        $wedmaster_sex = isset($multwed["wedmaster_people"]) ? $multwed["wedmaster_people"] : "";
        $wedmaster_height = isset($multwed["height"]) ? $multwed["height"] : "";
        $wedmaster_people = $wedmaster_sex . '||' . $wedmaster_height;
        //商家信息id
        $id_arr = json_decode($inputs['shopperids'],TRUE);
        $multwedneed = json_decode($inputs['multwedneed'],TRUE);
        $wedphotoer_service = array();
        $wedvideo_service = array();
        foreach($multwedneed as $k => $v){
            //摄影师的多项服务选择
            if(isset($v['wedphotoer_service'])){
                $wedphotoer_service[] = $v['wedphotoer_service'];
            }
            //摄像师的多项服务选择
            if(isset($v['wedvideo_service'])){
                $wedvideo_service[] = $v['wedvideo_service'];
            }
        }

        $shoper_ids = array();
        if(isset($id_arr['wedmaster'])){
            if($base['mode'] == 2 && count($id_arr['wedmaster']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个主持人');
            }
            foreach($id_arr['wedmaster'] as $key => $ids)
            {
                $shoper_ids['wedmaster'][] = array('id' => $ids);
                //给发短信拼接用户
                $sms_user[] = $ids;
            }
        }

        if(isset($id_arr['makeup'])){
            if($base['mode'] == 2 && count($id_arr['makeup']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个化妆师');
            }
            foreach($id_arr['makeup'] as $key => $ids)
            {
                $shoper_ids['makeup'][] = array('id' => $ids);
                //给发短信拼接用户
                $sms_user[] = $ids;
            }
        }
        if(isset($id_arr['wedphotoer'])){
            if($base['mode'] == 2 && count($id_arr['wedphotoer']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个摄影师');
            }
            foreach($id_arr['wedphotoer'] as $key => $ids)
            {
                //给发短信拼接用户
                $sms_user[] = $ids;
                $shoper_ids['wedphotoer'][] = array('id' => $ids);
                // foreach($wedphotoer_service as $kws => $vws)
                // {
                //     $shoper_ids['wedphotoer'][] = array('id' => $ids,'service_type' => $vws);
                // }
            }
        }
        if(isset($id_arr['wedvideo'])){
            if($base['mode'] == 2 && count($id_arr['wedvideo']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个摄像师');
            }
            foreach($id_arr['wedvideo'] as $key => $ids)
            {
                //给发短信拼接用户
                $sms_user[] = $ids;
                $shoper_ids['wedvideo'][] = array('id' => $ids);
               
            }
        }
        if(isset($id_arr['sitelayout'])){
            if($base['mode'] == 2 && count($id_arr['sitelayout']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个场地布置');
            }
            foreach($id_arr['sitelayout'] as $key => $ids)
            {
                $shoper_ids['sitelayout'][] = array('id' => $ids);
                //给发短信拼接用户
                $sms_user[] = $ids;
            }
        }
         //期望联系方式
        $way = isset($base["cli_hope_contect_way"]) ? $base["cli_hope_contect_way"] : array();
        if(!is_array($way))
        {
            $way = array($way);
        }
        $cli_way = implode(",", $way);
        $cli_hope_contect_way = rtrim($cli_way, ",");
        $entry = array(
            "base" => array(
                "status" => isset($inputs["submitype"]) ? $inputs["submitype"] : 3,
                "mode" => isset($base["mode"]) ? $base["mode"] : 0,
                "counselor_uid" => isset($base["counselor_uid"]) ? $base["counselor_uid"] : 0,
                "cli_name" => isset($base["cli_name"]) ? $base["cli_name"] : "",
                "cli_source" => $cli_source,
                "channel" => $channel,
                "cli_gender" => $cli_gender,
                "cli_birth" => isset($base["cli_birth"]) ? $base["cli_birth"] : "",
                "cli_edu" => $cli_edu,
                "cli_mobile" => isset($base["cli_mobile"]) ? $base["cli_mobile"] : "",
                "cli_tel" => isset($base["cli_tel"]) ? $base["cli_tel"] : "",
                "cli_nation" => $cli_nation,
                "cli_weixin" => isset($base["cli_weixin"]) ? $base["cli_weixin"] : "",
                "cli_qq" => isset($base["cli_qq"]) ? $base["cli_qq"] : "",
                "cli_weibo" => isset($base["cli_weibo"]) ? $base["cli_weibo"] : "",
                "cli_postcode" => isset($base["cli_postcode"]) ? $base["cli_postcode"] : "",
                "cli_email" => isset($base["cli_email"]) ? $base["cli_email"] : "",
                "cli_othercontect" => isset($base["cli_othercontect"]) ? $base["cli_othercontect"] : "",
                "cli_location" => $cli_location,
                "cli_address" => isset($base["cli_address"]) ? $base["cli_address"] : "",
                "cli_hope_contect_time" => isset($base["cli_hope_contect_time"]) ? $base["cli_hope_contect_time"] : "",
                "cli_tag" => $cli_tag,
                "comment" => isset($base["comment"]) ? $base["comment"] : "",
                "cli_hope_contect_way" => $cli_hope_contect_way,
            ),
            "wed_info" => array(
                "type" => 2,
                "wed_date_sure" => $wed_date_sure,
                "wed_date" => $wed_date,
                "wed_location" => $wed_location,
                "wed_place" => isset($wed_info["wed_place"]) ? $wed_info["wed_place"] : "",
                "wed_party_type" => isset($wed_info["wed_party_type"]) ? $wed_info["wed_party_type"] : "",
            ),
            "shopper_ids" => $shoper_ids
        );
        if(isset($multwed["wedmaster_amount"]))
        {
        	$entry['serves'][] = 'wedmaster';
            $entry["wedmaster"] = array(
                array(
                    'question' => '预定主持人，您的预算是？',
                    'word' => '预算',
                    'alias' => 'amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedmaster_amount"]) ? $multwed["wedmaster_amount"] : "",
                ),
                array(
                    'question' => '对于主持人及其服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    "answer" => isset($multwed["wedmaster_remark"]) ? $multwed["wedmaster_remark"] : "",
                ),
            );
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1){
                $entry["wedmaster"][] = array(
                    'question' => '对于为您服务的婚礼主持人，您的要求是？',
                    'word' => '对人的要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => $wedmaster_people,
                );
            }
        }
        if(isset($multwed["makeup_amount"]))
        {
        	$entry['serves'][] = 'makeup';
            $entry["makeup"] = array(
                array(
                    'question' => '预定化妆师，您的预算是？',
                    'word' => '预算',
                    'alias' => 'amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["makeup_amount"]) ? $multwed["makeup_amount"] : "",
                ),

                array(
                    'question' => '您计划婚礼当天选几套造型？',
                    'word' => '造型需求',
                    'alias' => 'modeling',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["modeling"]) ? $multwed["modeling"] : "",
                ),
                array(
                    'question' => '对于化妆师及其服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    'answer' => isset($multwed["makeup_remark"]) ? $multwed["makeup_remark"] : "",
                ),
                 array(
                    'question' => '对于为您服务的婚礼化妆师，您的要求是？',
                    'word' => '备注要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["makeup_people"]) ? $multwed["makeup_people"] : "",
                ),
            );
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1){
                $entry["makeup"][] = array(
                    'question' => '对于您服务的化妆师，您的要求是？',
                    'word' => '对人的要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["makeup_people"]) ? $multwed["makeup_people"] : "",
                );
            }
        }
        if(isset($multwed["wedphotoer_service"]))
        {
        	$entry['serves'][] = 'wedphotoer';
            $entry["wedphotoer"] = array(
                array(
                    'question' => '对于摄影师及其服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    'answer' => isset($multwed["wedphotoer_remark"]) ? $multwed["wedphotoer_remark"] : "",
                )
            );

            if(in_array("婚纱照拍摄", $photoer_service))
            {
                $entry["wedphotoer"][] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'alias' => 'service',
                    'datatype' => 'checkbox',
                    'service_type' => "hspz",
                    'answer' => "婚纱照拍摄",
                );
                //当选择选择婚纱照拍摄才会有此项
                $entry["wedphotoer"][] = array(
                    'question' => '对于婚纱照拍摄，您的预算是？',
                    'word' => '婚纱照拍摄预算',
                    'alias' => 'hspz_amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["hspz_amount"]) ? $multwed["hspz_amount"] : "",
                );
            }
            if(in_array("婚礼当天跟拍", $photoer_service))
            {
                $entry["wedphotoer"][] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'alias' => 'service',
                    'datatype' => 'checkbox',
                    'service_type' => "hlgp",
                    'answer' => "婚礼当天跟拍",
                );
                //选择婚礼跟拍才会有此项
                $entry["wedphotoer"][] = array(
                    'question' => '对于婚礼当天跟拍，您的预算是？',
                    'word' => '跟拍预算',
                    'alias' => 'hlgp_amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedphotoer_hlgp_amount"]) ? $multwed["wedphotoer_hlgp_amount"] : "",
                );
                //当选择婚礼跟拍才会有此项
                $entry["wedphotoer"][] = array(
                    'question' => '您希望选择哪种跟拍方案？',
                    'word' => '跟拍方案',
                    'alias' => 'hlgp_scheme',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedphotoer_hlgp_scheme"]) ? $multwed["wedphotoer_hlgp_scheme"] : "",
                );
            }
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1)
            {
                $entry["wedphotoer"][] = array(
                    'question' => '对于您服务的摄影师，您的要求是？',
                    'word' => '对人的要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedphotoer_people"]) ? $multwed["wedphotoer_people"] : "",
                );
            }
        }
        if(isset($multwed["wedvideo_service"]))
        {
        	$entry['serves'][] = 'wedvideo';
            $entry["wedvideo"] = array(
                array(
                    'question' => '对于摄像师及其服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    'answer' => isset($multwed["wedvideo_remark"]) ? $multwed["wedvideo_remark"] : "",
                )
            );

            if(in_array("婚礼前的爱情微电影", $video_service))
            {
                $entry["wedvideo"][] = array(
                    'question' => '您需要的摄像服务是？',
                    'word' => '服务选择',
                    'alias' => 'service',
                    'datatype' => 'checkbox',
                    'service_type' => "hspz",
                    'answer' => "婚礼前的爱情微电影",
                );
                //选择爱情微电影才会有此项
                $entry["wedvideo"][] = array(
                    'question' => '对于爱情微电影，您的预算是？',
                    'word' => '微电影预算',
                    'alias' => 'wdy_amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wdy_amount"]) ? $multwed["wdy_amount"] : "",
                );
            }
            if(in_array("婚礼当天跟拍", $video_service))
            {
                $entry["wedvideo"][] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'alias' => 'service',
                    'datatype' => 'checkbox',
                    'service_type' => "hlgp",
                    'answer' => "婚礼当天跟拍",
                );
                //选择婚礼跟拍才会有此项
                $entry["wedvideo"][] = array(
                    'question' => '对于婚礼当天跟拍，您的预算是？',
                    'word' => '跟拍预算',
                    'alias' => 'hlgp_amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedvideo_hlgp_amount"]) ? $multwed["wedvideo_hlgp_amount"] : "",
                );
                //当选择婚礼跟拍才会有此项
                $entry["wedvideo"][] = array(
                    'question' => '您希望选择哪种跟拍方案？',
                    'word' => '跟拍方案',
                    'alias' => 'hlgp_scheme',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedvideo_hlgp_scheme"]) ? $multwed["wedvideo_hlgp_scheme"] : "",
                );
            }
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1)
            {
                $entry["wedvideo"][] = array(
                    'question' => '对于为您服务的摄像师，您的要求是？',
                    'word' => '对人的要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedvideo_people"]) ? $multwed["wedvideo_people"] : "",1
                );
            }
        }
        if(isset($multwed["sitelayout_amount"]))
        {
        	$entry['serves'][] = 'sitelayout';
            $entry["sitelayout"] = array(
                array(
                    'question' => '对于婚礼现场的场地布置，您的预算是？',
                    'word' => '预算',
                    'alias' => 'amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["sitelayout_amount"]) ? $multwed["sitelayout_amount"] : "",
                ),
                array(
                    'question' => '您期待自己的婚礼现场是？',
                    'word' => '风格偏好',
                    'alias' => 'style',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["style"]) ? $multwed["style"] : "",
                ),
                array(
                    'question' => '您希望自己的婚礼现场的主色系是？',
                    'word' => '色系偏好',
                    'alias' => 'color',
                    'datatype' => 'radio',
                    'answer' => $color_detail,
                ),
                array(
                    'question' => '请选择2-5个词描述您理想的婚礼：',
                    'word' => '婚礼形容词',
                    'alias' => 'ideal',
                    'datatype' => 'checkbox',
                    'answer' => $ideal_detail,
                ),
                array(
                    'question' => '对于场地布置及其服务，您是否还有其他的要求或喜好？',
                    'question_appoint' => '对于我的服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    'answer' => isset($multwed["sitelayout_remark"]) ? $multwed["sitelayout_remark"] : "",
                ),
            );
        }

		//print_r($entry);
		if($inputs['submitype']==1){
				//向主站提交需求,返回主站id
				$config = $this->_data["config"];
				$ewapi_url = $config["ew_domain"]."erp/demand/insert-one-demand";
				//print_r($ewapi_url);
				$result = $this->curl->post($ewapi_url, $entry);
				//print_r($result);
				$result_arr = json_decode($result, TRUE);

				//发短信
				$msg = "您有一条客户咨询，请您马上登录易结，前往订单管理中查看，48小时内接单有效。";
				if(!empty($sms_user)){
					foreach($sms_user as $k => $v){
						$phone_arr[] = $this->content->getPhoneByUid($v);
					}
				}
				$this->sms->send($phone_arr,$msg);

				if(isset($result_arr['monomial'][0]['demandID'])){
					//删除之前的那条是数据
					$rows = $this->erp_conn->where('id',$demand_id)->delete('ew_demand_draft');
					return success('保存成功');
				}else
				{
					return failure("保存失败");
				}
		}else{
			   //保存草稿
			    $data['json_content'] = json_encode($entry,true);
				$data['create_time'] = date("Y:m:d H:i:s");
				$rows = $this->erp_conn->insert('ew_demand_draft', $data);
				if(empty($rows)){
					return failure("保存失败");
				}else{
					//删除之前的那条是数据
					$rows = $this->erp_conn->where('id',$demand_id)->delete('ew_demand_draft');
					return success("保存成功");
				}

		}
		


	}
    //需求对应商家列表
    public function shoper_review()
    {
        $inputs = $this->input->get();
        $demand_id = $this->input->get('id'); 
        $arr = $this->erp_conn->where("id",$demand_id)->select('json_content')->from('ew_demand_draft')->get();
        $list = $arr->result_array();
        $json_content= json_decode($list[0]['json_content'],true);
        //print_r($json_content);
		$rows = array();
		//获取商家的分类
		if(!empty($json_content['shopper_ids'])){
			$ids = $json_content['shopper_ids'];
			$new_arr = array();
		    foreach($ids as $key=>$val){
				foreach($val as $k => $v){
					$arr = $this->ew_conn->where('ew_user_shopers.uid',$v['id'])->select('ew_user_shopers.address, ew_user_shopers.uid,ew_users.nickname,ew_users.phone,ew_user_shopers.studio_name')->from('ew_user_shopers')->join('ew_users','ew_user_shopers.uid = ew_users.uid')->get()->result_array();
					$arr[0]['shopper_alias'] = $key;
					$rows[] = $arr[0];
				}
			}
		}
		$info = array('rows' => $rows);
		return success($info);
    }

}
