<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Confirm extends App_Controller
{
	public function __construct(){
        parent::__construct();
		$this->load->model('contract/contract_model', 'contract');
		$this->load->model('contract/contract_ext_model', 'contract_ext');
		$this->load->model('contract/contract_payment_details_model', 'payment');
		$this->load->model('sys_user_model', 'sum');
		$this->load->model('system/department_model', 'sdm');
        $this->load->model('commons/region_model' , 'region');
        $this->load->model("business/common_model" , 'buscommon');
        $this->load->model('business/business_shop_map_model' , 'shopmap');
        $this->load->model('business/business_model' , 'business');
        $this->load->helper('functions');// 载入公共函数
        $this->load->helper('array');
    }
	
	/**
	 * 财务收款确认页面
	 */
	public function index()
	{
        //款项状态
        list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
        $this->_data['pay_status'] = $pstatus;

        $this->_data['pay_status_explan'] = $pstatus_explan;

        //支付方式
        list($mode , $mode_explan) = $this->payment->getPayMode();
        unset($mode['cash']);
        unset($mode['other']);
        $this->_data['mode'] = $mode;
        $this->_data['mode_explan'] = $mode_explan;

        //款项类型
        list($type , $type_explan) = $this->payment->getPaymentType();
        unset($type['payback']);
        $this->_data['type'] = $type;
        unset($type_explan['payback']);
        $this->_data['type_explan'] = $type_explan;

        //签约方式
        list($sign_types , $sign_types_explan) = $this->contract->getTypes();
        $this->_data['sign_types'] = $sign_types;
        $this->_data['sign_types_explan'] = $sign_types_explan;

		$this->load->view('contract/confirm/confirm',$this->_data);
	}
    /**
    * 收款确认检索列表
    * @return mixed
    */
    public function search(){
        $params = $this->input->get();
        list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();

        $conditions = array();

        if(!empty($params['balance_status'])){
            $conditions['status'] = $pstatus['to_confirm'];
            //款项状态
            if($params['balance_status'])
            {
                $conditions['status'] = intval($params['balance_status']);
            }
        }

        //支付方式
        if($params['pay_by'])
        {
            $conditions['pay_mode'] = $params['pay_by'];
        }

        //签约方式
        if($params['sign_type'])
        {
            $conditions['type'] = $params['sign_type'];
        }

        //合同编码号
        if($params['contractno'])
        {
            $conditions['contract_num'] = $params['contractno'];
            $bid = $this->erp_conn->select("bid")->where("contract_num",$params['contractno'])->get("sign_contract")->row_array();
            if(isset($bid['bid'])){
                $conditions['ew_sign_contract_payment_details.bid'] = $bid['bid'];
            }
        }
        //商家名称
        if($params['nickname'])
        {
            // $conditions['shopper_name'] = $params['nickname'];
            $conditions['shopper_name' . ' like'] = '%'.$params['nickname'].'%';
        }
        //两个表里的creat_time 是一样的
        if($params['create_time_start'])
        {
            $conditions['ew_sign_contract_payment_details.create_time >='] = strtotime($params['create_time_start']);
        }

        if($params['create_time_end'])
        {
            $conditions['ew_sign_contract_payment_details.create_time <='] = strtotime($params['create_time_end']);
        }
        // 分页处理
        $limit = array();
        if(!empty($params['pagesize']) && !empty($params['page']))
        {
            if(is_numeric($params['pagesize']) && is_numeric($params['page']))
            {

                $start = ($params['page'] - 1) * $params['pagesize'];
                $limit = array('nums' => $params['pagesize'], 'start' => $start);
            }
        }
        $sel_fields = '* ,ew_sign_contract_payment_details.create_time as pay_c_time,ew_sign_contract_payment_details.id as cid';
        $total = $this->payment->findPaymentJoinExtra($conditions , array() , true);
        $data = $this->payment->findPaymentJoinExtra($conditions ,$limit , false, $sel_fields);

        $formatData = $this->_getData($data);
        $info = array(
            'total' => $total,
            'rows' => $formatData
        );
        return success($info);
    }
    public function _getData($data){
        //款项状态
        list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
        //支付方式
        list($mode , $mode_explan) = $this->payment->getPayMode();
        //款项类型
        list($type_fund , $type_explan) = $this->payment->getPaymentType();
        //签约方式
        list($sign_types , $sign_types_explan) = $this->contract->getTypes();
        $sign_types_flip = array_flip($sign_types);
        $tstatus = array_flip($pstatus);
        $mode = array_flip($mode);
        $type = array_flip($type_fund);
        //商家信息
        foreach($data as $k =>$v){
            $shopper_ids[] = $v['shopper_id'];

            //整理商机id
            if($v['bid'] > 0)
            {
                $bids[] = $v['bid'];
            }
        }
        if(empty($shopper_ids)){
            $uids = '';
        }else{
            $uids = implode(',',$shopper_ids);
        }

        $shopers = $this->buscommon->shoperInfo(array('uids' => $uids));
        $shop =  toHashmap($shopers['rows'],'uid');

        $sys_user_info = $this->user->findUsers();
        if(count($bids) > 0)
        {
            $bus_info = $this->business->findAll(array('id' => $bids) , array() , array() ,'id , operate_uid');
            foreach($bus_info as $bus)
            {
                if($bus['operate_uid'] > 0)
                {
                    $bid_opuid[$bus['id']] = $bus['operate_uid'];
                }
            }
        }


       //查询中间合同的信息 ext
        $c_id = $s_id =array();
        foreach($data as $k =>$v){
            if($v['fund_type'] == $type_fund["shoper_fund"] || $v['fund_type'] == $type_fund["sitelayout_fund"] || $v['fund_type'] == $type_fund["addition_fund"]){
                $s_id[] = $v['sid'];
                $c_id[] = $v['cid'];
            }
        }
        if(!empty($s_id)){
            $contract_ext = $this->erp_conn->select("id , number_img , sign_img ,amount")->where_In("id", $s_id)->get('sign_contract_ext')->result_array();
            $con_ext =  toHashmap($contract_ext,'id');
        }

        if(!empty($c_id)){
            $ext_amount = $this->erp_conn->select("cid , amount")->where_In("cid", $c_id)->get('sign_contract_ext')->result_array();
            $cont_ext_amount =  toHashmap($ext_amount,'cid');

            $pay_amount = $this->erp_conn->select("cid , amount")->where('fund_type !=',$type_fund['final_fund'])->where_In("cid", $c_id)->get('sign_contract_payment_details')->result_array();
            $cont_pay_amount =  toHashmap($pay_amount,'cid');
        }


        $sys_uids = $shopper_id = array();
        foreach($data as $key => &$order)
        {
            $uid = $order['shopper_id'];
            $sid = $order['sid'];
            $cid = $order['cid'];
            $shoper = isset($shop[$uid]) ? $shop[$uid] : '';
            $cont_ext = isset($con_ext[$sid]) ? $con_ext[$sid] : '';
            $cont_ext_amount = isset($cont_ext_amount[$cid]) ? $cont_ext_amount[$cid] : ''; //ext1
            $cont_pay_amount = isset($cont_pay_amount[$cid]) ? $cont_pay_amount[$cid] : '';//ext2
            if(!empty($shoper)){
                    $order['shoper_phone'] =$shoper['phone'];
                    $order['shoper_studio_name'] =$shoper['studio_name'];
                    $order['shoper_nickname'] =$shoper['nickname'];
            }else{
                $order['shoper_phone'] ='';
                $order['shoper_studio_name'] ='';
                $order['shoper_nickname'] ='';
            }
            if($order['fund_type'] == $type_fund["advance"] || $order['fund_type'] == $type_fund["shoper_fund"] || $order['fund_type'] == $type_fund["sitelayout_fund"]|| $order['fund_type'] == $type_fund["addition_fund"]){  //收支类型
                $order['case'] = 1;//确认收款
            }elseif($order['fund_type'] == $type_fund["final_fund"]){
                $order['case'] = 2;//尾款
            }else{
                $order['case']='';//空或者是回款
            }

            $order['shoper_alias'] ='策划师';
            $sys_uids[] = $order['sys_uid'];
            $order['pay_status'] = isset($order['status']) ? $order['status'] : '';
            $order['status'] = isset($tstatus[$order['status']]) ? $pstatus_explan[$tstatus[$order['status']]] : ''; //款项状态
            $order['pay_mode'] = isset($mode[$order['pay_mode']]) ? $mode_explan[$mode[$order['pay_mode']]] : ''; //支付方式
            if(empty($order['fund_describe'])){
                $order['fund_type'] = isset($type[$order['fund_type']]) ? $type_explan[$type[$order['fund_type']]] : ''; //款项类型
            }else{
                $order['fund_type'] = isset($order['fund_describe']) ? $order['fund_describe'] : ''; //款项类型
            }
//            $order['fund_type'] = isset($type[$order['fund_type']]) ? $type_explan[$type[$order['fund_type']]] : ''; //款项类型
//            $order['fund_type'] .= '-' . $order['fund_describe'];
            //处理时间
            if($order['update_time']==0){
                $order['update_time']='-';
            }else{
                $order['update_time'] = date('Y-m-d H:i:s',$order['update_time']);
            }
            //提交时间
            if($order['pay_c_time']==0){
                $order['pay_c_time']='-';
            }else{
                $order['pay_c_time'] = date('Y-m-d H:i:s',$order['pay_c_time']);
            }
            //到账时间
            if($order['pay_time']==0){
                $order['pay_time']='-';
            }else{
                $order['pay_time'] = date('Y-m-d H:i:s',$order['pay_time']);
            }
            //婚礼日期
            if($order['wed_date']==0){
                $order['wed_date']='-';
            }else{
                $order['wed_date'] = date('Y-m-d',$order['wed_date']);
            }
            $img = 'http://sa.easywed.cn/res/images/noimg.png';
            $order["voucher_img"] = $order["voucher_img"] ? get_oss_image($order["voucher_img"]).'@500w.jpg' :$img;
            //合同信息里的图片
            if($order['fund_type'] == $type_explan["shoper_fund"] || $order['fund_type'] == $type_explan["sitelayout_fund"] || $order['fund_type'] == $type_explan["addition_fund"]){
                $order["sign_img"] = !empty($cont_ext["sign_img"]) ? get_oss_image($cont_ext["sign_img"]).'@500w.jpg' : $img;
                $order["number_img"] = !empty($cont_ext["number_img"]) ? get_oss_image($cont_ext["number_img"]).'@500w.jpg' : $img;

            }else{

                $order["sign_img"] = $order["sign_img"] ? get_oss_image($order["sign_img"]).'@500w.jpg' : $img;
                $order["number_img"] = $order["number_img"] ? get_oss_image($order["number_img"]).'@500w.jpg' : $img;

            }

            //合同信息里的金额
            if($order['fund_type'] == $type_explan["shoper_fund"] || $order['fund_type'] == $type_explan["sitelayout_fund"] || $order['fund_type'] == $type_explan["addition_fund"]){
                $order["sitelayout_fund"] = $type_explan["sitelayout_fund"];

                $order["contract_amount"] = isset($cont_ext['amount']) ? $cont_ext['amount'] : "";
            }else if($order['fund_type'] == $type_explan["final_fund"]){
                $order['contract_amount'] = isset($order["amount"]) ? $order["amount"] : "";
            }else{
                $order["contract_amount"] = isset($order["wed_amount"]) ? $order["wed_amount"] : "";
            }

            $shopper_id[]= $order['shopper_id'];
            //婚礼地点
            $region_list = $this->region->getAll();
            $tp_location_text = '';
            $tp_location = explode(',' , $order['wed_location']);
            $tp_location_text .= isset($tp_location[1]) ? $region_list[$tp_location[1]].'-' : '';
            $tp_location_text .= isset($tp_location[2]) ? $region_list[$tp_location[2]] : '';
            $tp_location_text = $order["wed_place"] ? $tp_location_text.'-'.$order["wed_place"] : '';
            $order["wed_location"] = $tp_location_text;
            //签约方式
            $order['sign_type'] = isset($sign_types_flip[$order['type']]) ? $sign_types_explan[$sign_types_flip[$order['type']]] : '';
            //运营
            if($order['bid'] > 0 && isset($bid_opuid[$order['bid']]) && isset($sys_user_info[$bid_opuid[$order['bid']]]))
            {
                $order['operator'] = $sys_user_info[$bid_opuid[$order['bid']]]['username'];
            }
        }
        if(count($sys_uids) > 0)
        {
            $sys_users = $this->user->findUsers(array('id' => $sys_uids));
        }
        foreach($data as $key => &$order)
        {
            $order['follow_username'] = isset($sys_users[$order['sys_uid']]) ? $sys_users[$order['sys_uid']]['username'] : '-';

        }
        return $data;
    }

    /**
     * 确认收款
     */
    public function money()
    {
        $params = $this->input->post();
        $payment_id = $params['cid'];
        $pay_time = strtotime($params['pay_time']);
        $sys_uid = $this->session->userdata("admin_id");

        //获取款项状态
        list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
        //获取合同款项状态
        list($fstatus , $fstatus_explan) = $this->contract->getFundStatus();

        if($pay_time > time())
        {
            return failure('到账时间不能大于当前时间');
        }

        $payment_info = $this->payment->findByCondition(array('id' => $payment_id));
        if(!isset($payment_info['id']))
        {
            return failure('款项信息不存在');
        }

        $contract_info = $this->contract->findByCondition(array('id' => $payment_info['cid']));
        if(!isset($contract_info['id']))
        {
            return failure('合同信息不存在');
        }

        if($payment_info['status'] != $pstatus['to_confirm'])
        {
            return failure('款项信息状态不正确');
        }

        $post_params = array(
            'contract_num' => $contract_info['contract_num'],
            'payment_id' => $payment_info['ew_pay_id'],
            'status' => 1,
            'reason' => ''
        );

        $resp = $this->payment->syncPaymentStatus($post_params);
        if($resp)
        {
            //更新款项状态
            $udp_params = array(
                'pay_time' => $pay_time,
                'status' => $pstatus['confirmed'],
                'sys_uid' => $sys_uid,
                'update_time' => time(),
            );
            $this->payment->updateByCondition($udp_params , array('id' => $payment_info['id']));
            //更新合同状态
            if($contract_info['funds_status'] < $fstatus['paid_advance'])
            {
                $this->contract->updateByCondition(array('funds_status' => $fstatus['paid_advance']) , array('id' => $contract_info['id']));
            }
            return success('操作成功');
        }
        return failure('操作失败');
    }

    /**
     * 驳回款项
     */
    public function rejected()
    {
        $params = $this->input->post();
        $payment_id = $params['cid'];
        $reason = $params['reason'];
        $sys_uid = $this->session->userdata("admin_id");

        //获取款项状态
        list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();

        if(empty($reason))
        {
            return failure('驳回内容不能为空');
        }

        $payment_info = $this->payment->findByCondition(array('id' => $payment_id));
        if(!isset($payment_info['id']))
        {
            return failure('款项信息不存在');
        }

        $contract_info = $this->contract->findByCondition(array('id' => $payment_info['cid']));
        if(!isset($contract_info['id']))
        {
            return failure('合同信息不存在');
        }

        if($payment_info['status'] != $pstatus['to_confirm'])
        {
            return failure('款项信息状态不正确');
        }

        $business_info = $this->business->findByCondition(array('id' => $contract_info['bid']));
        if(!isset($business_info['id']))
        {
            return failure('商机信息不存在');
        }

        $post_params = array(
            'contract_num' => $contract_info['contract_num'],
            'payment_id' => $payment_info['ew_pay_id'],
            'status' => 0,
            'reason' => $reason
        );

        $resp = $this->payment->syncPaymentStatus($post_params);
        if($resp)
        {
            //更新款项状态
            $udp_params = array(
                'status' => $pstatus['reject'],
                'sys_uid' => $sys_uid,
                'update_time' => time(),
            );
            $this->payment->updateByCondition($udp_params , array('id' => $payment_info['id']));

            //发送短信给商家
            $shopper_mobile = '';
            $shoper_list = $this->buscommon->shoperInfo(array('uids' =>$contract_info['shopper_id']));
            foreach($shoper_list['rows'] as $sval)
            {
                $shopper_mobile = $sval['phone'];
            }

            if($shopper_mobile)
            {
                $content = '您好，新人' . $contract_info['username'] . '（订单号：'.$business_info['tradeno'].'）支付的金额'.$payment_info['amount'].'元，与您提交的合同或附件不符。请核实后重新提交。';
                $this->sms->send(array($shopper_mobile) , $content);
            }
            return success('操作成功');
        }
        return failure('操作失败');
    }

    //确认收款(废弃)
    public function money_1(){
        $arr_get = $this->input->post();
        $id = $arr_get['cid'];
        $pay_time=strtotime($arr_get['pay_time']);
        if($pay_time > time()){
            return failure('到账时间不能大于当前时间');
        }
        $uid = $this->session->userdata("admin_id");
        $fund_status = list($type , $type_explan) = $this->payment->getPaymentType();
        //查出信息
        $conditions = array('ew_sign_contract_payment_details.id'=>$id);
        $data = $this->payment->findPaymentJoinExtra($conditions ,array() , false);
        if(!empty($data)){
            foreach($data as $k=>$v){
                $contract_num = $v['contract_num'];
                $ew_pay_id = $v['ew_pay_id'];
                $fund_type = $v['fund_type'];
                $sign_id = $v['cid'];//签约表的id
                $b_id = $v['bid'];//商机id
                $shopper_id = $v['shopper_id'];//商家id
                $funds_status = $v['funds_status'];
                $username = $v['username'];
                $mobile = $v['mobile'];
                $amount = $v['amount'];

            }
            $fund_key = array_flip($type);
            if(array_key_exists($fund_type,$fund_key)){
                $name = $fund_key[$fund_type];
                $val_name = $type_explan[$name];
            }
            $payment = $this->erp_conn->select("amount")->where("bid", $b_id)->get('sign_contract_payment_details')->row_array();
            $content = '您好，易结网已成功托管'.$username.'支付的'.$val_name.''.$amount.'元，感谢您使用易结网。如有疑问，请咨询您的策划师或拨打4006-054-520进行咨询。';

            //给已丢单的策划师发短信

            $shop_ids = $this->erp_conn->select("shop_id")->where('bid', $b_id)->where('shop_id !=', $shopper_id)->where('status !=',2)->get('ew_business_shop_map')->result_array();
            //调用主站接口
            $params = array('contract_num'=>$contract_num,'status'=>2,'ew_pay_id'=>$ew_pay_id);

            if($fund_type == $type['advance']){//定金
                $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/confirm-earnest-money', $params);
                $ret = json_decode($ret,true);
                if($ret['result'] == 'succ') {
                    list($status , $status_explan) = $this->contract->getContractStatus();
                    list($fstatus , $funds_explan) = $this->contract->getFundStatus();
                    //修改此商家确认订单  20  60,不修改返款状态，继续为空
                    $this->erp_conn->where('id', $sign_id)->update("ew_sign_contract", array('contract_status' => $status['confirmed'], 'sign_time' => time()));
                    //修改相同商机其他商家的签约信息为 无效 60
                     $this->erp_conn->where('bid', $b_id)->where('id !=', $sign_id)->update("ew_sign_contract", array('contract_status' =>$status['invalid'], 'sign_time' => time()));
                    //修改相同商机不同cid 并且款项类型为定金的款项状态改为已驳回

                    list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();//款项状态
                    list($mode , $mode_explan) = $this->payment->getPayMode();//支付方式
                    list($ptypes , $ptypes_explan) = $this->payment->getPaymentType();//款项类型
                    $this->erp_conn->where('bid', $b_id)->where('cid !=' ,$sign_id)->where('fund_type',$ptypes['advance'])->update("ew_sign_contract_payment_details", array('status' => $pstatus['reject'],'refuse_reason'=>'驳回'));
                    //修改签约商家的分单状态为已签约 1 且已见面 2
                    $this->erp_conn->where('bid', $b_id)->where('shop_id =', $shopper_id)->update("ew_business_shop_map", array('status' => Business_shop_map_model::STATUS_SIGN,'face_status'=>Business_shop_map_model::FACE_STATUS_MEET));
                    //修改相同商机其他商家的状态为已丢单  2
                    $this->erp_conn->where('bid', $b_id)->where('shop_id !=', $shopper_id)->update("ew_business_shop_map", array('status' => Business_shop_map_model::STATUS_LOST));
                    //修改商机交易状态为已成单 3
                    list($trade , $trade_explan) = $this->business->getTradeStatus();
                    $this->erp_conn->where('id', $b_id)->update("ew_business", array('trade_status' => $trade['ordered'],'updatetime' => time()));

                    $arr = array('status' => $pstatus['confirmed'],'pay_time'=>$pay_time,'update_time'=>time(),'sys_uid'=>$uid);
                    $result = $this->payment->findCid($id,$arr);
                    if($result){
                        //给新人发短信
                        $this->sms->send(array($mobile) , $content);

                        //给自动丢单的策划师发短信
                        if(!empty($shop_ids)){
                            $ids = array();
                            foreach($shop_ids as $v){
                                $ids[] = $v['shop_id'];
                            }
                            $uids = implode(',' , $ids);
                            $bussiness_shop = $this->erp_conn->select("tradeno , follower_uid")->where("id", $b_id)->get('business')->row_array();

                            $sys_users = $this->erp_conn->select("username , mobile")->where("id", $bussiness_shop['follower_uid'])->get('erp_sys_user')->row_array();

                            $shoper_list = $this->buscommon->shoperInfo(array('uids' => $uids));
                            $shoper_mobiles = array();
                            foreach($shoper_list['rows'] as $sval)
                            {
                                $shoper_mobiles[] = $sval['phone'];

                            }

                            $shoper_content = '您好，给您分配的新人' . $username . '，已经确定不再需要您提供服务。温馨提示：您可以在易结商家版客户端订单管理中查看，订单号为'.$bussiness_shop['tradeno'].'。';

                            $this->sms->send($shoper_mobiles , $shoper_content);
                        }
                        return success('确认成功');
                    }else{
                        return failure('确认失败');
                    }
                }else{
                    return failure($ret['msg']);
                }
                //echo $this->erp_conn->last_query();
            }elseif($fund_type == $type['shoper_fund']){//服务款项
                $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/confirm-shoper-money', $params);
                $ret = json_decode($ret,true);
                if($ret['result'] == 'succ'){
                    list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
                    $arr = array('status' => $pstatus['confirmed'],'pay_time'=>$pay_time,'update_time'=>time(),'sys_uid'=>$uid);
                    $result = $this->payment->findCid($id,$arr);
                    if($result){
                        $this->sms->send(array($mobile) , $content);
                        return success('确认成功');
                    }else{
                        return failure('确认失败');
                    }
                }else{
                    return failure($ret['msg']);
                }
            }elseif($fund_type == $type['sitelayout_fund']){//场地布置
                $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/confirm-sitelayout-money', $params);
                $ret = json_decode($ret,true);
                if($ret['result'] == 'succ'){
                    list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
                    $arr = array('status' => $pstatus['confirmed'],'pay_time'=>$pay_time,'update_time'=>time(),'sys_uid'=>$uid);
                    $result = $this->payment->findCid($id,$arr);
                    if($result){
                        $this->sms->send(array($mobile) , $content);
                        return success('确认成功');
                    }else{
                        return failure('确认失败');
                    }
                }else{
                    return failure($ret['msg']);
                }
            }elseif($fund_type == $type['final_fund']){//尾款
                $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/confirm-final-money', $params);
                $ret = json_decode($ret,true);
                if($ret['result'] == 'succ') {
                    list($fstatus , $funds_explan) = $this->contract->getFundStatus();
                    //确认尾款时，当返款状态为已确认时把返款状态改为待首次返款
                    if($funds_status < $fstatus['first_back']){ //20
                         $this->erp_conn->where('id', $sign_id)->update("ew_sign_contract", array('funds_status' => $fstatus['first_back']));
                    }
                    list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
                    $arr = array('status' => $pstatus['confirmed'],'pay_time'=>$pay_time,'update_time'=>time(),'sys_uid'=>$uid);
                    $result = $this->payment->findCid($id,$arr);
                    if($result){
                        $this->sms->send(array($mobile) , $content);
                        return success('确认成功');
                    }else{
                        return failure('确认失败');
                    }
                }else{
                    return failure($ret['msg']);
                }
            }

        }

    }
    //驳回(废弃)
    public function rejected_1(){
        $arr_get = $this->input->post();
        $id = $arr_get['cid'];
        if(empty($arr_get['reason'])){
            return failure('驳回内容不能为空');
        }
        $reason = $arr_get['reason'];
        $id = $arr_get['cid'];
        $uid = $this->session->userdata("admin_id");
        list($type , $type_explan) = $this->payment->getPaymentType();
        $conditions = array('ew_sign_contract_payment_details.id'=>$id);
        $data = $this->payment->findPaymentJoinExtra($conditions ,array() , false);
        //print_r($data);

        if(!empty($data)){
            $contract_id = 0;
            foreach($data as $k=>$v){
                $contract_id = intval($v['cid']);
                $contract_num = $v['contract_num'];
                $ew_pay_id = $v['ew_pay_id'];
                $fund_type = $v['fund_type'];
                $username = $v['username'];
                $amount = $v['amount'];
                $shoper_id = $v['shopper_id'];
                $b_id = $v['bid'];
            }

            $fund_key = array_flip($type);
            if(array_key_exists($fund_type,$fund_key)){
                $name = $fund_key[$fund_type];
                $val_name = $type_explan[$name];
            }

            //调用主站接口
            $params = array('contract_num'=>$contract_num,'status'=>0,'ew_pay_id'=>$ew_pay_id,'reason'=>$reason);
            if($fund_type == $type['advance']){//定金
                $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/confirm-earnest-money', $params);
            }elseif($fund_type == $type['shoper_fund']){//服务款项
                $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/confirm-shoper-money', $params);
            }elseif($fund_type == $type['sitelayout_fund']){//场地布置
                $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/confirm-sitelayout-money', $params);
            }elseif($fund_type == $type['final_fund']){//尾款
                $ret = $this->curl->post($this->config->item('ew_domain').'erp/contract/confirm-final-money', $params);
            }
            $ret = json_decode($ret,true);
            //print_r($ret);die;
            if($ret['result'] == 'succ'){
                //如果定金驳回成功，则把此定金所属签约信息状态改为已驳回
                if($fund_type == $type['advance'])
                {
                    if($contract_id > 0)
                    {
                        //合同状态
                        list($contractstatus, $contractstatus_explan) = $this->contract->getContractStatus();
                        $this->contract->updateByCondition(array('contract_status' => $contractstatus['reject']) , array('id' => $contract_id));
                    }
                }
                //如果尾款驳回成功，则把此尾款所属签约信息下的所有未驳回的增补合同状态改为已驳回
                elseif($fund_type == $type['final_fund'])
                {
                    //中间合同状态
                    list($contract_ext_status, $_) = $this->contract_ext->getContractExtStatus();
                    //合同类型
                    list($contracttype, $contracttype_explan) = $this->contract_ext->getContractType();
                    if($contract_id > 0)
                    {
                        //更新此合同下所有未驳回的增补合同的状态为已驳回
                        $this->contract_ext->updateByCondition(array('c_status' => $contract_ext_status['reject']) , array('cid' => $contract_id , 'contract_type' => $contracttype['addition'] , 'c_status' => $contract_ext_status['submmited']));
                        //更新签约表的优惠金额为0
                        $this->contract->updateByCondition(array('discount_amount' => 0) , array('id' => $contract_id));
                    }
                }
                list($pstatus , $pstatus_explan) = $this->payment->getPaymentStatus();
                $arr = array('status' => $pstatus['reject'],'refuse_reason'=>$reason,'update_time'=>time(),'sys_uid'=>$uid);
                $result = $this->payment->findCid($id,$arr);
                if($result){
                    if(!empty($shoper_id)){
                        $bussiness = $this->erp_conn->select("tradeno")->where("id", $b_id)->get('business')->row_array();
                        $tradeno = $bussiness['tradeno'];
                        $shoper_list = $this->buscommon->shoperInfo(array('uids' =>$shoper_id));
                        foreach($shoper_list['rows'] as $sval)
                        {
                            $shoper_mobiles = $sval['phone'];

                        }
                        if($fund_type == $type['advance']){

                            $content = '您好，新人'.$username.'（订单号：'.$tradeno.'）与您签订的婚礼服务合同被驳回，原因是'.$reason.'。请核实后重新提交。';
                        }else{
                            $content = '您好，新人'.$username.'（订单号：'.$tradeno.'）支付的'.$val_name.'金额'.$amount.'元，与您提交的合同或附件不符。请核实后重新提交。';
                        }
                        $this->sms->send(array($shoper_mobiles) , $content);
                    }

                    return success('驳回成功');
                }else{
                    return failure('驳回失败');
                }

            }else{
                return failure($ret['msg']);
            }
        }

    }

    /*
     * 确认或驳回双方合同
     * contract_id:(array)
     * status:(boolean)
     */
    public function confirmBoth()
    {
        $input = $this->input->post();
        if(!isset($input['contract_id']) || !isset($input['status'])){
            return failure("params error!");
        }
        $contract = $this->erp_conn->where_in("id",$input['contract_id'])->get("sign_contract")->result_array();
        if(!isset($contract[0])){
            return failure("contract not exists!");
        }
        if($contract[0]['type'] != 2){
            return failure("contract is not 二方合同！");
        }
        if($input['status'] == 2 && (!isset($input['memo_text']) || empty($input['memo_text'])))
        {
            return failure('请输入拒绝原因');
        }
        list($status , $status_explan) = $this->contract->getContractStatus();
        list($trade , $trade_explan) = $this->business->getTradeStatus();
        switch ($input['status']) {
            case '1':
                $status = $status['confirmed'];
                break;
            case "0":
                $status = $status['reject'];
                break;
            default:
                break;
        }
        $post_params = array("contract_num"=>$contract[0]['contract_num'],"status"=>$input['status']);

        if($input['status'] == 0)
        {
            $post_params['close_reason'] = isset($input['memo_text']) ? $input['memo_text'] : '';
        }

        $re = $this->curl->post($this->config->item('ew_domain').'/erp/contract/confirm-contract', $post_params);
        $response = json_decode($re,true);
        if(!isset($response['result']) || $response['result'] != "succ"){
            return failure("主站接口错误！");
        }
        $data = array('contract_status' => $status,"sign_time" => time());
        $res = $this->erp_conn->where_in("id",$input['contract_id'])->where("type",2)->update("sign_contract",$data);

        //更新shop_map其中签约的商家的状态
        foreach($contract as $k => $v){
            //更新商机交易状态
            $trade_status = $this->erp_conn
                ->where("id",$v['bid'])
                ->update("business",array("trade_status" => $trade['ordered'],"updatetime" => time()));
            //未见面的更新shop_map状态和见面状态，见面时间
            $shop_map_no_face = $this->erp_conn
                ->where(array("bid" => $v['bid'],"shop_id" => $v['shopper_id'],"face_status" => 1))
                ->update("business_shop_map",array("status"=>1,"face_status"=>2,"facetime"=>time()));
            //见面的只更新状态
            $shop_map_faced = $this->erp_conn
                ->where(array("bid" => $v['bid'],"shop_id" => $v['shopper_id'],"face_status" => 2))
                ->update("business_shop_map",array("status"=>1));
            //其他的商家置为丢单，并记录丢单时间
            $other_shop_map = $this->erp_conn
                ->where(array("bid" => $v['bid'],"shop_id !=" => $v['shopper_id']))
                ->update("business_shop_map",array("status"=>2,"losttime" => time()));
        }
        if($res == true){
            return success("success!");
        }else{
            return failure("failure!");
        }
    }


}