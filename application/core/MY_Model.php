<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Base_Model
 */
class MY_Model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    static function getInstance()
    {
        $_CI = & get_instance();
        return $_CI;
    }

    public function getAllShopperAlias()
    {
        return array(
                'wedplanners' => '策划师',
                'wedmaster' => '主持人',
                'makeup' => '化妆师',
                'wedphotoer' => '婚礼摄影',
                'wedvideo' => '婚礼摄像',
                'sitelayout' => '场地布置',
                '' => '无',
            );
    }

    public function getShopperAlias($shopper_alias){
        $allShopperAlias = $this->getAllShopperAlias();
        return $allShopperAlias[$shopper_alias];
    }

    public function getAllOrderStatus()
    {
        return array(
                '1' => "提交需求待审核",
                '2' => "该订单的需求处于草稿状态",
                '11' => "审核成功待商家投标",
                '21' => "商家提交自荐信待审核",
                '31' => "客户读自荐信待初选商家",
                '41' => "选定商家待商家出方案",
                '46' => "商家已出方案",
                '51' => "客户选定商家及方案",
                '55' => "新人已将资金托管到易结",
                '61' => "订单完成",
                '99' => "订单失败",
                '' => '无',
            );
    }

    public function getOrderStatus($status)
    {
        $allOrderStatus = $this->getAllOrderStatus();
        return $allOrderStatus[$status];
    }

    public function getAllSignStatus()
    {
        return array(
                '1' => "商家已向新人发出邀约，待新人确认",
                '11' => "新人确认商家的邀约，待资金托管到易结",
                '12' => "新人已将资金托管到易结，待易结确认签约",
                '20' => "新人已付全款，erp确认签约完成，商家和新人进入婚礼筹备阶段，此时，商家可以添加婚礼团队",
                '21' => "新人未付全款，但合约依旧生效",
                '22' => "新人未付款",
                '80' => "订单完成（由erp手动修改）",
                '99' => "订单关闭（由erp关闭）",
                '' => '无',
            );
    }

    public function getSignStatus($status)
    {
        $allSignStatus = $this->getAllSignStatus();
        return $allSignStatus[$status];
    }

    /**
     * 获取数据
     */
    public function getAll($condition, $limit = array(), $order = array(), $sel_fields = '*')
    {
        $this->erp_conn->select($sel_fields);
        $this->erp_conn->from($this->_table);

        $this->conditions($condition);

        // 排序
        if(!empty($order) && is_array($order))
        {
            foreach($order as $k => $rv)
            {
                $this->erp_conn->order_by($k,$rv);
            }
        }

        // 分页
        if(isset($limit['nums']) && $limit['nums'] > 0)
        {
            $this->erp_conn->limit($limit['nums'] , $limit['start']);
        }

        return $this->erp_conn->get()->result_array();
    }

    /**
     * 获取数据 单条
     */
    public function getOne($condition, $sel_fields = '*')
    {
        $this->erp_conn->select($sel_fields);
        $this->erp_conn->from($this->_table);

        $this->conditions($condition);

        return $this->erp_conn->get()->row_array();
    }

    /**
     * 获取数据
     * @param $condition 查询条件
     * @param $limit 分页条件,array('nums' => 10, 'start' => 20),nums分页条数，start查询起始
     * @param $order 排序条件
     * @param string $sel_fields 查询字段,可逗号分割
     * @return mixed
     */
    public function findAll($condition, $limit = array(), $order = array(), $sel_fields = '*')
    {
        $this->erp_conn->select($sel_fields);
        $this->erp_conn->from($this->_table);

        foreach($condition as $k => $v)
        {
            if(is_array($v) && count($v) > 0)
            {
                $this->erp_conn->where_in($k,$v);
            }
            else
            {
                $this->erp_conn->where($k,$v);
            }
        }

        foreach($order as $k => $rv)
        {
            $this->erp_conn->order_by($k,$rv);
        }

        if(isset($limit['nums']) && $limit['nums'] > 0)
        {
            $this->erp_conn->limit($limit['nums'] , $limit['start']);
        }

        $query = $this->erp_conn->get();
        return $query->result_array();
    }

    /**
     * 根据条件获取单条数据
     * @param array $conditions
     * @return array
     */
    public function findByCondition($conditions = array() , $sel_fields = '*')
    {
        if(count($conditions) <= 0)return array();
        $this->erp_conn->select($sel_fields);
        $this->erp_conn->from($this->_table);

        foreach($conditions as $k => $cv)
        {
            if(is_array($cv) && count($cv) > 0){
                $this->erp_conn->where_in($k,$cv);
            }else{
                $this->erp_conn->where($k,$cv);
            }
        }

        $query = $this->erp_conn->get();
        return $query->row_array();
    }

    /**
     * 更新数据
     * @param $data
     * @param $condition
     * @return bool
     */
    public function updateByCondition($data , $condition){
        if(count($data) <= 0 || count($condition) <= 0)return false;

        foreach($condition as $k => $cv)
        {
            if(is_array($cv) && count($cv) > 0)
            {
                $this->erp_conn->where_in($k , $cv);
            }
            else
            {
                $this->erp_conn->where($k , $cv);
            }
        }
        $this->erp_conn->update($this->_table , $data);
        return $this->erp_conn->affected_rows();
    }

    /**
     * 获取记录数
     * @param array $condition
     * @return mixed
     */
    public function counts($condition = array())
    {
        $this->erp_conn->from($this->_table);

        foreach($condition as $k => $v)
        {
            if(is_array($v) && count($v) > 0)
            {
                $this->erp_conn->where_in($k,$v);
            }
            else
            {
                $this->erp_conn->where($k,$v);
            }
        }

        return $this->erp_conn->count_all_results();
    }

    /**
     * 新增数据
     * @param array $insert_data
     * @return mixed
     */
    public function add($insert_data = array())
    {
        $this->erp_conn->insert($this->_table,$insert_data);
        return $this->erp_conn->insert_id();
    }

    /**
     * 更新数据
     * @param array $cond
     * @param array $data
     * @return bool
     */
    public function modify(array $cond, array $data)
    {
        foreach($cond as $field => $val)
        {
            $this->erp_conn->where($field, $val);
        }
        return $this->erp_conn->update($this->_table, $data);
    }
}