<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 酒店商机管理
 */

class Shopers extends App_Controller {


	public function __construct(){
        parent::__construct();
		$this->load->model("business/common_model",'buscommon');
		$this->load->model('business/business_model', 'business');
		$this->load->model('business/business_shop_map_model', 'shopmap');
		$this->load->helper('array');
		$this->load->helper('ew_filter');
        $this->load->helper('functions');// 载入公共函数

        $this->load->library('sms');
    }

	/**
	 *  获取商家列表
	 */
	public function getShopers()
	{
        $inputs = $this->input->get();
		//搜索条件
        $data['grade'] = isset($inputs['grade']) ? $inputs['grade'] : 0; //商家等级
		
        $data['keyword'] = isset($inputs['keywords']) ? $inputs['keywords'] : ''; //关键字
		
		$province = isset($inputs['province']) ? $inputs['province'] : ""; //省
		$city = isset($inputs['city']) ? $inputs['city'] : ""; //市
		if(!empty($province) && !empty($city))
        {
            $address = "$province,$city";
        }
        elseif(!empty($province))
        {
            $address = "$province,";
        }
        elseif(!empty($city))
        {
            $address = ",$city";
        }
        $data['address'] = isset($address) ? $address : '';
		
		//page
        $data['page'] = (isset($inputs['page']) && $inputs['page'] > 0) ? $inputs['page'] : 1;
        $data['pagesize'] = isset($inputs['pagesize']) ? $inputs['pagesize'] : 10;
		
		$shoper_list = $this->buscommon->shoperInfo($data);
		
		//处理商家等级
		$grade = $this->getShoperGrade();
		$grade = $grade ? toHashmap($grade, "id") : array();
		if($shoper_list["rows"])
		{
			foreach ($shoper_list["rows"] as &$rows)
			{
				if(isset($grade[$rows["grade"]]))
				{
					$grade_info = $grade[$rows["grade"]];
					$rows["grade_name"] = $grade_info["grade_name"];
				}else
				{
					$rows["grade_name"] = "";
				}

				//咨询评分平均值
				$average = $this->erp_conn->select_avg("mark")->where(array("shop_id"=>$rows["uid"],"mark !="=>0))->get("business_shop_map")->result_array();
				$rows["mark_average"] = empty($average[0]['mark']) ? "" : round($average[0]['mark'],1) ;
			}
		}
		
		return success($shoper_list);
	}
	
	/**
	 * 获取策划师等级
	 * @return type
	 */
	public function getShoperGrade()
	{
		$result = $this->buscommon->getShoperGrade();
		
		return $result;
	}

