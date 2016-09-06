<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
---- 保存在线签约信息
---- add by zhangmiao
---- 
 */
class Contract extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('demand/demand_contract_model','contract');//旧版在线签约，数据库存在erp中
        $this->load->model('demand/sign_contract_model','sign');//新版在线签约，数据库存在ew中
    }


    /*
     *
     */
    public function index()
    {

    }

    /*
     * 保存在线签约信息
     */
    public function saveContract()
    {
        $input = $this->input->post();
        if(!isset($input['demand_id']) || empty($input['demand_id']))
        {
            echo json_encode(array('result' => 'fail','info' => 'demand_id参数不正确'));exit;
        }
        if(empty($input['money']) || empty($input['wed_date']) || empty($input['wed_province']) || empty($input['wed_place']))
        {
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        if(!$this->checkCode($input['contract_code']))//检查签约号是否合法
        {
            echo json_encode(array('result' => 'fail','info' => '签约号不合法'));exit;
        }
        //根据需求id获取该需求下中标的订单
        $order_id = $this->contract->getOrderByDemand($input['demand_id']);
        if(count($order_id) != 1)//无中标的订单或者中标的超过一条
        {
            echo json_encode(array('result' => 'fail','info' => '订单数据错误'));exit;
        }
        $data['demand_id'] = $input['demand_id'];
        $data['order_id'] = $order_id[0]['id'];
        $data['contract_code'] = $input['contract_code'];
        $data['money'] = floatval($input['money']);
        $data['wed_date'] = $input['wed_date'];
        $data['wed_location'] = $input['wed_country'].','.$input['wed_province'].','.$input['wed_city'];
        $data['wed_place'] = trim($input['wed_place']);
        $data['comment'] = isset($input['comment']) ? trim($input['comment']) : '';
        $contract_item = $this->contract->getContract($data);
        if(empty($contract_item))
        {//没有当前信息要插入一条
            if(!$this->checkCodeExist($data['contract_code'])){//检查签约号是否存在
                echo json_encode(array('result' => 'fail','info' => '签约号已存在，请更换'));exit;
            }
            $res = $this->contract->insertOneContract($data);

            //修改"商家列表页和搜索页数据"表的turnover成交量的值+1
            $order_info = $this->ew_conn->where('id', $data['order_id'])->get('demand_order')->row_array();

            $shoper_list_info = $this->ew_conn->select('uid, turnover')->where('uid',$order_info['shopper_user_id'])->get('shoper_list')->row_array();

            if(!empty($shoper_list_info))
            {
                $param['turnover'] = $shoper_list_info['turnover'] +1;
                $this->ew_conn->where('uid',$shoper_list_info['uid'])->update('shoper_list',$param);
            }
        }
        else
        {//存在该签约要更新
            //检查签约号是否存在（更新的时候不能检查当前的contract_code）
            if(!$this->checkCodeExist($data['contract_code'],$contract_item[0]['demand_id'])){
                echo json_encode(array('result' => 'fail','info' => '签约号已存在，请更换'));exit;
            }
            $res = $this->contract->updateOneContract($data);
        }
        //保存签约信息的时候同时更新需求表中的数据
        $res_ew = $this->contract->updateDemand($data);
        if($res == true && $res_ew === true)
        {
            echo json_encode(array('result' => 'succ','info' => '更新成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '更新失败'));exit;
        }
    }

    /*
     * 检查contract_code是否合法
     */
    public function checkCode($code)
    {
        $flag = preg_match ("/^[A-Za-z0-9-_:]+$/", $code, $m);
        return $flag;
    }

    /*
     * 检查contract_code是否存在
     */
    public function checkCodeExist($code,$demand_id='')
    {
        $res = $this->contract->checkCodeExist($code,$demand_id);
        return $res;
    }



/*==================================================华丽的分割线======================================================*/


    /*
     * 获取合作商列表
     */
    public function getContractPartner()
    {
        $input = $this->input->get();
        if(!isset($input["sid"]) && !isset($input["demand_id"])){
            echo json_encode(array('result' => 'fail','info' => '缺少需求id'));exit;
        }
        $result = $this->sign->getPartnerList($input);
        return success($result);
    }

    /*
     * 保存修改的合作商
     * $shopper_info['shopper_uid']:商家uid
     * $shopper_info['shopper_alias']:商家别名
     */
    public function saveContractPartner()
    {
        // $input = array(
        //         "id" => 1748, 
        //         "param" => array(
        //                 "wedmaster" => array(23,24),
        //                 "makeup" => array(23,24),
        //                 "wedphotoer" => array(23,24),
        //                 "wedvideo" => array(23,24),
        //                 "sitelayout" => array(23,24),
        //             ),
        //         );
        $input = $this->input->post();
        if(!isset($input["sid"])){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        if(!isset($input["param"]) || empty($input["param"])){
            echo json_encode(array('result' => 'succ','info' => '没有添加合作商'));exit;
        }
        $input["param"] = json_decode($input["param"],true);
        // $demand_contract = $this->sign->getContractByDemandid($input);
            if(isset($input["param"]["wedmaster"])){
                foreach($input["param"]["wedmaster"] as $k => $v){
                    $add_data['cid'] = $input['sid'];
                    $add_data['shopper_uid'] = $v;
                    $add_data['shopper_alias'] = "wedmaster";
                    $this->sign->addPartner($add_data);
                }
            }
            if(isset($input["param"]["makeup"])){
                foreach($input["param"]["makeup"] as $k => $v){
                    $add_data['cid'] = $input['sid'];
                    $add_data['shopper_uid'] = $v;
                    $add_data['shopper_alias'] = "makeup";
                    $this->sign->addPartner($add_data);
                }
            }
            if(isset($input["param"]["wedphotoer"])){
                foreach($input["param"]["wedphotoer"] as $k => $v){
                    $add_data['cid'] = $input['sid'];
                    $add_data['shopper_uid'] = $v;
                    $add_data['shopper_alias'] = "wedphotoer";
                    $this->sign->addPartner($add_data);
                }
            }
            if(isset($input["param"]["wedvideo"])){
                foreach($input["param"]["wedvideo"] as $k => $v){
                    $add_data['cid'] = $input['sid'];
                    $add_data['shopper_uid'] = $v;
                    $add_data['shopper_alias'] = "wedvideo";
                    $this->sign->addPartner($add_data);
                }
            }
            if(isset($input["param"]["sitelayout"])){
                foreach($input["param"]["sitelayout"] as $k => $v){
                    $add_data['cid'] = $input['sid'];
                    $add_data['shopper_uid'] = $v;
                    $add_data['shopper_alias'] = "sitelayout";
                    $this->sign->addPartner($add_data);
                }
            }
        echo json_encode(array('result' => 'succ','info' => '添加成功'));exit;
    }


    /*
     * 取消合作
     * $id:需求id
     * $shopper_uid:商家uid（字符串，逗号分隔）
     */
    public function delContractPartner()
    {
        // $input = array("id" => 1748,"shopper_uid" => "23,55");
        $input = $this->input->post();
        if(!isset($input["sid"]) || !isset($input["shopper_uid"]) || !in_array($input["cooperation_status"], array(1,2))){
            echo json_encode(array('result' => 'fail','info' => '参数错误'));exit;
        }
        $result = $this->sign->delPartner($input);
        if($result == true)
        {
            echo json_encode(array('result' => 'succ','info' => '移除成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '移除失败'));exit;
        }
    }


}