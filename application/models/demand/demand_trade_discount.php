<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 新人招投标-交易优惠Model
 */
class Demand_trade_discount extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    /*
     * 获取优惠列表
     * demand_id:需求id
     */
    public function getDiscountList($data)
    {
        $params = array('contract_id' => $data['sid'],'demand_id' => $data['demand_id']);
        $this->erp_conn->from("demand_discount");
        $this->erp_conn->where($params);
        if(isset($data['page']) && isset($data['pagesize'])){
            $start = ($data['page'] - 1) * $data['pagesize'];
            $this->ew_conn->limit($data['pagesize'],$start);
        }
        $rows = $this->erp_conn->get()->result_array();
        foreach($rows as $k => $v){
            $rows[$k]['dis_target_text'] = ($v['dis_target'] == 1) ? "新人" : "商家" ;
        }

        $this->erp_conn->from("demand_discount");
        $this->erp_conn->where($params);
        $total = $this->erp_conn->count_all_results();
        return array('rows' => $rows, 'total' => $total);
    }


    public function addOne($data)
    {
        $data['create_time'] = date("Y-m-d H:i:s");
        $result = $this->erp_conn->insert("demand_discount", $data);
        return $result;
    }

    public function editOne($data)
    {
        $this->erp_conn->where('id',$data['id']);
        unset($data['id']);
        $result = $this->erp_conn->update("demand_discount", $data);
        return $result;
    }

}
?>