	/**
	 * 添加商机的服务商家
	 */
	public function addBusShopers()
	{

		//接受参数
        $inputs = $this->input->post();
		
		$bus_id = isset($inputs["bus_id"]) ? $inputs["bus_id"] : 0; //商机id
		$bus_id = intval($bus_id);
        $binfo = $this->business->findRow(array("id" => $bus_id));
		if(!$binfo)
		{
			return failure("商机不存在");
		}
		
		$bus_shoperids = isset($inputs["shoperids"]) ? $inputs["shoperids"] : "";  //商家id字符串

		if(empty($bus_shoperids))
		{
			return failure("请选择商家");
		}

        //婚宴酒店类型不允许分配商家
        //获取婚礼类型
        list($btype , $btype_explan) = $this->business->getBusinessType();
        if($btype['wed_place'] == $binfo['ordertype'])
        {
            return failure('抱歉，婚宴酒店类型不允许分配商家');
        }
		
		$bus_shoperida = explode(",", trim($bus_shoperids));
		
		//获取已经添加的商家
		$planner_list = $this->shopmap->getShoperidsBybid($bus_id);
		$planner_ids = array_flatten($planner_list, "shop_id");
      
        //筛掉已经添加的商家，以免重复添加
		$shoper_ids = array();
		foreach($bus_shoperida as $v)
		{
			if(!in_array($v, $planner_ids))
			{
				$shoper_ids[] = $v;
			}
		}
        if(empty($shoper_ids)){
            return success('添加成功');
        }
        //获取商机状态
        list($btype , $btype_explan) = $this->business->getBusinessStatus();
        //获取交易状态
        list($tstatus, $tstatus_explan) = $this->business->getTradeStatus();
        //同步商家数据信息到主站
        $shoper_map_ids = array_filter(array_merge($planner_ids , $shoper_ids));
        $resp = $this->business->syncToMaster($bus_id , $shoper_map_ids);
        if($resp['code'] > 0)
        {
            //更新商机表[商机状态:已分单n进4],[分单时间],[交易状态:未见面]
            if($binfo['status'] == $btype['build'])
            {
                if($binfo['ordertime'] == 0){
                    $this->business->updateByCondition(
                        array('status' => $btype['parted_n_4'] , 'ordertime' => time() , 'trade_status' => $tstatus['no_faced']),
                        array('id' => $bus_id));
                }else{
                    $this->business->updateByCondition(
                        array('status' => $btype['parted_n_4'] , 'trade_status' => $tstatus['no_faced']),
                        array('id' => $bus_id));
                }

            }
        }
        else
        {
            return failure('分单失败，同步主站数据不成功:' . $resp['code_msg']);
        }
		
		//整理添加数组
        $shoper_ids = array_filter($shoper_ids);

        // 查看商家是否是测试人员
        $shoper_info = '';
       // if(!empty($shoper_ids))
        //{
            $uid_str = implode(',', $shoper_ids);

            if(!empty($uid_str))
            {
                $shoper_info = $this->buscommon->shoperInfo(array('uids' => $uid_str));
                $shoper_info = $shoper_info['rows'];
                if(!empty($shoper_info))
                {
                    $shoper_info = toHashmap($shoper_info, 'uid');
                }
            }
      //  }

		$init_arr = array();
		foreach ($shoper_ids as $key => $sid)
		{
			$init_arr[] = array(
				"bid" => $bus_id,
				"shop_id" => $sid,
				 "status" => Business_shop_map_model::STATUS_ALLOW,
				//"status" => Business_shop_map_model::STATUS_NOT,
				"face_status" => Business_shop_map_model::FACE_STATUS_NOT_MEET,
				"status_reason" => "",
				"allocatetime" => time(),
                'is_test' => isset($shoper_info[$sid]) && $shoper_info[$sid]['is_test'] == 1 ? 1 : 0
			);
		}


		if(!$this->shopmap->increase_batch($init_arr))
    {
        return failure("添加失败");
    }else
    {

        return success('添加成功');
    }
	}
	
	/**
	 * 单行移除操作
	 * @return type
	 */
	public function removeShoper()
	{
		$id = $this->input->get("id", 0);
		$id = intval($id);
        $shop_map = $this->shopmap->getRow($id);
		if(empty($shop_map))
		{
			return failure("记录不存在");
		}
        $binfo = $this->erp_conn->select("tradeno")->where('id', $shop_map['bid'])->get('business')->row_array();
        if(empty($binfo))
        {
            return failure("商机不存在");
        }
        $params = array('tradeno'=>$binfo['tradeno'],'shopper_uid'=>$shop_map['shop_id']);
        $ret = $this->curl->post($this->config->item('ew_domain').'erp/business/remove-shopper', $params);
        $ret = json_decode($ret,true);
        if($ret['result'] == 'succ'){
            if(!$this->shopmap->removeRow($id))
            {
                return failure("删除失败");
            }else
            {
                return success('删除成功');
            }
        }else{
            return failure($ret['msg']);
        }
		

	}
	
