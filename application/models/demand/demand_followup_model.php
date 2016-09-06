<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 
 */
class Demand_followup_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getList($data)
    {
        $where = $this->getWhere($data);
        $limit = "";
        if(isset($data['page']) && isset($data['pagesize'])){
            $start = ($data['page']-1)*$data['pagesize'];
            $limit = " limit ".$start.",".$data['pagesize'];
        }
        $order = " order by S.id desc ";
        $field = $this->getField();
        $sql = "select ".$field." from 
            ew_sign_contract as S 
            left join 
            ew_demand_content as C 
            on S.demand_id=C.id 
            left join 
            ew_demand_order as O 
            on S.demand_id=O.content_id 
            ".$where.$order.$limit;
        $rows = $this->ew_conn->query($sql)->result_array();
        foreach($rows as $k => $v){
            $rows[$k]['detail_url'] = "trade/followup/contractDetail";
            $rows[$k]['demand_id'] = $v['demand_id'] == null ? "" : $v['demand_id'];
            $rows[$k]['counselor_uid'] = $v['counselor_uid'] == null ? "" : $v['counselor_uid'];
            $rows[$k]['order_id'] = $v['order_id'] == null ? "" : $v['order_id'];
            $rows[$k]['c_demand_num'] = $v['c_demand_num'] == null ? "" : $v['c_demand_num'];
            $rows[$k]['shopper_alias'] = $v['shopper_alias'] == null ? "" : $v['shopper_alias'];
            $rows[$k]['o_status_text'] = $this->getOrderStatus($v['o_status']);
            $rows[$k]['s_status_text'] = $this->getSignStatus($v['s_status']);
            $rows[$k]['s_offline_text'] = $v['s_offline'] == 0 ? "线上" : "线下" ;
            $rows[$k]['shopper_alias_text'] = $this->getShopperAlias($v['shopper_alias']);
            $rows[$k]['confirm_time'] = date("Y-m-d H:i:s",$v['confirm_time']);
            $rows[$k]['s_create_time'] = date("Y-m-d H:i:s",$v['s_create_time']);
            //新人顾问
            $counselor = $this->erp_conn->from("erp_sys_user")->where('id',$v['counselor_uid'])->get()->result_array();
            $rows[$k]['counselor'] = isset($counselor[0]) ? $counselor[0]['username'] : "" ;
            //商家信息
            $shopper_info = $this->ew_conn->from("user_shopers")->where("uid",$v['shopper_id'])->get()->result_array();
            $rows[$k]['shopper_name'] = isset($shopper_info[0]) ? $shopper_info[0]['realname'] : "" ;
            $rows[$k]['studio_name'] = isset($shopper_info[0]) ? $shopper_info[0]['studio_name'] : "" ;
            //用户信息
            $user_info = $this->ew_conn->from("users")->where("uid",$v['wed_uid'])->get()->result_array();
            $rows[$k]['wed_user_name'] = isset($user_info[0]) ? $user_info[0]['username'] : "" ;
            $rows[$k]['wed_user_phone'] = isset($user_info[0]) ? $user_info[0]['phone'] : "" ;
            //收付款金额
            $payment_in = $this->erp_conn->from("demand_payment_record")->where("contract_id",$v['s_id'])->where("inorout",1)->select_sum("pay_amount")->get()->result_array();
            $rows[$k]['payment_in'] = !empty($payment_in[0]["pay_amount"]) ? $payment_in[0]["pay_amount"] : 0 ;
            $payment_out = $this->erp_conn->from("demand_payment_record")->where("contract_id",$v['s_id'])->where("inorout",2)->select_sum("pay_amount")->get()->result_array();
            $rows[$k]['payment_out'] = !empty($payment_out[0]["pay_amount"]) ? $payment_out[0]["pay_amount"] : 0 ;
            //优惠信息
            $discount_user = $this->erp_conn->from("demand_discount")->where('contract_id',$v['s_id'])->where('dis_target',1)->select_sum("dis_amount")->get()->result_array();
            $rows[$k]['dis_amount_user'] = !empty($discount_user[0]["dis_amount"]) ? $discount_user[0]["dis_amount"] : 0 ;
            $discount_shopper = $this->erp_conn->from("demand_discount")->where('contract_id',$v['s_id'])->where('dis_target',2)->select_sum("dis_amount")->get()->result_array();
            $rows[$k]['dis_amount_shopper'] = !empty($discount_shopper[0]["dis_amount"]) ? $discount_shopper[0]["dis_amount"] : 0 ;
        }
        $count = $this->getcount($where);
        return array("rows" => $rows,"total" => $count);
    }

    public function getcount($where)
    {
        $sql = "select count(1) from 
            ew_sign_contract as S 
            left join 
            ew_demand_content as C 
            on S.demand_id=C.id 
            left join 
            ew_demand_order as O 
            on S.demand_id=O.content_id 
            ".$where;
        $count = $this->ew_conn->query($sql)->result_array();
        return $count[0]["count(1)"];
    }

    public function getWhere($data)
    {
        $where = " where 1 ";
        if(isset($data['shopper_alias']) && !empty($data['shopper_alias'])){
            $where .= " and C.shopper_alias='".$data['shopper_alias']."'";
        }
        if(isset($data['create_time_start']) && !empty($data['create_time_start'])){
            $where .= " and S.created_time >= '".$data['create_time_start']."'";
        }
        if(isset($data['create_time_end']) && !empty($data['create_time_end'])){
            $where .= " and S.created_time <= '".$data['create_time_end']."'";
        }
        if(isset($data['wed_date_start']) && !empty($data['wed_date_start'])){
            $where .= " and S.wed_date >= '".$data['wed_date_start']."'";
        }
        if(isset($data['wed_date_end']) && !empty($data['wed_date_end'])){
            $where .= " and S.wed_date <= '".$data['wed_date_end']."'";
        }
        if(isset($data['o_status']) && !empty($data['o_status'])){
            $where .= " and O.status='".$data['o_status']."'";
        }
        if(isset($data['s_status']) && !empty($data['s_status'])){
            $where .= " and S.status='".$data['s_status']."'";
        }
        if(isset($data['shopper_name']) && !empty($data['shopper_name'])){
            $shopper_id = $this->ew_conn->from("user_shopers")->like("realname",$data['shopper_name'])->select("uid")->get()->result_array();
            $shopper_ids_arr = array();
            foreach($shopper_id as $k => $v){
                $shopper_ids_arr[] = $v['uid'];
            }
            $shopper_ids = implode(",",$shopper_ids_arr);
            $where .= " and S.shopper_id in (".$shopper_ids.")";
        }
        if(isset($data['c_demand_num']) && !empty($data['c_demand_num'])){
            $where .= " and C.demand_id='".$data['c_demand_num']."'";
        }
        if(isset($data['contract_num']) && !empty($data['contract_num'])){
            $where .= " and S.contract_num='".$data['contract_num']."'";
        }
        if(isset($data['offline']) && ($data['offline'] === '1' || $data['offline'] === '0')){
            $where .= " and S.offline='".$data['offline']."'";
        }
        return $where;
    }

    public function getField()
    {
        $field = "
                C.id as demand_id,
                C.counselor_uid as counselor_uid,
                S.number as c_demand_num,
                O.id as order_id,
                O.status as o_status,
                S.id as s_id,
                S.offline as s_offline,
                S.contract_num as contract_num,
                S.status as s_status,
                S.shopper_id as shopper_id,
                S.alias as shopper_alias,
                S.wed_date as wed_date,
                S.wed_place as wed_place,
                S.uid as wed_uid,
                S.confirm_time as confirm_time,
                S.created_time as s_create_time,
                S.wed_amount as wed_amount,
            ";
        $field = trim($field);
        $field = trim($field,",");
        return $field;
    }

    public function getOne($data)
    {
        if(isset($data['s_id'])){
            $this->ew_conn->where('id',$data['s_id']);
        }
        $res = $this->ew_conn->from("sign_contract")->get()->result_array();
        if(isset($res[0])){
            return $res[0];
        }else{
            return array();
        }
    }

    public function updContract($data)
    {
        $params = array('status' => 20,'confirm_time' => time());
        $result = $this->ew_conn->where("id", $data['s_id'])->update("ew_sign_contract",$params);
        return $result;
    }

    public function updContent($data)
    {
        if(isset($data['demand_id']) && $data['demand_id'] != ''){
            $params = array('status' => 80);
            $result = $this->ew_conn->where("id", $data['demand_id'])->update("ew_demand_content",$params);
            return $result;
        }else{
            return false;
        }
        
    }

    public function updOrder($data)
    {
        if(isset($data['order_id']) && $data['order_id'] != ''){
            $params = array('status' => 61,'time_61' => date("Y-m-d H:i:s"));
            $result = $this->ew_conn->where("id", $data['order_id'])->update("ew_demand_order",$params);
            return $result;
        }else{
            return false;
        }
    }

}
?>
