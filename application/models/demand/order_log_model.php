<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 订单日志
 */

class Order_log_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'ew_demand_order_log';

    public function add($data){
        if(empty($data))
        {
            return failure('参数错误');
        }

        $data["time"] =date("Y-m-d H:i:s");

        return $this->erp_conn->insert(self::TBL, $data);
    }

    public function getlist()
    {
        $ret = $this->erp_conn->get(self::TBL)->result_array();
        return ! empty($ret) ? $ret : array();
    }
}