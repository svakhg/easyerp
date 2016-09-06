<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
  # 财务收支管理
  # add by zhangmiao
  # 
 */
class Inandout extends App_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("demand/demand_trade_payment","payment");
        $this->load->model("demand/demand_followup_model","followup");
        $login_uid = $this->session->userdata('admin_id');//当前登陆用户id
        if(isset($login_uid) && !empty($login_uid)){
            $login_userinfo = $this->erp_conn->where('id',$login_uid)->get("ew_erp_sys_user")->result_array();
            $login_username = $login_userinfo[0]['username'];
        }else{
            $login_username = "";
        }
    }

    public function index()
    {
        $this->_data['pay_set'] = $this->payment->get_pay_set_id();
        $this->load->view("finance/finance_search_view",$this->_data);
    }

    /*
     * 获取收支明细列表
     */
    public function inandoutList()
    {
        $input = $this->input->get();
        if(!isset($input)){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        $result = $this->payment->getList($input);
        return success($result);
    }

    /*
     * 添加收支记录
     */
    public function addInandout()
    {
        $input = $this->input->post();
        if(!isset($input['pay_set_id']) || !isset($input['start_time']) || !isset($input['pay_amount'])){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        $data = $input;
        $login_uid = $this->session->userdata('admin_id');
        $data['service_id'] = isset($login_uid) ? $login_uid : 0;
        $data['serial_number'] = date("YmdHis").rand(100000,999999);
        $result = $this->payment->addOne($data);
        if($result == true)
        {
            echo json_encode(array('result' => 'succ','info' => '添加成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '添加失败'));exit;
        }
    }

    /*
     * 付款记录关联合同
     */
    public function relatedContract()
    {
        $input = $this->input->post();
        if(!isset($input['contract_num']) || !isset($input['p_id'])){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        $contract_item = $this->ew_conn->from("sign_contract")->where("contract_num",$input["contract_num"])->get()->result_array();
        if(empty($contract_item)){
            echo json_encode(array('result' => 'fail','info' => '该合同编号不存在'));exit;
        }else{
            $data = array("contract_id" => $contract_item[0]["id"]);
            $this->erp_conn->where("p_id",$input["p_id"])->update("demand_payment_record",$data);

            //关联成功直接执行易结确认操作
            // $order_item = $this->ew_conn->from("demand_order")->where("content_id",$contract_item[0]["demand_id"])->where("shopper_user_id",$contract_item[0]["shopper_id"])->get()->result_array();
            // $demand_id = $contract_item[0]["demand_id"];
            // $order_id = isset($order_item[0]) ? $order_item[0]["id"] : 0 ;
            // $s_id = $contract_item[0]["id"];
            // //更新sign_contract表status、confirm_time字段
            // $res_contract = $this->followup->updContract(array("s_id" => $s_id));
            // //更新content表status
            // $res_content = $this->followup->updContent(array("demand_id" => $demand_id));
            // //更新order表status,time_status字段
            // $res_order = $this->followup->updOrder(array("order_id" => $order_id));
            // //更新策划师成交量
            // $contract_item = $this->followup->getOne($input);
            // $contract_shopper_id = isset($contract_item['shopper_id'])?$contract_item['shopper_id']:0;
            // $turnover_url = $this->config->config["ew_domain"]."erp/demand/change-shopper-turnover";
            // $res_turnover = $this->curl->post($turnover_url, array("uid"=>$contract_shopper_id));

            $url = $this->config->config["ew_domain"]."erp/business/erp-confirm";
            $result = $this->curl->post($url,$data);
            //关联成功直接执行易结确认操作

            echo json_encode(array('result' => 'succ','info' => '关联成功'));exit;
        }
    }

}
?>
