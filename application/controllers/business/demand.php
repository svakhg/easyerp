<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Demand extends App_Controller
{
	/**
	 * 商机列表
	 */
	public function index()
	{
		$this->load->model('business/business_model', 'business');
		$this->load->model('sys_user_model', 'sum');
		$this->load->model('system/roles_model', 'srm');
		$this->load->model('system/department_model', 'sdm');

		list($source, $source_explan) = $this->business->getBusinessSource();
		list($customer, $customer_explan) = $this->business->getCustomerIdentify();
		list($ordertype, $ordertype_explan) = $this->business->getBusinessType();
		list($budget, $budget_explan) = $this->business->getWedBudget();
		list($findtype, $findtype_explan) = $this->business->getFindShopType();
		list($wedtype, $wedtype_explan) = $this->business->getWedType();
		list($status, $status_explan) = $this->business->getBusinessStatus();

		// 新人顾问
		$department = $this->sdm->getWedAdviser();
		$adviser = $this->sum->findAll(array('department' => $department['id'] , 'num_code <' => '99999000'), array(), array());
		
		// 运营人员
		$operater_department = $this->sdm->getOperater();
		$operater = $this->sum->findAll(array('department' => $operater_department['id'] , 'num_code <' => '99999000'), array(), array());

		// 登陆人信息
		$admin = $this->sum->getInfoById($this->session->userdata('admin_id'));
		if(isset($admin)){
			$role = $this->erp_conn->where("role_name","婚礼顾问主管")->get("erp_sys_role")->row_array();
			if(in_array($role['id'], explode(",",$admin['role_id']))){
				$admin['is_hunliguwenzhuguan'] = 1;
			}else{
				$admin['is_hunliguwenzhuguan'] = 0;
			}
		}

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
		$this->_data['adviser'] = $adviser;
		$this->_data['operater'] = $operater;
		$this->_data['admin'] = $admin;
		$this->_data['usertype'] = $this->business->getUserType();
// print_R($this->_data);exit;
		$this->load->view('business/index', $this->_data);
	}

	/**
	 * 顾问商机录入页面
	 */
	public function add()
	{
		$this->load->model('business/business_model', 'business');
		list($source, $source_explan) = $this->business->getBusinessSource();
		list($customer, $customer_explan) = $this->business->getCustomerIdentify();
		list($ordertype, $ordertype_explan) = $this->business->getBusinessType();
		list($budget, $budget_explan) = $this->business->getWedBudget();
		list($findtype, $findtype_explan) = $this->business->getFindShopType();
		list($wedtype, $wedtype_explan) = $this->business->getWedType();

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
		$this->_data['usertype'] = $this->business->getUserType();

		$this->load->view('business/adviseradd', $this->_data);
	}

	/**
	 * 商机标记操作
	 */
	public function baseinfo()
	{
		$params = $this->input->post();
		if(empty($params['source']))
		{
			return failure('请输入商机来源');
		}
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
		$data['status'] = !empty($params['status']) ? $status[$params['status']] : $status['newadd'];
		$data['username'] = !empty($params['username']) ? $params['username'] : '';
		$data['userpart'] = !empty($params['userpart']) ? $params['userpart'] : 0;
		$data['tel'] = !empty($params['tel']) ? $params['tel'] : '';
		$data['weixin'] = !empty($params['weixin']) ? $params['weixin'] : '';
		$data['qq'] = !empty($params['qq']) ? $params['qq'] : '';
		$data['other_contact'] = !empty($params['other_contact']) ? $params['other_contact'] : '';
		$data['source'] = !empty($params['source']) ? $params['source'] : '';
		$data['source_note'] = !empty($params['source_note']) ? $params['source_note'] : '';
		$data['hotel_name'] = !empty($params['hotel_name']) ? $params['hotel_name'] : '';
		// $data['status_note'] = !empty($params['status_note']) ? $params['status_note'] : '';
		$data['tradeno'] =  $this->business->generateTradeNo();
		$data['sys_usertype'] = $writer['sys_usertype_adviser']; // 录入商机来源
		$data['sys_uid'] = $data['follower_uid'] = $this->session->userdata('admin_id'); // 婚礼顾问uid
		$data['createtime'] = $data['advisertime'] = time();
        $data['hmsr'] = '';
        $data['source_url'] = '';

		// 保持数据库
		//$this->business->modify(array('mobile' => $data['mobile']), array('show' => 0));
		$result = $this->business->add($data);

		return success(array('id' => $result));
	}

	/**
	 * 客户需求
	 */
	public function userdemand()
	{
		$params = $this->input->post();
		if(empty($params['id']))
		{
			return failure('商机id不可为空');
		}

		// 载入模型
		$this->load->model('business/business_model', 'business');
		$this->load->model('business/business_extra_model', 'be');

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

		if(!$this->business->modify(array('id' => $params['id']), $baseinfo))
		{
			return failure('婚礼日期和商机类型保存失败');
		}

		// 婚礼地点处理
		if(!empty($params['is_wed_place']))
		{
			$extra['wed_place'] = $params['wed_place'];
			$extra['wed_place_area'] = '';
		}
		else
		{
			$extra['wed_place'] = '';
			$extra['wed_place_area'] = $params['wed_place_area'];
		}

        list($ordertype, $ordertype_explan) = $this->business->getBusinessType();
        //如果是婚礼酒店相关,则wed_place_area保存place_area
        if($baseinfo['ordertype'] != $ordertype['wed_plan'])
        {
            $extra['wed_place'] = '';
            $extra['wed_place_area'] = $params['place_area'];
        }

		// 城市
		$extra['bid'] = $params['id'];
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

        list($wedtype, $wedtype_explan) = $this->business->getWedType();
		$extra['wed_type'] = !empty($params['wed_type']) ? $params['wed_type'] : $wedtype['type_no'];
		!empty($params['guest_from']) && $extra['guest_from'] = $params['guest_from'];
		!empty($params['guest_to']) && $extra['guest_to'] = $params['guest_to'];
		!empty($params['desk_from']) && $extra['desk_from'] = $params['desk_from'];
		!empty($params['desk_to']) && $extra['desk_to'] = $params['desk_to'];
		!empty($params['price_from']) && $extra['price_from'] = $params['price_from'];
		!empty($params['price_to']) && $extra['price_to'] = $params['price_to'];

		!empty($params['budget']) && $extra['budget'] = $params['budget'];
		$extra['budget_note'] = $params['budget_note'];
		$extra['findtype'] = $params['findtype'];
		$extra['findnote'] = $params['findnote'];
		$extra['wish_contact'] = $params['wish_contact'];
		$extra['moredesc'] = $params['moredesc'];

		$result = $this->be->add($extra);

		if(!$result)
		{
			return failure('客户需求保存失败');
		}
		return success(array('id' => $params['id'], 'eid' => $result));
	}


	/**
	 * 分配顾问接口
	 */
	public function adviser()
	{
		$this->load->model('sys_user_model', 'sum');
		$this->load->model('system/department_model', 'sdm');
		$this->load->model('business/business_model', 'bbm');
		$this->load->model('business/business_adviser_log_model', 'bbalm');
		//list($status, $status_explan) = $this->bbm->getBusinessStatus();
		//$allow_change_status = array($status['newadd'], $status['follow_noanswer'], $status['follow_next']);

		// 参数处理 
		$params = $this->input->post();
		if(empty($params['id']) || !is_array($params['id']))
		{
			return failure('请选择要分配的顾问的商机');
		}
		if(empty($params['aid']))
		{
			return failure('请选择婚礼顾问');
		}

		// 获取分配顾问的商机 并对已经分配顾问的商机验证是否可修改顾问
		$cond['autowhere']['id'] = $params['id'];
		$business = $this->bbm->getAll($cond);
		$msg = '';
		$st = array();
		foreach ($business as $key => $val)
		{
			if(!empty($val))
			{
				$ids[] = $val['id'];
				// 处理当前跟进人信息 插入adviser_map表
				if(!empty($val['follower_uid']))
				{
					$ut = !empty($val['advisertime']) ? $val['advisertime'] : $val['createtime'];
					$insert_data[] = array(
						'status' => $val['status'], 'adviser_id' => $val['follower_uid'], 'allocatetime' => $ut, 'bid' => $val['id']
					);
				}
			}
		}

		// 验证权限
		$admin = $this->sum->getInfoById($this->session->userdata('admin_id'));
		$deparment = $this->sdm->getWedAdviser();
		if(!$admin['satrap'])
		{
			return failure('您没有权限分配婚礼顾问');
		}


		if(!empty($ids))
		{
			$result = $this->bbm->updateByCondition(array('follower_uid' => $params['aid'], 'advisertime' => time()), array('id' => $ids));
			if(!$result)
			{
				return failure('分配顾问失败，请重新尝试');
			}
		}

		if(!empty($insert_data))
		{
			$this->bbalm->add_batch($insert_data);
		}

		//给主站发送顾问
        $this->syncMasterConsultant($params['id']);
        
		return success(array('id' => $params['aid']));
	}

	/*
	 * 分配运营人员
	 * $post['bid']:(1,2,3)
	 * $post['operate_uid']:(23)
	 */
	public function operator()
	{
		$this->load->model('business/business_model', 'business');
		$post = $this->input->post();
		if(!isset($post['bid'])){
			return failure("please choose the business!");
		}
		if(!isset($post['operate_uid'])){
			return failure("please choose the operator!");
		}

		list($status, $status_explan) = $this->business->getBusinessStatus();
		$this->erp_conn->where_in("status",array($status['build'],$status['parted_n_4'],$status['parted']))->where_in("id",$post['bid']);
		$res = $this->erp_conn->update("business",array('operate_uid'=>$post['operate_uid'],'operatetime'=>time()));
		if($res == true){
            //给主站发送顾问和运营人员信息
            $this->syncMasterOperator($post['bid']);
			return success("已建单、已分单n进4、已分单4进2状态的商机操作成功，其他商机未修改！");
		}else{
			return failure("分配失败！");
		}
	}

	/*
	 * 向主站同步商机的新人顾问和运营人员信息
     * bid_arr:
	 */
	private function syncMasterConsultant($bid_arr)
	{
        if(empty($bid_arr)){
            return true;
        }
        $business = $this->erp_conn->where_in("id",$bid_arr)->get("business")->result_array();
        foreach($business as $k => $v){
            $tradeno_arr[] = $v['tradeno'];
        }
        $consultant = $this->erp_conn->where("id",$business[0]['follower_uid'])->get("erp_sys_user")->result_array();
        $post_params['consultant_name'] = isset($consultant[0]) ? $consultant[0]['username'] : "" ;
        $post_params['consultant_phone'] = isset($consultant[0]) ? $consultant[0]['mobile'] : "" ;
        $post_params['consultant_id'] = isset($consultant[0]) ? $consultant[0]['id'] : "" ;
        $post_params['tradeno'] = implode(',', $tradeno_arr);
        $re = $this->curl->post($this->config->item('ew_domain').'/erp/business/update-consultant', $post_params);
        $res = json_decode($re,true);
        if($res['result'] == "succ"){
            return true;
        }else{
            return false;
        }
	}

    private function syncMasterOperator($bid_arr)
    {
        if(empty($bid_arr)){
            return true;
        }
        $business = $this->erp_conn->where_in("id",$bid_arr)->get("business")->result_array();
        foreach($business as $k => $v){
            $tradeno_arr[] = $v['tradeno'];
        }
        $operation = $this->erp_conn->where("id",$business[0]['operate_uid'])->get("erp_sys_user")->result_array();
        $post_params['operation_name'] = isset($operation[0]) ? $operation[0]['username'] : "" ;
        $post_params['operation_phone'] = isset($operation[0]) ? $operation[0]['mobile'] : "" ;
        $post_params['operation_id'] = isset($operation[0]) ? $operation[0]['id'] : "" ;
        $post_params['tradeno'] = implode(',', $tradeno_arr);
        $re = $this->curl->post($this->config->item('ew_domain').'/erp/business/update-operation', $post_params);
        $res = json_decode($re,true);
        if($res['result'] == "succ"){
            return true;
        }else{
            return false;
        }
    }


	/*
	 * 商机历史（同一电话号码的多条商机记录）
	 */
	public function history()
	{
		$this->load->model('business/business_model', 'business');
		$this->load->model('sys_user_model', 'sum');
		$this->load->model('system/roles_model', 'srm');
		$this->load->model('system/department_model', 'sdm');

		list($source, $source_explan) = $this->business->getBusinessSource();
		list($customer, $customer_explan) = $this->business->getCustomerIdentify();
		list($ordertype, $ordertype_explan) = $this->business->getBusinessType();
		list($budget, $budget_explan) = $this->business->getWedBudget();
		list($findtype, $findtype_explan) = $this->business->getFindShopType();
		list($wedtype, $wedtype_explan) = $this->business->getWedType();
		list($status, $status_explan) = $this->business->getBusinessStatus();

		// 新人顾问
		$department = $this->sdm->getWedAdviser();
		$adviser = $this->sum->findAll(array('department' => $department['id'], 'num_code <' => '99999000'), array(), array());

		// 登陆人信息
		$admin = $this->sum->getInfoById($this->session->userdata('admin_id'));

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
		$this->_data['adviser'] = $adviser;
		$this->_data['admin'] = $admin;
		$this->_data['usertype'] = $this->business->getUserType();

		$this->load->view('business/history', $this->_data);
	}
    
    /**
     * 历史数据重复部分处理
     */
    public function business_distinct()
    {
        set_time_limit(0);
        $this->load->model('business/business_distinct_model', 'bd');
        $this->load->model('business/business_model', 'bm');
        $total = $this->bd->counts(array());
        $nums = 50;
        $page_num = ceil($total / $nums);
        for ($page = 1; $page <= $page_num; $page++)
        {
            $start = ($page - 1) * $nums;
            $mobiles = $this->bd->findAll(array(), array('nums' => $nums, 'start' => $start), array());
            foreach($mobiles as $mobile)
            {
                $business = $this->bm->findAll(array('mobile' => $mobile['mobile']));
                if(count($business) <= 1)
                {
                    continue;
                }
                $kxz = $kgj = $kfs = $kfd = $xz = $gj = $fs = $fd = $ids = array();
                foreach ($business as $busi)
                {
                    if($busi['status'] == 1)
                    {
                        $kxz[$busi['id']] = $busi['follower_uid'];
                    }
                    elseif($busi['status'] == 2 || $busi['status'] == 3)
                    {
                        $kgj[$busi['id']] = $busi['follower_uid'];
                    }
                    elseif($busi['status'] == 4 || $busi['status'] == 5)
                    {
                        $kfs[$busi['id']] = $busi['follower_uid'];
                    }
                    elseif($busi['status'] == 6)
                    {
                        $kfd[$busi['id']] = $busi['follower_uid'];
                    }
                }
                $xz = array_keys($kxz);
                $gj = array_keys($kgj);
                $fs = array_keys($kfs);
                $fd = array_keys($kfd);
                
                // 如果都是新增不做任何处理
                if(!empty($xz) && empty($gj) && empty($fs) && empty($fd))
                {
                    continue;
                }
                // 如果都是废商机 设置一条有顾问的为废商机 其他的做软删除
                elseif(empty($xz) && empty($gj) && !empty($fs) && empty($fd))
                {
                    foreach($kfs as $id => $follower_uid)
                    {
                        if(!empty($follower_uid))
                        {
                            $fs_id = $id;
                            break;
                        }
                    }
                    foreach($fs as $fid)
                    {
                        if($fs_id != $fid)
                        {
                            $ids[] = $fid;
                        }
                    }
                }
                // 如果有已经分单的或者有跟进中的 把费商机设置删除
                elseif(!empty($fd) || !empty($gj))
                {
                    $ids = $fs;
                }
                
                if(!empty($ids) && is_array($ids))
                {
                    $this->bm->updateByCondition(array('is_del' => 1, 'status' => 10), array('id' => $ids));
                }
                else
                {
                    $this->bd->updateByCondition(array('is_succ' => 0), array('mobile' => $mobile['mobile']));
                }
            }
        }
    }
}