	/**
	 * 获取商机的服务商家列表
	 * @return type
	 */
	public function getBusShopers()
	{

		//商机id
		$bus_id = $this->input->get("bus_id", 0);
		$bus_id = intval($bus_id);
		if(!$this->business->findRow(array("id" => $bus_id)))
		{
			return failure("商机不存在");
		}
		$inputs = $this->input->get();
		// //状态
		 $status = isset($inputs['status']) ? $inputs['status'] : "" ;

		 //拼查询参数
		 $params = array('bid' => $bus_id);
		 if($status == 'date'){//4=>2
		 	$params['status != '] = Business_shop_map_model::STATUS_ALLOW ;
		 }elseif($status == 'sign'){
		 	$params['status'] = Business_shop_map_model::STATUS_SIGN;
		 }
		// 分页处理
		$limit = array();
		if(!empty($inputs['pagesize']) && !empty($inputs['page']))
        {
            if(is_numeric($inputs['pagesize']) && is_numeric($inputs['page']))
            {

                $start = ($inputs['page'] - 1) * $inputs['pagesize'];
                $limit = array('nums' => $inputs['pagesize'], 'start' => $start);
            }
        }
		
		//商机的服务商家列表
		//$total = $this->shopmap->counts(array("bid" => $bus_id));
		//$shoper_map_list = $this->shopmap->getShoperList(array("bid" => $bus_id), $limit, array('id' => 'desc'));
         $total = $this->shopmap->counts($params);
		 $shoper_map_list = $this->shopmap->getShoperList($params, $limit, array('id' => 'desc'));
		//取商家id数组
		$shoperid_arr = array_flatten($shoper_map_list, "shop_id");
		//取商家信息
		$data_where["uids"] = implode(",", array_unique($shoperid_arr));
		//page
		if(isset($inputs['page']) && isset($inputs['pagesize']))
		{
			 $data_where['page'] = 1;
			 $data_where['pagesize'] = $inputs['pagesize'];
		}
		$shoper_info_list = $this->buscommon->shoperInfo($data_where);
		
		$shoper_info = !empty($shoper_info_list["rows"]) ? $shoper_info_list["rows"] : array();
		$shoper_info = toHashmap($shoper_info, "uid");
		$list = array();
		//处理商家等级
		$grade = $this->buscommon->getShoperGrade();
		$grade = $grade ? toHashmap($grade, "id") : array();
		foreach ($shoper_map_list as &$map)
		{
            $map["facetime"] = !empty($map["facetime"]) ? date('Y-m-d H:i:s',$map["facetime"]) :'';
            $map["meettime"] = !empty($map["meettime"]) ? date('Y-m-d H:i:s',$map["meettime"]) :'';
            $map["losttime"] = !empty($map["losttime"]) ? date('Y-m-d H:i:s',$map["losttime"]) :'';
            $map["allocatetime"] = !empty($map["allocatetime"]) ? date('Y-m-d H:i:s',$map["allocatetime"]) :'';
			//处理状态对应描述
			$map["status_detail"] = "";
			if($map["status"] == Business_shop_map_model::STATUS_LOST)
			{
				switch ($map["face_status"])
				{
					case Business_shop_map_model::FACE_STATUS_NOT_MEET:
						$map["status_detail"] = "未见面，已丢单";break;
					case Business_shop_map_model::FACE_STATUS_MEET:
						$map["status_detail"] = "已见面，已丢单";break;
					default:$map["status_detail"] = "已丢单";break;
				}
			}elseif($map["status"] == Business_shop_map_model::STATUS_SIGN)
			{
				$map["status_detail"] = "已签约";
			}else
			{
				switch ($map["face_status"])
				{
					case Business_shop_map_model::FACE_STATUS_NOT_MEET:
						$map["status_detail"] = "未见面";break;
					case Business_shop_map_model::FACE_STATUS_MEET:
						$map["status_detail"] = "已见面";break;
					default:$map["status_detail"] = "";break;
				}
			}
			//处理用户信息
			if(isset($shoper_info[$map['shop_id']]))
			{
				$map = array_merge($map, $shoper_info[$map['shop_id']]);
			}
			
			if(isset($map["grade"]) && isset($grade[$map["grade"]]))
			{
				$grade_info = $grade[$map["grade"]];
				$map["grade_name"] = $grade_info["grade_name"];
			}
			else
			{
				$map["grade_name"] = "";
			}

			$map["mark"] = ($map["mark"] == 0) ? "" : $map["mark"];
			//咨询评分平均值
			$average = $this->erp_conn->select_avg("mark")->where(array("shop_id"=>$map["uid"],"mark !="=>0))->get("business_shop_map")->result_array();
			$map["mark_average"] = empty($average[0]['mark']) ? "" : round($average[0]['mark'],1) ;
			
			$list[] = $map;
		}
		return success(array('total' => $total,'rows' => $list));
	}

