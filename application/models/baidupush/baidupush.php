<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 该Model封装了百度推送消息的方法
 */
class Baidupush extends MY_Model {
	/**
	 * 数据库表名
	 * @var string
	 */
	const TBL = 'bd_pushmsg';

    public function __construct(){
        parent::__construct();
    }

	/**
	 * 推送来自新人的消息入库
	 * @param array $orderInfo 订单信息
	 * @param string $orderInfo['demand_id'] 需求编号
	 * @param string $orderInfo['status'] 订单状态
	 * @param string $orderInfo['shopper_user_id'] 用户id
	 * @param string $orderInfo['id'] 订单id
	 * @param string $orderInfo['is_judge'] 是否已评价
	 * @param tinyint $pushType 推送类型 1:推送消息到某个user  2:推送消息到一个tag中的全部user 3:推送消息到该app中的全部user
	 * @param tinyint $orderType 订单类型 1:来自新人的
	 */
	public function addPush($orderInfo, $pushType) {
		if (empty($orderInfo['demand_id']) || empty($orderInfo['status']) || empty($orderInfo['shopper_user_id']) || empty($orderInfo['id'])) {
			throw new Exception("参数错误", 5);
		}
		if (empty($pushType)) {
			throw new Exception("推送类型不能为空", 5);
		}
		if (!is_numeric($pushType)) {
			throw new Exception("推送类型必须为整数", 5);
		}
		$message_db = array();
		switch ($orderInfo['status']) {
			case 11:
				$message_db['title'] = '易结推送给你的订单';
				$message_db['type'] = 1;
				$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，赶快抢单吧！';
				break;
			//为新人推送消息时放开
			// case 21:
			// 	$message['messageTitle'] = '您竞标的订单有新的状态';
			// 	$message_db['title'] = '您竞标的订单有新的状态';
			// 	$message['messageType'] = 2;
			// 	$message_db['type'] = 2;
			// 	$message['messageContent'] = '需求编号：'.$orderInfo['demand_id'].'  ';
			// 	$message['content'] = '需求编号：'.$orderInfo['demand_id'].'，赶快抢单吧！';
			// 	break;
			case 31:
				$message_db['title'] = '您竞标的订单有新的状态';
				$message_db['type'] = 2;
				$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，自荐信审核已通过';
				break;
			case 41:
                if($orderInfo['shopper_alias'] == 'wedplanners'){
                    $message_db['title'] = '易结推送给你的订单';
                    $message_db['type'] = 1;
                    $message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，赶快抢单吧！';
                }else{
                    $message_db['title'] = '您竞标的订单有新的状态';
                    $message_db['type'] = 2;
                    $message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，已通过初选';
                }
				break;
			//为新人推送消息时放开
			// case 46:
			// 	$message['messageTitle'] = '您竞标的订单有新的状态';
			// 	$message_db['title'] = '您竞标的订单有新的状态';
			// 	$message['messageType'] = 2;
			// 	$message_db['type'] = 2;
			// 	$message['messageContent'] = '需求编号：'.$orderInfo['demand_id'].'  ';
			// 	$message['content'] = '需求编号：'.$orderInfo['demand_id'].'，赶快抢单吧！';
			// 	break;
			case 51:
			case 61:
				if ($orderInfo['is_judge'] == 3) {
					$message_db['title'] = '您已中标的订单有新的评价';
					$message_db['type'] = 3;
					//获取需求者昵称
//					$uid = Demands_Order::where('id',$orderInfo['id'])->first();
					$message_db['content'] = '新人评价了需求编号为' . $orderInfo['demand_id'] . '的订单';
				} else {
					$message_db['title'] = '您竞标的订单有新的状态';
					$message_db['type'] = 2;
					$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，已中标';
				}

				break;
			default:
				# code...
				break;
		}
		if (!empty($message_db)) {
			//数据入库
			$message_db['createtime'] = time();
			$message_db['uid'] = $orderInfo['shopper_user_id'];
			$message_db['orderId'] = $orderInfo['id'];
			$message_db['orderStatus'] = $orderInfo['status'];
			$message_db['orderType'] = 1;
			$message_db['pushType'] = $pushType;
			$re = $this->ew_conn->insert(self::TBL,$message_db);
		}
	}

