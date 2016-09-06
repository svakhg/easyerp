<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: abel
 * Date: 2015/6/8
 * Time: 14:58
 */
class Demand_content_model extends MY_Model {

    const TBL = 'demand_content';

    const UNEXAMINED_STATUS_NO_SHOPPER = 0;

    const UNEXAMINED_STATUS_HAVE_SHOPPER = 4;

    public function __construct(){
        parent::__construct();

    }

    /*
     * 查询待审核新人需求 总数
     */
    public function getWait_examined_demand(){

        $result = $this->ew_conn
            ->where('status',self::UNEXAMINED_STATUS_NO_SHOPPER)->or_where('status',self::UNEXAMINED_STATUS_HAVE_SHOPPER)
            ->from(self::TBL)
            ->count_all_results();
        return $result;
    }

    /*
     * 根据id获取需求
     * $id
     */
    public function getContentById($id, $select = '*')
    {
        return $this->ew_conn->select($select)->where('id', $id)->get(self::TBL)->row_array();
    }

    /**
     * 编辑
     * 通过id定位
     */
    public function edit($id, $data)
    {
        return $this->ew_conn->where('id', $id)->update(self::TBL, $data);
    }

    /*
     * 更新需求状态
     * 根据自定义定位条件
     */
    public function changeContentStatus($id, $status)
    {
        return $this->ew_conn->where('id', $id)->update(self::TBL, array('status' => $status));
    }

    /*
     * 根据uid获取电话号码
     */
    public function getPhoneByUid($uid)
    {
        $result = $this->ew_conn->from("users")->where('uid',$uid)->get()->result_array();
        if(isset($result[0])){
            return $result[0]['phone'];
        }else{
            return false;
        }
    }
}