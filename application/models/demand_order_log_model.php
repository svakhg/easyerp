<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 需求订单日志
 * @author zhangtao
 */

class Demand_order_log_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'ew_demand_order_log';
	/**
	 * 
	 * 添加日志
	 * @param $uid         integer  操作人id
	 * @param $demand_id   integer  需求id
	 * @param $order_id    integer  订单id
	 * @param $demand_code varcher  需求号码
	 * @param $order_code  varcher  需求号码
	 * @param $comment     varcher  备注
	 * @param $action      varcher  操作动作 
	 * 
	 * **/
	
    public function demandlog($demand_id, $order_id,$order_code,$demand_code,$action='',$comment='')
    {		
    	    $uid = $this->session->userdata('admin_id');
    		$arr = $this->erp_conn->where('id',$uid)->get("ew_erp_sys_user");
            $list = $arr->row_array();
         
    	    $data = array();
	        $data['operater'] = $list['username']; //操作人
            $data['time'] = date('Y-m-d H:i:s');  //创建时间
            $data['demand_id'] = $demand_id; //需求id
            $data['order_id'] = $order_id;   //订单id
            $data['demand_code'] = $demand_code; //需求号码
            $data['order_code'] = $order_code;  //订单号码
            $data['action'] = $action;   //操作动作
            $data['comment'] = $comment; //备注
            $this->erp_conn->insert(self::TBL, $data);
            //echo $this->erp_conn->last_query();
    }


    
}