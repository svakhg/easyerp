<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * author: easywed
 * createTime: 15/10/14 13:58
 * description:商机扩展表model
 */

class Business_extra_model extends MY_Model
{
    protected $_table = 'business_extra';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 预处理数据
     * @param array $insert_data
     * @return array
     */
    public function prepareData($insert_data = array())
    {
        $prepare_data = array('bid' => 0, 'weddate_note' => '', 'location' => '', 'wed_place' => '',
            'wed_place_area' => '','wed_type' => 0, 'guest_from' => 0, 'guest_to' => 0, 'desk_from' => 0, 'desk_to' => 0,
            'price_from' => 0, 'price_to' => 0, 'budget' => 0, 'findtype' => 0, 'findnote' => '',
            'wish_contact' => '', 'moredesc' => ''
        );

        if(count($insert_data) > 0)
        {
            $prepare_data = array_merge($prepare_data , $insert_data);
        }
        return $prepare_data;
    }

    /**
     * 条件处理
     */
    public function conditions($cond)
    {
        if(is_array($cond['bids']) && !empty($cond['bids']))
        {
            $this->erp_conn->where_in('bid', $cond['bids']);
        }
    }
	
	/**
     * 获取单条记录
     * @param $condition 查询条件
     * @param string $sel_fields 查询字段
     * @return array
     */
    public function findRow($condition, $sel_fields = '*')
    {
        $result = $this->findByCondition($condition, $sel_fields);
        return $result;
    }
}