	/*
	 * 分配约见商家
	 * 由n进4 －》4进2
	 * bid:
	 * shop_id:
	 */
	public function dateShopper()
	{
		$data = $this->input->post();
        $bus_id = isset($data["bid"]) ? $data["bid"] : 0; //商机id
        $bus_id = intval($bus_id);
        $binfo = $this->business->findRow(array("id" => $bus_id));
        if(!$binfo)
        {
            return failure("商机不存在");
        }
        if(empty($data['shop_id'])){
            return failure("请选择商家!");
        }
        //获取已经添加的商家
        $planner_list = $this->shopmap->getShoperids($data['bid'],$data['shop_id']);
        $planner_ids = array_flatten($planner_list, "shop_id");
        //筛掉已经添加的商家，以免重复添加
        $shoper_ids = array();
        foreach($data['shop_id'] as $v)
        {
            if(in_array($v, $planner_ids))
            {
                $shoper_ids[] = $v;
            }
        }
        if(!empty($shoper_ids)){
            return success("分配成功！");
        }

        $this->erp_conn->where(array("bid"=>$data['bid'],"status"=>Business_shop_map_model::STATUS_ALLOW));
		$this->erp_conn->where_in("shop_id",$data['shop_id']);



        $post_params = array('tradeno'=>$binfo['tradeno'],'shopper_ids'=>implode(',',$data['shop_id']));
        $ret = $this->curl->post($this->config->item('ew_domain').'/erp/business/confirm-shopper', $post_params);
        $ret = json_decode($ret,true);
        if($ret['result'] == 'succ') {
            //把map改为（4进2）
            $res = $this->erp_conn->update("business_shop_map",array("status"=>Business_shop_map_model::STATUS_NOT , 'meettime' => time()));

            //商机status改为 parted (已分单4进2)
            list($business_status, $status_explan) = $this->business->getBusinessStatus();
            $bus = $this->erp_conn->where('id',$bus_id)->update("business",array("status"=>$business_status['parted']));
            $bus = $this->erp_conn->where(array('id'=>$bus_id,"signletime"=>0))->update("business",array("signletime"=>time()));
            if($res == true && $bus == true){

                $binfo = $this->erp_conn->select("operate_uid , username ,mobile ,tradeno")->where("id",$data['bid'])->get("business")->row_array();
                //发送短信给商家
                //根据商机id获取新人信息
                $sys_users = $this->erp_conn->select("username , mobile")->where("id", $binfo['operate_uid'])->get('erp_sys_user')->row_array();

                $shoper_mobiles = array();
                $content = "";
                if(!empty( $data['shop_id']))
                {
                    $shoper_list = $this->buscommon->shoperInfo(array('uids' => implode(',' , $data['shop_id'])));
                    foreach($shoper_list['rows'] as $sval)
                    {
                        $shoper_mobiles[] = $sval['phone'];
                        $url = 'm.easywed.cn/planner/'.$sval['uid'];
                        $short_url = get_short_url($url);
                        if($short_url['status']=='succ')
                        {
                            $url = $short_url['short'];
                        }
                        $info[] = $sval['nickname'].'，（电话：'.$sval['phone'].'）';
                    }


                    foreach($info as $v)
                    {

                        $content .= $v.' ;';
                    }
                    $content = rtrim($content, ";");
                }
                $users_content =  '您好，我是婚礼顾问'.@$sys_users['username'].'（电话：'.@$sys_users['mobile'].'），您选择的易结策划师有：'.$content.'。稍后策划师会和您沟通具体筹备婚礼的事宜。如有疑问，请与我联系。';
                //给新人发短信
                $this->sms->send(array($binfo['mobile']) , $users_content);
                //给策划师发短信
                $shoper_content='您好，您接单的新人'.$binfo['username'].'，联系电话'.$binfo['mobile'].'，请在24小时内尽快联系新人。温馨提示：你可以在易结商家版客户端订单管理中查看新人需求详情，订单号为'.$binfo['tradeno'].'。';

                $this->sms->send($shoper_mobiles , $shoper_content);
                return success("分配成功！");

            }else
            {
                return failure("分配失败！");
            }
        }else{
            return failure($ret['msg']);
        }


	}

