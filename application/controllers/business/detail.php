<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detail extends App_Controller
{
	public function __construct(){
        parent::__construct();
		$this->load->model('business/business_model', 'business');
		$this->load->model('business/business_extra_model' , 'business_extra');
		$this->load->model("business/common_model",'buscommon');
        $this->load->model('business/business_shop_map_model', 'shopmap');
		$this->load->model('sys_user_model', 'sum');
		$this->load->model('system/roles_model', 'srm');
		$this->load->model('system/department_model', 'sdm');
        $this->load->model('commons/message_model', 'message');
        $this->load->model('business/tools_model', 'tools');
        $this->load->model('commons/message_model', 'message');
        $this->load->helper('functions');// 载入公共函数

        $this->load->helper('array');
    }
	
	/**
	 * 商机详情页显示
	 */
	public function index()
	{

		//tab_value
		$tab_value = $this->input->get("tab_value") ? $this->input->get("tab_value") : 0;
		$this->_data['tab_value'] = $tab_value;
		//商机id
		$bid = $this->input->get("bid") ? $this->input->get("bid") : 0;
		$bus_info = $this->business->findRow(array("id" => $bid));
		if(!$bus_info)
		{
			echo "<script>alert('商机不存在');</script>";die;
		}
		$this->_data['bid'] = $bid;
		
		//是否有权限查看
		$login_user = $this->sum->getInfoById($this->session->userdata("admin_id"));
		$role_id = isset($login_user["role_id"]) ? $login_user["role_id"] : 0;
		$role_info = $this->srm->getInfoById($role_id);
		
		if(!$login_user["satrap"] && $role_info["role_name"] == "婚礼顾问")
		{
			if($this->session->userdata("admin_id") != $bus_info["follower_uid"])
			{
				echo "<script>alert('因为权限不够，无法查看此条商机');</script>";die;
			}
		}
		
		
		//判断商机基本信息是否完善
		$is_perfect = $this->buscommon->is_perfect($bid);
		$this->_data['is_perfect'] = $is_perfect;
//		print_r($is_perfect);die;
		
		
		list($source, $source_explan) = $this->business->getBusinessSource();		
		list($customer, $customer_explan) = $this->business->getCustomerIdentify();
		list($ordertype, $ordertype_explan) = $this->business->getBusinessType();
		list($budget, $budget_explan) = $this->business->getWedBudget();
		list($findtype, $findtype_explan) = $this->business->getFindShopType();
		list($wedtype, $wedtype_explan) = $this->business->getWedType();
		list($status, $status_explan) = $this->business->getBusinessStatus();
		list($tradestatus, $tradestatus_explan) = $this->business->getTradeStatus();

        //除以下这8个商机来源，其他商机来源的商机来源备注不可编辑
        $system_source = array(
            $source['callcenter'], $source['live800'], $source['weibo'],$source['channel_spread'],
            $source['youzan'],$source['other'],$source['mike'],$source['internal_rec']
        );
        $source_note_edit = true;
        if(!in_array($bus_info['source'],$system_source))
        {
            $source_note_edit = false;
        }

        //已分单的商机去掉婚宴酒店这个商机项
        if($status['parted'] == $bus_info['status'])
        {
            unset($ordertype['wed_place']);
        }

        //历史分单数据处理，分配商家要可点
        if($bid <= 3213 && $bus_info['status'] == $status['parted'])
        {
            $this->_data['is_perfect'] = 1;
        }
		
		// 新人顾问
//		$department = $this->sdm->getWedAdviser();
//		$adviser = $this->sum->findAll(array('department' => $department['id']), array(), array());
		
		//添加人信息
		$admin = $this->sum->getInfoById($bus_info["sys_uid"]);

		$this->_data['customer'] = $customer;
		$this->_data['customer_explan'] = $customer_explan;
		$this->_data['source'] = $source;
		$this->_data['source_explan'] = $source_explan;
		$this->_data['ordertype'] = $ordertype;
		$this->_data['ordertype_explan'] = $ordertype_explan;
		$this->_data['budget'] = $budget;
		$this->_data['budget_explan'] = $budget_explan;
		$this->_data['findtype'] = $findtype;
		$this->_data['findtype_explan'] = $findtype_explan;
		$this->_data['wedtype'] = $wedtype;
		$this->_data['wedtype_explan'] = $wedtype_explan;
		$this->_data['status'] = $status;
		$this->_data['status_explan'] = $status_explan;
		$this->_data['tradestatus'] = $tradestatus;
		$this->_data['tradestatus_explan'] = $tradestatus_explan;
//		$this->_data['adviser'] = $adviser;
		$this->_data['admin'] = $admin;
		$this->_data['usertype'] = $this->business->getUserType();
		
		//商家等级
		$grade = $this->buscommon->getShoperGrade();
		$this->_data["grade"] = $grade;
		
		//商家信息
		$bus_extra_info = $this->business_extra->findRow(array("bid" => $bid), "weddate_note, location, wed_place, wed_place_area, wed_type, guest_from, guest_to, desk_from, desk_to, price_from, price_to, budget, budget_note, findtype, findnote, wish_contact, moredesc");
		$bus_extra_info = !empty($bus_extra_info) ? $bus_extra_info : $this->business_extra->prepareData();
		
		//创建时间
		$bus_info["createtime"] = date("Y-m-d H:i:s", $bus_info["createtime"]);
		//婚礼日期
		$bus_info["wed_date"] = $bus_info["wed_date"] ? date("Y-m-d", $bus_info["wed_date"]) : "";
		//交易状态
		$rts = array_flip($tradestatus);
		$bus_info["trade_status_detail"] = isset($rts[$bus_info["trade_status"]]) ? $tradestatus_explan[$rts[$bus_info["trade_status"]]] : '';
		//分单时间（n进4时间）
        if(isset($bus_info["ordertime"]) && $bus_info["ordertime"] != 0){
            $bus_info["ordertime"] = date("Y-m-d H:i:s", $bus_info["ordertime"]);
        }else{
            $bus_info["ordertime"] = "";
        }
        //4进2 时间
        if(isset($bus_info["signletime"]) && $bus_info["signletime"] != 0){
            $bus_info["signletime"] = date("Y-m-d H:i:s", $bus_info["signletime"]);
        }else{
            $bus_info["signletime"] = "";
        }

		//操作时间
		$bus_info["updatetime"] = date("Y-m-d H:i:s", $bus_info["updatetime"]);
		//新人顾问
		$follower = $this->sum->getInfoById($bus_info['follower_uid']);
		$bus_info['follower'] = $follower ? $follower['username'] : "";
        $bus_info['follower_mobile'] = $follower ? $follower['mobile'] : "";
		//运营
		$operator = $this->sum->getInfoById($bus_info['operate_uid']);
		$bus_info['operator'] = $operator ? $operator['username'] : "";
        $bus_info['operator_phone'] = $operator ? $operator['mobile'] : "";

        //商机状态文案
        $status_flip = array_flip($status);
        $bus_info['status_alias'] = $status_flip[$bus_info['status']];
        $bus_info['status_explan'] = $status_explan[$status_flip[$bus_info['status']]];

        //交易状态文案
        $trade_status_flip = array_flip($tradestatus);
        $bus_info['trade_status_alias'] = $bus_info['trade_status']==0 ? "" : $trade_status_flip[$bus_info['trade_status']];
        $bus_info['trade_status_explan'] = $bus_info['trade_status']==0 ? "" : $tradestatus_explan[$trade_status_flip[$bus_info['trade_status']]];

		//建单来源
		$rso = array_flip($source);
		$source_detail = isset($rso[$bus_info['source']]) ? $source_explan[$rso[$bus_info['source']]] : '';
		$bus_info['build_source'] = !empty($bus_info["source_note"]) ? $source_detail.'-'.$bus_info["source_note"] : $source_detail;
		//区分是商机还是分单
		if($bus_info["status"] == $status["parted"])
		{
			$flag = 1; //1为分单
		}else
		{
			$flag = 2; //2为商机
		}
		//商机变为分单状态后 商机列表进入的详情禁止编辑,1可以修改，0禁止修改
		$detailType = $this->input->get("detailType") ? $this->input->get("detailType") : 0;
        $tab_value = $this->input->get("tab_value") ? $this->input->get("tab_value") : 0;
		$detailType = intval($detailType);
        $tab_value = intval($tab_value);
		$allow_modify = 1;
		if($detailType == 1 && $bus_info["status"] == $status["parted"])
		{
			$allow_modify = 0;
		}
		//交易状态大于等于3(已成单，已丢单，无效订单)，详情页不可修改
		if($bus_info["trade_status"] >= $tradestatus['ordered'])
		{
			$allow_modify = 0;
		}

        //如果找商家备注为整型id时，则去查询此商家名称
        if(!empty($bus_extra_info['findnote']) && is_numeric($bus_extra_info['findnote']) && $bus_extra_info['findtype'] == $findtype['people_self'])
        {
            $shop_info = $this->buscommon->shoperInfo(array('uids' => intval($bus_extra_info['findnote'])));
            if (count($shop_info['rows']) > 0 && !empty($shop_info['rows'][0]['nickname']))
            {
                $bus_extra_info['findnote'] = $shop_info['rows'][0]['nickname'];
            }
        }

        // $bus_info['bidstr'] = $this->business->formatBid($bid , strtotime($bus_info['createtime']));
		$this->_data["detailType"] = $detailType;
        $this->_data["tab_value"] = $tab_value;
		$this->_data["allow_modify"] = $allow_modify;
		$this->_data["flag"] = $flag;
		$this->_data["info"] = $bus_info;
		$this->_data["info_extra"] = $bus_extra_info;
        $this->_data['source_note_edit'] = $source_note_edit;
        //目前空着，以后可能要改
		//$sys_users = $this->erp_conn->select("username , mobile")->where("id", $bus_info['operate_uid'])->get('erp_sys_user')->row_array();
        //$this->_data['operate_name'] = isset($sys_users['username']) ? $sys_users['username'] : "";

        $url= $this->config->item('m_domain').'personal/business/'.$bus_info['tradeno'];
        $short_url = get_short_url($url);
		if($short_url['status']=='succ')
        {
         $this->_data['operate_url'] = $short_url['short'];
        }
        //客人推荐酒店短信内容
        $params = array(
            'follower'=>$bus_info['operator'],
            'follower_mobile'=>$bus_info['operator_phone'] //运营的手机号
        );
        $tools['tools_guest'] = $this->message->tools_guest($params);
        if(!empty($bus_info['wed_date'])){
            $wed_date = $bus_info['wed_date'];
        }else{
            $wed_date = $bus_extra_info['weddate_note'];
        }
        //给销售客户信息
        $data =  array(
            'follower'=>$bus_info['operator'],
            'follower_mobile'=>$bus_info['operator_phone'],
            'username'=>$bus_info['username'],
            'mobile'=>$bus_info['mobile'],
            'wed_date'=>$wed_date,
        );
        $tools['tools_sales'] = $this->message->tools_sales($data);
        //挽救短信内容
        $params_save = array(
            'follower'=>$bus_info['follower'],
            'follower_mobile'=>$bus_info['follower_mobile']
        );
        $tools['tools_save'] = $this->message->tools_save($params_save);
        //判断挽救短信的按钮是否变灰
        $tools_info = $this->erp_conn->where('bid',$bid)->where('type',Tools_model::STATUS_SAVE)->get('tools')->result_array();
        if(empty($tools_info)){
            $type_3 = 0;
        }else{
            $type_3 = 1;
        }
        $tools['type_3'] = $type_3;
        $this->_data['tools'] = $tools;     //客人
//     print_R($this->_data);exit;

		$this->load->view('business/detail', $this->_data);
	}
	
	/**
	 * 保存基本信息修改
	 * @return type
	 */
	public function saveBaseinfo()
	{
		$params = $this->input->post();
		
		$bid = isset($params["bid"]) ? $params["bid"] : 0;
		$bid = intval($bid);
        $binfo = $this->business->findRow(array("id" => $bid));
		if(!$binfo)
		{
			return failure("商机不存在");
		}
		
		if(empty($params['source']))
		{
			return failure('请输入商机来源');
		}
		//手机，电话，qq，微信不可同时为空
		if(empty($params['mobile']) && empty($params['tel']) && empty($params['weixin']) && empty($params['qq']))
		{
			return failure('手机号、电话、微信、QQ至少填写一项');
		}

		$this->load->model('business/business_model', 'business');
		list($writer, $writer_explan) = $this->business->getWBusinessWriter();
		list($status, $status_explan) = $this->business->getBusinessStatus();
		// if(!isset($status[$params['status']]))
		// {
		// 	return failure('请选择商机状态');
		// }

		$data['usertype'] = !empty($params['usertype']) ? $params['usertype'] : '';
		$data['mobile'] = $params['mobile'];
        // if($binfo['status'] != $status['parted'])
        // {
        //     $data['status'] = $status[$params['status']];
        // }
		$data['username'] = !empty($params['username']) ? $params['username'] : '';
		$data['userpart'] = !empty($params['userpart']) ? $params['userpart'] : 0;
		$data['tel'] = !empty($params['tel']) ? $params['tel'] : '';
		$data['weixin'] = !empty($params['weixin']) ? $params['weixin'] : '';
		$data['qq'] = !empty($params['qq']) ? $params['qq'] : '';
		$data['other_contact'] = !empty($params['other_contact']) ? $params['other_contact'] : '';
		$data['source_note'] = !empty($params['source_note']) ? $params['source_note'] : '';
		$data['hotel_name'] = !empty($params['hotel_name']) ? $params['hotel_name'] : '';
		// $data['status_note'] = !empty($params['status_note']) ? $params['status_note'] : '';
		//$data['tradeno'] = !empty($params['tradeno']) ? $params['tradeno'] : '';
		$data['sys_usertype'] = $writer['sys_usertype_adviser']; // 录入商机来源
        if($binfo['follower_uid'] <= 0)
        {
            $data['follower_uid'] = $this->session->userdata('admin_id');// 婚礼顾问uid
        }
        if($binfo['sys_uid'] <= 0)
        {
            $data['sys_uid'] = $this->session->userdata('admin_id');// 添加人uid
        }
		//$data['createtime'] = time();

		// 保持数据库
		$result = $this->business->modify(array("id" => $bid), $data);

		return success("保存成功");
	}
	
	
	public function saveUserdemand()
	{
		$params = $this->input->post();
        $binfo = $this->business->findRow(array("id" => $params['bid']));
		if(!$binfo)
		{
			return failure("商机不存在");
		}

		// 载入模型
		$this->load->model('business/business_model', 'business');
		$this->load->model('business/business_extra_model', 'be');
		list($wedtype, $wedtype_explan) = $this->business->getWedType();

		// 处理参数 商机基本信息表
		$baseinfo['ordertype'] = $params['ordertype'];
		// 婚礼日期处理
		if(!empty($params['is_wed_date']))
		{
			$baseinfo['wed_date'] = strtotime($params['wed_date']);
			$extra['weddate_note'] = '';
		}
		else
		{
			$baseinfo['wed_date'] = 0;
			$extra['weddate_note'] = $params['weddate_note'];
		}

		if(!$this->business->modify(array('id' => $params['bid']), $baseinfo))
		{
			return failure('婚礼日期和商机类型保存失败');
		}

		// 婚礼地点处理
		if(isset($params['is_wed_place']))
		{
			if(!empty($params['is_wed_place']))
			{
				$extra['wed_place'] = $params['wed_place'];
				$extra['wed_place_area'] = '';
			}else
			{
				$extra['wed_place'] = "";
				$extra['wed_place_area'] = $params['wed_place_area'];
			}
		}
		else
		{
			$extra['wed_place'] = '';
			$extra['wed_place_area'] = $params['place_area'];
		}

		// 城市
		$extra['bid'] = $params['bid'];
		if(!empty($params['wed_country']) && !empty($params['wed_province']))
		{
			if(!empty($params['wed_city']))
			{
				$extra['location'] = $params['wed_country'].','.$params['wed_province'].','.$params['wed_city'];
			}else
			{
				$extra['location'] = $params['wed_country'].','.$params['wed_province'];
			}
			
		}
		else
		{
			$extra['location'] = '';
		}

		$extra['wed_type'] = !empty($params['wed_type']) ? $params['wed_type'] : $wedtype['type_no'];
		$extra['guest_from'] = $params['guest_from'] ? intval($params['guest_from']) : 0;
		$extra['guest_to'] = $params['guest_to'] ? intval($params['guest_to']) : 0;
//		$extra['desk_from'] = $params['desk_from'] ? intval($params['desk_from']) : 0;
//		$extra['desk_to'] = $params['desk_to'] ? intval($params['desk_to']) : 0;
		$extra['price_from'] = $params['price_from'] ? intval($params['price_from']) : 0;
		$extra['price_to'] = $params['price_to'] ? intval($params['price_to']) : 0;

		$extra['budget'] = $params['budget'] ? $params['budget'] : 0;
        $extra['budget_note'] = $params['budget_note'];
		$extra['findtype'] = $params['findtype'] ? $params['findtype'] : 0;
		$extra['findnote'] = $params['findnote'];
		$extra['wish_contact'] = $params['wish_contact'];
		$extra['moredesc'] = $params['moredesc'];
        $extra['is_test'] = isset($binfo['is_test']) && $binfo['is_test'] == 1 ? 1 : 0;

        //获取商机状态
        list($bstatus , $bstatus_explan) = $this->business->getBusinessStatus();
        //当在分单状态下编辑信息时，把信息同步到主站
        if($binfo['status'] == $bstatus['parted'])
        {
            //获取商家id
            $planner_list = $this->shopmap->getShoperidsBybid($params['bid']);
            $planner_ids = array_flatten($planner_list, "shop_id");
            $resp = $this->business->syncToMaster($params['bid'] , $planner_ids , $extra);
            if($resp['code'] < 0)
            {
                return failure('抱歉，同步信息至主站失败');
            }
        }
        unset($extra['is_test']);
		$result = $this->be->modify(array("bid" => $params['bid']), $extra);

		if(!$result)
		{
			return failure('客户需求保存失败');
		}
		return success("客户需求保存成功");
	}


	/*
	 * 修改商机状态
	 * data['status']:商机状态
	 * data['id']:商机id
	 */
	public function changeBusinessStatus()
	{
		$data = $this->input->post();
		$bussiness_status = list($bstatus , $bstatus_explan) = $this->business->getBusinessStatus();
        $bstatus_alias = array_flip($bstatus);
        if(array_key_exists($data['status'],$bstatus)){
          $status = $bstatus[$data['status']];
        }
		$change_data = array(
				'status' => $status,
				'updatetime' => time(),
			);
		//状态为已建单
        if($data['status'] == $bstatus_alias['7']){
            $bussiness = $this->erp_conn->select("source , usertype , username , userpart , mobile , wed_date , follower_uid,source_note")->where("id", $data['bid'])->get('business')->row_array();
            $bussiness_extra = $this->erp_conn->select("location,wed_place,wed_place_area,budget,findtype,findnote")->where("bid", $data['bid'])->get('business_extra')->row_array();
            $sys_users = $this->erp_conn->select("username , mobile")->where("id", $bussiness['follower_uid'])->get('erp_sys_user')->row_array();
            if(empty($bussiness['source'])){
                return failure('商机来源不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness['usertype'])){
                return failure('客户类型不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness['username'])){
                return failure('客户姓名不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness['userpart'])){
                return failure('客户身份不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness['mobile'])){
                return failure('客户手机不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness['wed_date']) && $bussiness['wed_date']!=0){
                return failure('婚礼日期不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness['source_note'])){
                return failure('商机来源备注不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness_extra['location'])){
                return failure('婚礼地点不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness_extra['wed_place']) && empty($bussiness_extra['wed_place_area'])){
                return failure('婚礼场地不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness_extra['budget'])){
                return failure('婚礼预算不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness_extra['findtype'])){
                return failure('找商家方式不能为空，请保存后再修改商机状态');
            }elseif(empty($bussiness_extra['findnote'])){
                return failure('找商家方式备注不能为空，请保存后再修改商机状态');
            }

            
        }
		$res_change = $this->erp_conn->update("business",$change_data,array('id'=>$data['bid']));
        //print_r($data['status']);
        //print_r($bussiness_status[1]);die;
        if(array_key_exists($data['status'],$bussiness_status[1])){
            $bussiness_status = $bussiness_status[1][$data['status']];
        }
		if($res_change == true){
			//已建单状态给客户发短信
			if($data['status'] == $bstatus_alias['7']){
                //客户类型
                $usertype = $this->business->getUserType();
                if($bussiness['usertype'] == $usertype[1]  || $bussiness['usertype'] == $usertype[5]){
                    //客户类型为 C2  C6
                    $content = $this->message->build_c2();
                    $send = $this->sms->send(array($bussiness['mobile']),$content);//给客户发短信
                }else if($bussiness['usertype'] == $usertype[3] || $bussiness['usertype'] == $usertype[7]){
                    //客户类型为 C4、C7
                    $content = $this->message->build_c4();
                    $send = $this->sms->send(array($bussiness['mobile']),$content);//给客户发短信

                }else if($bussiness['usertype'] == $usertype[4] || $bussiness['usertype'] == $usertype[6]){
                    //客户类型为 C5、C5&C6、
                    $content = $this->message->build_c5();
                    $send = $this->sms->send(array($bussiness['mobile']),$content);//给客户发短信
                }
			}
			return success(array('status'=>$bussiness_status,'msg'=>"修改成功"));
		}else{
            return failure("修改失败");
		}
	}


	/*
	 * 无效商机激活
	 * data['bid']
	 */
	public function activeBusiness()
	{
		$data = $this->input->post();
		$bid = $data['bid'];
		$business = $this->erp_conn->from("business")->where("id",$bid)->get()->result_array();
		list($status, $status_explan) = $this->business->getBusinessStatus();
		if(!isset($business[0])){
			return failure("商机不存在");
		}
		//商机属于无效状态
		if(!in_array($business[0]['status'], array($status['follow_noanswer'],$status['garbage_invalid_info'],$status['garbage_three_times'],$status['garbage_other'],$status['garbage_repeat'],$status['3days_ago']))){
			return failure("商机状态错误");
		}
		$res_upd = $this->erp_conn->update("business",array('status'=>$status['newadd']),array('id'=>$bid));
		if($res_upd == true){
			return success("激活成功");
		}else{
			return failure("数据执行失败");
		}
	}

}

