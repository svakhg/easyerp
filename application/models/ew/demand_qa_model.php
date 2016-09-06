<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: abel
 * Date: 2015/6/8
 * Time: 14:58
 */
class Demand_qa_model extends MY_Model {

    const TBL = 'demand_qa';

    public function __construct(){
        parent::__construct();

    }

    /*
     * 根据需求content_id 获取qa表的订单列表
     *
     */
    public function getQasByContentId($content_id, $select = '*')
    {
        $result = $this->ew_conn->select($select)->where('content_id', $content_id)->get(self::TBL)->result_array();
        return ! empty($result) ? $result : array();
    }

    /*
     * 根据需求id 获取qa单条记录
     *
     */
    public function getQaById($id, $select = '*')
    {
        return $this->ew_conn->select($select)->where('id', $id)->get(self::TBL)->row_array();
    }


    /*
     * 根据需求条件 content_id 和 alias 获取qa单条记录
     *
     */
    public function getByCondition($content_id, $col = '')
    {
        $obj = $this->ew_conn->where('content_id', $content_id);
        if($col != '')
        {
            $obj->where('alias',$col);
        }
        return $obj->get(self::TBL)->row_array();
    }

    /**
     * 添加一条qa记录
     */
    public function addByCondition($data)
    {
        return $this->ew_conn->insert(self::TBL, $data);
    }

    /**
     * 编辑
     * 通过content_id 和alias $col定位
     */
    public function editByCondition($content_id, $data, $col = '')
    {
        $obj =  $this->ew_conn->where('content_id', $content_id);
        if($col != '')
        {
            $obj->where('alias',$col);
        }
        return $obj->update(self::TBL, $data);
    }
}