	/**
	 * 推送来自商家的消息入库
	 * @param array $orderInfo 订单信息
	 * @param string $orderInfo['demand_id'] 需求编号
	 * @param string $orderInfo['status'] 订单状态
	 * @param string $orderInfo['shopper_user_id'] 用户id
	 * @param string $orderInfo['id'] 订单id
	 * @param string $orderInfo['is_judge'] 是否已评价
	 * @param tinyint $pushType 推送类型 1:推送消息到某个user  2:推送消息到一个tag中的全部user 3:推送消息到该app中的全部user
	 * @param tinyint $orderType 订单类型  2:来自商家的
	 */
	public function addBusinessPush($orderInfo, $pushType) {
		if (empty($orderInfo['demand_id']) || empty($orderInfo['status']) || empty($orderInfo['shopper_user_id']) || empty($orderInfo['id'])) {
			throw new Exception("参数错误", 5);
		}
		if (empty($pushType)) {
			throw new Exception("推送类型不能为空", 5);
		}
		if (!is_numeric($pushType)) {
			throw new Exception("推送类型必须为整数", 5);
		}
		$message_db = array();
		switch ($orderInfo['status']) {
			case 11:
				$message_db['title'] = '易结推送给你的订单';
				$message_db['type'] = 1;
				$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，赶快抢单吧！';
				break;
			case 16:
				$message_db['title'] = '易结推送给你的订单';
				$message_db['type'] = 1;
				$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，有商家指定您为其服务！';
				break;
			case 31:
				$message_db['title'] = '您竞标的订单有新的状态';
				$message_db['type'] = 2;
				$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，意向书审核已通过';
				break;
			case 36:
				$message_db['title'] = '您竞标的订单有新的状态';
				$message_db['type'] = 2;
				$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，合作商确定接单';
				break;
			case 41:
				$message_db['title'] = '您竞标的订单有新的状态';
				$message_db['type'] = 2;
				$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，商家确定合作商';
				break;
			case 51:
			case 61:
				if ($orderInfo['is_judge'] == 3) {
					$message_db['title'] = '您已中标的订单有新的评价';
					$message_db['type'] = 3;
					//获取需求者昵称
//					$uid = Demands_Order::where('id',$orderInfo['id'])->first();
					$message_db['content'] = '商家评价了需求编号为' . $orderInfo['demand_id'] . '的订单';
				} else {
					$message_db['title'] = '您竞标的订单有新的状态';
					$message_db['type'] = 2;
					$message_db['content'] = '需求编号：' . $orderInfo['demand_id'] . '，商家确定完成服务';
				}
				break;
			default:
				# code...
				break;
		}
		if (!empty($message_db)) {
			//数据入库
			$message_db['createtime'] = time();
			$message_db['uid'] = $orderInfo['shopper_user_id'];
			$message_db['orderId'] = $orderInfo['id'];
			$message_db['orderStatus'] = $orderInfo['status'];
			$message_db['orderType'] = 2;
			$message_db['pushType'] = $pushType;
			$re = $this->ew_conn->insert(self::TBL,$message_db);
		}
	}
//新人招投标
    public function BaiduPushForErp($data)
    {
        $orderPush['demand_id'] =  $data['demand_id'];
        $orderPush['id'] =  $data['id'];
        $orderPush['status'] =  $data['status'];
        $orderPush['shopper_user_id'] =  $data['shopper_user_id'];
        $orderPush['is_judge']  =  '';
        $orderPush['shopper_alias'] =  $data['shopper_alias'];

        $this->addPush($orderPush, 1);
    }

    //商家招投标
    public function BaiduBusinessPushForErp($data)
    {
        $orderPush['demand_id'] =  $data['demand_id'];
        $orderPush['id'] =  $data['id'];
        $orderPush['status'] =  $data['status'];
        $orderPush['shopper_user_id'] =  $data['shopper_user_id'];
        $orderPush['is_judge']  =  '';

        $this->addBusinessPush($orderPush, 1);
    }

}
