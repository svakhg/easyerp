<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business extends App_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 商机列表
	 */
	public function listbusiness($export = false)
	{
		$this->load->model('business/business_model', 'business');
		$this->load->model('business/business_extra_model', 'be');
        $this->load->model('commons/region_model' , 'region');
        $this->load->model('sys_user_model', 'sum');

		$this->load->helper('array');

		$this->load->helper('ew_filter');

		// 获取请求参数
		$params = $this->input->get();
        //$params = ew_filter_quote_html($params);

		// 状态处理
		list($source, $source_explan) = $this->business->getBusinessSource();
		list($ordertype, $ordertype_explan) = $this->business->getBusinessType();
		list($budget, $budget_explan) = $this->business->getWedBudget();
		list($findtype, $findtype_explan) = $this->business->getFindShopType();
		list($status, $status_explan) = $this->business->getBusinessStatus();
        list($tradestatus, $tradestatus_explan) = $this->business->getTradeStatus();//获取交易状态

		$rso = array_flip($source);
		$ror = array_flip($ordertype);
		$rbu = array_flip($budget);
		$rfi = array_flip($findtype);
		$rst = array_flip($status);
		$tradestatus_flip = array_flip($tradestatus);
        $cond = array();
        // 条件处理
		if($params['follower_uid'])
        {
            $cond['autowhere']['follower_uid'] = $params['follower_uid'];
        }
        if(!empty($params['operate_uid']))
        {
            if($params['operate_uid'] == 99){
                $cond['autowhere']['operate_uid'] ='';
            }else{
                $cond['autowhere']['operate_uid'] = $params['operate_uid'];
            }

       }

        //属于策划师运营部 并且 不是主管 只能看到自己负责的商机
        $user_info = $this->sum->getInfoById($this->session->userdata('admin_id'));
        if($user_info['satrap'] == 0)
        {
            $cond['autowhere']['follower_uid'] = $this->session->userdata('admin_id');
        }
        if($user_info['department'] == 2 && $user_info['satrap'] == 0)
        {
            unset($cond['autowhere']['follower_uid']);
            unset($cond['autowhere']['operate_uid']);
            $cond['autowhere']['where_str'] = '(`follower_uid` = '.$this->session->userdata('admin_id').' OR `operate_uid` = '.$this->session->userdata('admin_id').')';
        }

		!empty($params['mobile']) && $cond['autowhere']['mobile like'] = '%'.$params['mobile'].'%';
		!empty($params['username']) && $cond['autowhere']['username like'] = '%'.$params['username'].'%';
		!empty($params['usertype']) && $cond['autowhere']['usertype'] = $params['usertype'];
		!empty($params['ordertype']) && $cond['autowhere']['ordertype'] = $params['ordertype'];
		!empty($params['source']) && $cond['autowhere']['source'] = $params['source'];
		!empty($params['creat_time_start']) && $cond['creat_time_start'] = $params['creat_time_start'];
		!empty($params['creat_time_end']) && $cond['creat_time_end'] = $params['creat_time_end'];
		!empty($params['wed_time_start']) && $cond['wed_time_start'] = $params['wed_time_start'];
		!empty($params['wed_time_end']) && $cond['wed_time_end'] = $params['wed_time_end'];
		!empty($params['adviser_time_start']) && $cond['adviser_time_start'] = $params['adviser_time_start'];
		!empty($params['adviser_time_end']) && $cond['adviser_time_end'] = $params['adviser_time_end'];

        !empty($params['operate_time_start']) && $cond['operate_time_start'] = $params['operate_time_start'];
        !empty($params['operate_time_end']) && $cond['operate_time_end'] = $params['operate_time_end'];

        !empty($params['bid']) && $cond['autowhere']['tradeno'] = $params['bid'];//$this->business->formatBid($params['bid'] , 0 , true);
        //测试数据
		if($this->session->userdata('is_test') == 0)
		{
			$cond['autowhere']['is_test'] = 0;
		}
        //剔除重复数据
        //$cond['autowhere']['is_del'] = 0;
		// 状态处理
		if(isset($params['status_1']) && $params['status_1'] == 100 && $params['status_2'] == 0)
        {
            $cond['autowhere']['status'] = array($status['follow_noanswer'] , $status['follow_next']);
        }
        elseif(isset($params['status_1']) && $params['status_1'] == 101 && $params['status_2'] == 0)
        {
            $cond['autowhere']['status'] = array($status['garbage_invalid_info'] , $status['garbage_three_times']);
        }
        elseif(isset($params['status_2']) && $params['status_2'] != 0)
        {
            $cond['autowhere']['status'] = $params['status_2'];
        }
        elseif(isset($params['status_2']) && $params['status_1'] != 0)
        {
            $cond['autowhere']['status'] = $params['status_1'];
        }

		// 分页处理
		$limit = array();
		if(!empty($params['pagesize']) && !empty($params['page']))
        {
            if(is_numeric($params['pagesize']) && is_numeric($params['page']))
            {
                $params['pagesize'] = $params['pagesize'] < 20 ? 20 : $params['pagesize'];
                $start = ($params['page'] - 1) * $params['pagesize'];
                $limit = array('nums' => $params['pagesize'], 'start' => $start);
            }
        }
		// 获取记录
		$total = $this->business->getOne($cond, array('count(id) AS num'));
        // 记录列表
		$list = $this->business->getAll($cond, $limit, array('id' => 'desc'));
		// 获取每个电话提交的次数
		$mobiles = array_flatten($list, 'mobile');
		$num = $this->business->getAll(array('mobile_count' => $mobiles), array(), array(), 'count(id) AS num, mobile');
		$history = toHashmap($num, 'mobile', 'num');

		$ids = array_flatten($list, 'id');
		$extra_list = $this->be->getAll(array('bids' => $ids));
		$extra_list = toHashmap($extra_list, 'bid');
		$data = array();

		foreach ($list as $key => &$business)
		{
			$data[$key]['id'] = $business['id'];
			//使用原有的tradeno
			$data[$key]['tradeno'] = $business['tradeno'];
			// $data[$key]['tradeno'] = $this->business->formatBid($business['id'] , $business['createtime']);
			$data[$key]['status'] = isset($rst[$business['status']]) ? $status_explan[$rst[$business['status']]] : '';
			$data[$key]['status_alias'] = isset($rst[$business['status']]) ? $rst[$business['status']] : '';
			$data[$key]['trade_status'] = $business['trade_status'];
			$data[$key]['trade_status_alias'] = isset($tradestatus_flip[$business['trade_status']]) ? $tradestatus_flip[$business['trade_status']] : "" ;
			$data[$key]['trade_status_text'] = isset($tradestatus_flip[$business['trade_status']]) ? $tradestatus_explan[$tradestatus_flip[$business['trade_status']]] : "" ;
			if(isset($cond['follower_uid']) && $cond['follower_uid'] == $this->session->userdata('admin_id'))
			{
				$data[$key]['follower'] = $this->session->userdata('admin');
			}
			else
			{
				$data[$key]['follower'] = '';
				if(!empty($business['follower_uid']))
				{
					$admin = $this->sum->getInfoById($business['follower_uid']);
					$data[$key]['follower'] = isset($admin['username']) ? $admin['username'] : '';
				}
			}
			//分配的运营人员名称
			$data[$key]['operate_name'] = ($business['operate_uid'] == 0) ? "" : $this->sum->getInfoById($business['operate_uid'])['username'];
			$data[$key]['operatetime'] = ($business['operatetime'] == 0) ? "" : date("Y-m-d H:i:s",$business['operatetime']);

			$data[$key]['source'] = isset($rso[$business['source']]) ? $source_explan[$rso[$business['source']]] : '';
			$data[$key]['source_note'] = $business['source_note'];
			$data[$key]['ordertype'] = isset($ror[$business['ordertype']]) ? $ordertype_explan[$ror[$business['ordertype']]] : '';
			$data[$key]['usertype'] = $business['usertype'];
			$data[$key]['username'] = $business['username'];
			$data[$key]['mobile'] = $business['mobile'];
			$data[$key]['wed_date'] = !empty($business['wed_date']) ? date('Y-m-d', $business['wed_date']) : '';
			$data[$key]['add_date'] = date('Y-m-d H:i:s', $business['createtime']);
			$data[$key]['add_date_month'] = date('Y-m', $business['createtime']);
			$data[$key]['advisertime'] = !empty($business['advisertime']) ? date('Y-m-d H:i:s', $business['advisertime']) : '';
			$data[$key]['tips'] = '';
			$data[$key]['history'] = isset($history[$business['mobile']]) ? $history[$business['mobile']] : 0;
			$id = $business['id'];
			if(isset($extra_list[$id]))
			{
				$extra = $extra_list[$id];
				$data[$key]['wed_place'] = empty($extra['wed_place']) ? $extra['wed_place_area'] : $extra['wed_place'];
				$data[$key]['budget'] = isset($rbu[$extra['budget']]) ? $budget_explan[$rbu[$extra['budget']]] : '';
				$data[$key]['location'] = $extra['location'];
				$data[$key]['findtype'] = isset($rfi[$extra['findtype']]) ? $findtype_explan[$rfi[$extra['findtype']]] : '';
			}
			else
			{
				$data[$key]['wed_place'] = '';
				$data[$key]['budget'] = '';
				$data[$key]['location'] = '';
				$data[$key]['findtype'] = '';
			}

			$record = $this->erp_conn
				->where(array("bid"=>$business['id'],'status'=>1))
				->order_by("created","desc")
				->get("records")->result_array();
			if(isset($record[0])){
				if($record[0]["record_time"] == 0){
					$data[$key]['last_record_time'] = date("Y-m-d H:i",$record[0]['created']);
				}else{
					$data[$key]['last_record_time'] = date("Y-m-d H:i",$record[0]['record_time']);
				}
			}else{
				$data[$key]['last_record_time'] = "";
			}
			$data[$key]['last_record_content'] = isset($record[0]) ? $record[0]['content'] : "" ;
		}
		if($export)
		{
			return array('total' => $total['num'], 'rows' => $data);
		}
		return success(array('total' => $total['num'], 'rows' => $data));
	}

	public function exportcsv()
	{
		$this->load->helper('excel_tools');
		$exporter = new ExportDataExcel('browser', date('Y-m-d') . '_business.xls');
		$exporter->initialize();
		$exporter->addRow(array(
			'商机编号',
			'商机状态',
			'交易状态',
			'新人顾问',
			'运营',
			'商机来源',
			'来源说明',
			'商机类型',
			'客户类型',
			'客户姓名',
			'客户手机',
			'婚礼日期',
			'婚礼地点',
			'预算',
			'地址',
			'提交月份',
			'提交日期',
			'分配顾问时间',
			'分配运营时间',
			'找商家方式'
		));

		$result = $this->listbusiness(true);
		/*var_dump($result); exit();
		if(!isset($result['info']) || !is_array($result['info']))
		{
			exit('导出失败');
		}*/
		$params = $this->input->get();

		$pagesize = !empty($params['pagesize']) ? $params['pagesize'] : 50;
		$page = ceil($result['total'] / $pagesize);

        $region_list = $this->region->getAll();
		for($i = 1; $i <= $page; $i++)
		{
			$_GET['pagesize'] = $pagesize;
			$_GET['page'] = $i;
			$business = $this->listbusiness(true);
			foreach ($business['rows'] as $b)
			{
                $tp_location_text = '';
                $tp_location = explode(',' , $b['location']);
                $tp_location_text .= !empty($tp_location[0]) ? $region_list[$tp_location[0]].' ' : '';
                $tp_location_text .= !empty($tp_location[1]) ? $region_list[$tp_location[1]].' ' : '';
                $tp_location_text .= !empty($tp_location[2]) ? $region_list[$tp_location[2]] : '';
				$exporter->addRow(array(
					$b['tradeno'],
					$b['status'],
					$b['trade_status_text'],
					$b['follower'],
					$b['operate_name'],
					$b['source'],
					$b['source_note'],
					$b['ordertype'],
					$b['usertype'],
					$b['username'],
					$b['mobile'],
					$b['wed_date'],
					$b['wed_place'],
					$b['budget'],
                    $tp_location_text,
					$b['add_date_month'],
					$b['add_date'],
                    $b['advisertime'],
					$b['operatetime'],
					$b['findtype']
				));
			}
		}
		$exporter->finalize();
        exit();
	}

	/**
	 * 修改商机交易状态
	 * @return type
	 */
	public function setTradestatus()
	{
        $this->load->model("business/common_model",'buscommon');

        $this->load->model('business/business_model', 'business');
		$inputs = $this->input->post();

		//商机id
		$bid = isset($inputs["bid"]) ? $inputs["bid"] : 0;
		$bid = intval($bid);
        $binfo = $this->business->findRow(array("id" => $bid));
		if(!$binfo)
		{
			return failure("商机不存在");
		}
		//丢单原因
		$reason = isset($inputs["status_note"]) ? $inputs["status_note"] : "";
		if(empty($reason))
		{
			return failure("请填写原因");
		}

		$upt = array(
			"trade_status" => $inputs["status"],
			"status_note" => $reason,
			"updatetime" => time(),
			);

        //获取交易状态
        list($tradestatus, $tradestatus_explan) = $this->business->getTradeStatus();
        //同步丢单状态至主站
        if($inputs["status"] == $tradestatus['discard'] && !$this->business->syncLostOrder($binfo['tradeno'] , $reason))
        {
            return failure('同步丢单状态信息失败');
        }
        elseif($inputs["status"] == $tradestatus['invalid'] && !$this->business->syncMissOrder($binfo['tradeno'] , $reason))
        {
            return failure('同步失单状态信息失败');
        }

		if($this->business->modify(array("id" => $bid), $upt))
		{
            if($inputs["status"] == $tradestatus['discard']){
                //给策划师发短信
                $shoper_ids = $this->erp_conn->where('bid',$bid)->where('status',0)->get('business_shop_map')->result_array();
                if(!empty($shoper_ids)){
                    foreach($shoper_ids as $v){
                        $ids[] = $v['shop_id'];
                    }
                    $shoper_list = $this->buscommon->shoperInfo(array('uids' => implode(',' , $ids)));
                    foreach($shoper_list['rows'] as $sval)
                    {
                        $shoper_mobiles[] = $sval['phone'];

                    }
                    //$sys_users = $this->erp_conn->select("username , mobile")->where("id", $binfo['follower_uid'])->get('erp_sys_user')->row_array();

                    $shoper_content = '您好，给您分配的新人' . $binfo['username'] . '，已经确定不再需要您提供服务。温馨提示：您可以在易结商家版客户端订单管理中查看，订单号为'.$binfo['tradeno'].'。';

                    $this->sms->send($shoper_mobiles , $shoper_content);
                 }
            }
			return success('修改成功');
		}else
		{
			return failure("修改失败");
		}
	}

	/**
	 * 批量设置商机无效（用在商机历史页）
	 */
	public function setInvalidBatch()
	{
		$this->load->model('business/business_model', 'business');
		$inputs = $this->input->post();

		//获取商机状态
        list($status, $status_explan) = $this->business->getBusinessStatus();

		if(empty($inputs["bid"]) || !is_array($inputs["bid"]))
		{
			return failure('请选择要设为无效的历史商机');
		}
		//只能设置新增的历史商机
		$cond['autowhere']['id'] = $inputs["bid"];
		$business = $this->business->getAll($cond);
		$flag = 0;
		foreach ($business as $key => $val)
		{
			if($val["status"] != $status["newadd"])
			{
				$flag = 1;
			}
			continue;
		}

		if($flag == 1)
		{
			return failure('只能设置新增的历史商机为无效商机');
		}

		$upt = array(
			"status" => $status["garbage_repeat"],
			"is_del" => 1
			);

		if($this->business->updateByCondition($upt, array("id" => $inputs["bid"])))
		{
			return success('修改成功');
		}else
		{
			return failure("修改失败");
		}
	}
}
