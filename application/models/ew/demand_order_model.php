<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: abel
 * Date: 2015/6/8
 * Time: 14:58
 */
class Demand_order_model extends MY_Model {

    const TBL = 'demand_order';

    const UNEXAMINED_STATUS = 21;

    public function __construct(){
        parent::__construct();

    }

    /*
     * 查询待审的自荐信列表
     */
    public function getWait_examined_letterOrder(){

        $result = $this->ew_conn
            ->where('status',self::UNEXAMINED_STATUS)
            ->from(self::TBL)
            ->count_all_results();
        return $result;
    }

    /*
     * 根据需求content_id 获取order表的订单列表
     *
     */
    public function getOrdersByContentId($content_id, $select = '*')
    {
        $result = $this->ew_conn->select($select)->where('content_id', $content_id)->get(self::TBL)->result_array();
        return ! empty($result) ? $result : array();
    }

    /*
     * 根据需求id 获取order订单
     *
     */
    public function getOrderById($id, $select = '*')
    {
        return $this->ew_conn->select($select)->where('id', $id)->get(self::TBL)->row_array();
    }

    /*
     * 改变订单的状态
     * 根据自定义定位条件 更新订单状态
     */
    public function changeOrderStatusInWhere($content_id, $data, $where)
    {
        return $this->ew_conn->where("content_id = $content_id and $where")->update(self::TBL, $data);
    }
}