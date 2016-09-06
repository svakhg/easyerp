<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 
 */

class OfflineContract extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->local_conn = $this->load->database('local',true);
        $this->ew_conn->query("SET sql_mode = ''");
    }

    public function main()
    {
        $this->erp_conn->trans_start();
        $res_contract = $this->local_conn->get("contract_tmp")->result_array();
        foreach($res_contract as $k => $v){
            if($v['contract_num'] == ""){
                continue;
            }
            $ew_data = $this->ew_conn
                ->where(array("contract_num"=>$v['contract_num']))
                ->get("ew_sign_contract")->result_array();print_R($this->ew_conn->last_query());
            $status = $this->status($ew_data[0]['status']);
            $data_contract['uid'] = $ew_data[0]['uid'];
            $data_contract['username'] = $ew_data[0]['username'];
            $data_contract['mobile'] = $v['user_phone'];
            $data_contract['contract_num'] = $ew_data[0]['contract_num'];
            $data_contract['wed_date'] = strtotime($ew_data[0]['wed_date']);
            $data_contract['wed_location'] = $ew_data[0]['wed_location'];
            $data_contract['wed_place'] = $ew_data[0]['wed_place'];
            $data_contract['wed_amount'] = $ew_data[0]['wed_amount'];
            $data_contract['offline'] = $ew_data[0]['offline'];
            $data_contract['alias'] = $ew_data[0]['alias'];
            $data_contract['alias_type'] = $ew_data[0]['alias_type'];
            $data_contract['discount_amount'] = $ew_data[0]['discount_amount'];
            $data_contract['contract_status'] = $status['contract_status'];
            $data_contract['archive_status'] = ($v['archive_status'] == "已归档") ? 2 : 1 ;
            $data_contract['funds_status'] = $status['funds_status'];
            $data_contract['create_time'] = $ew_data[0]['created_time'];
            $data_contract['sign_time'] = $ew_data[0]['confirm_time'];
            $data_contract['archive_time'] = ($v['archive_status'] == "已归档") ? $ew_data[0]['confirm_time'] + 3*24*3600 : 0 ;
            $data_contract['number_img'] = "";
            $data_contract['sign_img'] = "";
            $data_contract['shopper_id'] = $ew_data[0]['shopper_id'];
            $data_contract['shopper_name'] = $v['shopper_nickname'];
            $data_contract['stop_reason'] = "";
            $data_contract['stop_time'] = 0;
            $data_contract['finish_time'] = 0;
            $data_contract['is_test'] = $ew_data[0]['is_test'];
            var_dump($data_contract);
            // $res = $this->erp_conn->insert("sign_contract",$data_contract);
            var_dump($res);
        }
        $this->erp_conn->trans_complete();
    }

    public function status($status)
    {
        switch ($status) {
            case '1':
                $res_status['contract_status'] = 1;
                $res_status['funds_status'] = 1;
                break;
            case '20':
                $res_status['contract_status'] = 20;
                $res_status['funds_status'] = 10;
                break;
            case '30':
                $res_status['contract_status'] = 20;
                $res_status['funds_status'] = 20;
                break;
            case '80':
                $res_status['contract_status'] = 30;
                $res_status['funds_status'] = 40;
                break;
            case '90':
                $res_status['contract_status'] = 60;
                $res_status['funds_status'] = 1;
                break;
            
            default:
                # code...
                break;
        }
        return $res_status;
    }


    public function syncPayment()
    {
        $this->erp_conn->trans_start();
        $res_payment = $this->local_conn->get("contract_payment_tmp")->result_array();
        foreach($res_payment as $k => $v){
            if($v['contract_num'] == ""){
                continue;
            }
            $contract = $this->erp_conn->where("contract_num",$v['contract_num'])->get("sign_contract")->result_array();
            $pay_data['cid'] = isset($contract[0]) ? $contract[0]['id'] : 0 ;
            $pay_data['bid'] = 0;
            $pay_data['sid'] = 0;
            $pay_data['ew_pay_id'] = 0;
            $pay_data['serial_number'] = date('YmdHis').rand(100000,999999);
            $pay_data['status'] = 5;
            $pay_data['amount'] = $v['entrust_amount'];
            $pay_data['pay_mode'] = $v['pay_type'] == "对公" ? 3 : 2 ;
            $pay_data['fund_type'] = $this->fund_type($v['fund_type']);
            $pay_data['voucher_img'] = "";
            $pay_data['create_time'] = strtotime($v['pay_time']);
            $pay_data['update_time'] = strtotime($v['pay_time']);
            $pay_data['pay_time'] = strtotime($v['pay_time']);
            $pay_data['sys_uid'] = 20;
            $pay_data['refuse_reason'] = "";
            if($v['pay_type'] == "对公" && strpos($v['comment'],"对公") === false){
                $pay_data['note'] = "对公".$v['comment'];
            }else{
                $pay_data['note'] = $v['comment'];
            }
            $pay_data['is_test'] = 0;
            print_R($pay_data);
            // $res = $this->erp_conn->insert("sign_contract_payment_details",$pay_data);
            var_dump($res);
        }
        $this->erp_conn->trans_complete();
    }


    public function fund_type($fundtype)
    {
        switch ($fundtype) {
            case '定金':
                $type = 1;
                break;
            case '尾款':
                $type = 5;
                break;
            case '回款':
                $type = 10;
                break;
            case '其他':
                $type = 8;
                break;
            
            default:
                # code...
                break;
        }
        return $type;
    }


}
