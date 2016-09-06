<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
  # 婚礼需求跟进管理
  # add by zhangmiao
  # 
 */
class Followup extends App_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("demand/demand_followup_model","followup");
        $this->load->model("demand/demand_trade_payment","payment");
    }

    public function index()
    {
        $_data = array();
        $_data['order_status'] = $this->followup->getAllOrderStatus();
        $_data['sign_status'] = $this->followup->getAllSignStatus();
        $this->load->view("trade/contract/demand_followup_view",$_data);
    }

    /*
     * 获取跟进订单的列表
     */
    public function getOrderList()
    {
        $input = $this->input->get();
        $list = $this->followup->getList($input);
        return success($list);
    }

    /*
     * 获取跟进订单的详情
     * 包括需求，订单，合同，合作商
     */
    public function contractDetail()
    {
        $input = $this->input->get();
        $_data = array();
        $contract = $this->ew_conn->where("id",$input['sid'])->get("sign_contract")->result_array();
        $user_info = $this->ew_conn->where("uid",$contract[0]['uid'])->get("users")->result_array();
        $shopper_info = $this->ew_conn->where("uid",$contract[0]['shopper_id'])->get("user_shopers")->result_array();
        $_data['contract'] = $contract[0];
        $_data['user_info'] = $user_info[0];
        $_data['shopper_info'] = $shopper_info[0];
        $_data['pay_set'] = $this->payment->get_pay_set_id();
        // print_R($_data);exit;
        $this->load->view("trade/contract/followup_detail_view",$_data);
    }

    /*
     * 易结确认操作
     */
    public function confirmTrade()
    {
        $input = $this->input->post();
        if(!isset($input['demand_id']) || !isset($input['order_id']) || !isset($input['s_id'])){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        //更新sign_contract表status、confirm_time字段
        $res_contract = $this->followup->updContract($input);
        //更新order表status,time_status字段
        $res_order = $this->followup->updOrder($input);
        //更新content表status
        $res_content = $this->followup->updContent($input);
        //更新策划师成交量
        $contract_item = $this->followup->getOne($input);
        $contract_shopper_id = isset($contract_item['shopper_id'])?$contract_item['shopper_id']:0;
        $turnover_url = $this->config->config["ew_domain"]."erp/demand/change-shopper-turnover";
        $res_turnover = $this->curl->post($turnover_url, array("uid"=>$contract_shopper_id));
        echo json_encode(array('result' => 'succ','info' => '操作成功'));exit;
    }

    /*
     * 手工确认订单完成
     */
    public function finishContract()
    {
        $input = $this->input->post();
        if(!isset($input['s_id'])){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        $result = $this->ew_conn->where("id",$input['s_id'])->update("sign_contract",array("status"=>80));
        if($result === true){
            echo json_encode(array('result' => 'succ','info' => '操作成功'));exit;
        }else{
            echo json_encode(array('result' => 'fail','info' => '操作失败'));exit;
        }
    }


}
?>
