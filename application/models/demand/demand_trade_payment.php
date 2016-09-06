<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 新人招投标-财务管理Model
 */
class Demand_trade_payment extends MY_Model
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
        $order = " order by p_id desc ";
        $sql = "select * from ew_demand_payment_record ".$where.$order.$limit;
        $rows = $this->erp_conn->query($sql)->result_array();
        # 获取支付方式
        $pay_set_id_arr = $this->get_pay_set_id();
        foreach($pay_set_id_arr as $k => $v){
            $pay_arr[$v['id']] = $v;
        }
        foreach($rows as $k => $v){
            $pay_set_id = $v['pay_set_id'];
            $rows[$k]['pay_set_id_text'] = isset($pay_arr[$pay_set_id]) ? $pay_arr[$pay_set_id]['name'] : "" ;
            $rows[$k]["inorout_text"] = $v['inorout'] == 1 ? "收入" : "支出" ;
            if($v['contract_id'] != 0){
                $contract = $this->ew_conn->where('id',$v['contract_id'])->get("sign_contract")->result_array();
            }
            $rows[$k]['contract_num'] = isset($contract[0]) ? $contract[0]['contract_num'] : "" ;
            unset($contract);
            if($v['demand_id'] != 0){
                $demand = $this->ew_conn->where('demand_id',$v['demand_id'])->get("demand_content")->result_array();
            }
            $rows[$k]['shopper_alias'] = isset($demand[0]) ? $demand[0]['shopper_alias'] : "" ;
            $rows[$k]['shopper_alias_text'] = !empty($rows[$k]['shopper_alias']) ? $this->getShopperAlias($rows[$k]['shopper_alias']) : "" ;
            unset($demand);
        }
        $count = $this->getcount($where);
        return array("rows" => $rows,"total" => $count);
    }

    public function getcount($where)
    {
        $sql = "select count(1) from ew_demand_payment_record ".$where;
        $count = $this->erp_conn->query($sql)->result_array();
        return $count[0]["count(1)"];
    }

    public function getWhere($data)
    {//print_R($data);exit;
        $where = " where 1 ";
        if(isset($data['wed_from']) && !empty($data['wed_from'])){
            $where .= " and start_time >='".$data['wed_from']."'";
        }
        if(isset($data['wed_to']) && !empty($data['wed_to'])){
            $where .= " and start_time <='".$data['wed_to']."'";
        }
        if(isset($data['pay_set_id']) && !empty($data['pay_set_id'])){
            $where .= " and pay_set_id ='".$data['pay_set_id']."'";
        }
        if(isset($data['fund_type']) && !empty($data['fund_type'])){
            $where .= " and fund_type ='".$data['fund_type']."'";
        }
        if(isset($data['inorout']) && !empty($data['inorout'])){
            $where .= " and inorout ='".$data['inorout']."'";
        }
        if(isset($data['pay_man']) && !empty($data['pay_man'])){
            $where .= " and pay_man ='".$data['pay_man']."'";
        }
        if(isset($data['gain_man']) && !empty($data['gain_man'])){
            $where .= " and gain_man ='".$data['gain_man']."'";
        }
        return $where;
    }

    public function addOne($data)
    {
        $result = $this->erp_conn->insert("demand_payment_record",$data);
        return $result;
    }

    /*
     * 获取支付方式
     */
    public function get_pay_set_id()
    {
        $sql = "SELECT `id`,`setting_id`, `name`,`enable`,`order` FROM (`ew_erp_sys_basesetting`) WHERE `setting_id` = 176 and `enable`= 1";
        $arr = $this->erp_conn->query($sql)->result_array();
        return $arr;
    }

}
?>