    /*
     * 发送短信给新人
     * mobile:
     * content:
     */
    public function sendMessage(){
        $data = $this->input->post();
        if(empty($data['bus_id']) || $data['bus_id'] == 0){
            return failure("bid error!");
        }
        if(empty($data['content'])){
            return failure("发送信息不能为空!");
        }
        $binfo = $this->erp_conn->select("mobile")->where("id", $data['bus_id'])->get('business')->row_array();
        if(!empty($binfo))
        {
            $send = $this->sms->send(array($binfo['mobile']) , $data['content']);
            if($send["code"] == 1)
            {
                return success("发送成功");
            }else
            {
                return failure("发送失败");
            }
        }

    }

/**
* 设置已见面
* @return type
*/
	public function toMeet()
	{
		$id = $this->input->get("id", 0);
		$id = intval($id);
		$shopmap = $this->shopmap->getRow($id);
		if(!$shopmap)
		{
			return failure("记录不存在");
		}
		$upt = array("face_status" => Business_shop_map_model::FACE_STATUS_MEET,'facetime' => time());
		//设置商机表的交易状态为已见面
		list($tradestatus, $tradestatus_explan) = $this->business->getTradeStatus();
		if(!$this->shopmap->updateRow($id, $upt))
		{
			return failure("修改失败");
		}else
		{
			$upt_bus = array("trade_status" => $tradestatus["faced"]);
			if(!$this->business->modify(array("id" => $shopmap["bid"]), $upt_bus))
			{
				return failure("修改失败");
			}
			return success('修改成功');
		}
	}
	
	/**
	 * 设置为已丢单
	 * @return type
	 */
	public function lostOrder()
	{
		$id = $this->input->post("id", 0);
		$id = intval($id);
        $shop_map_info = $this->shopmap->getRow($id);
		if(!$shop_map_info)
		{
			return failure("记录不存在");
		}
		
		$reason = $this->input->post("reason");
		$reason = ew_filter_quote_html($reason);
		if(empty($reason))
		{
			return failure("请填写丢单原因");
		}

        //同步状态信息至主站
        $binfo = $this->business->findRow(array('id' => $shop_map_info['bid']));

        if(!$this->shopmap->syncLostOrder($binfo['tradeno'] , $shop_map_info['shop_id'] , $reason))
        {
            return failure('同步状态信息失败');
        }
		
		$upt = array("status" => Business_shop_map_model::STATUS_LOST , 'status_reason' => $reason , 'losttime' => time());
		if(!$this->shopmap->updateRow($id, $upt))
		{
			return failure("修改失败");
		}else
		{
            $shoper_mobiles = array();
            $sys_users = $this->erp_conn->select("username , mobile")->where("id", $binfo['follower_uid'])->get('erp_sys_user')->row_array();
            if(!empty($shop_map_info)) {
                $shoper_list = $this->buscommon->shoperInfo(array('uids' => $shop_map_info['shop_id']));
                foreach ($shoper_list['rows'] as $sval) {
                    $shoper_mobiles = $sval['phone'];

                }
                $shoper_content = '您好，给您分配的新人' . $binfo['username'] . '，已经确定不再需要您提供服务。温馨提示：您可以在易结商家版客户端订单管理中查看，订单号为'.$binfo['tradeno'].'。';
                $this->sms->send(array($shoper_mobiles), $shoper_content);
            }
			return success('修改成功');
		}
	}

	/*
	 * 给策划师评分
	 * shop_map_id:
	 * shop_id:
	 * consult_mark:
	 * mark_description:
	 */
	public function giveMark()
	{
		$input = $this->input->post();
		if(!isset($input['shop_map_id']) || !isset($input['shop_id']) || !isset($input['consult_mark']) || !is_numeric($input['consult_mark'])){
			return failure("params error!");
		}
		$upd_data = array("mark" => $input['consult_mark'],"mark_description" => $input['mark_description']);
		$where = array("id" => $input['shop_map_id'],"shop_id" => $input['shop_id']);
		$res = $this->erp_conn->update("business_shop_map",$upd_data,$where);
		if($res == true){
			return success("mark success!");
		}else{
			return failure("mark failure!");
		}
	}

}
