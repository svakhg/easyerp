<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * author: easywed
 * createTime: 15/10/14 16:28
 * description:中间合同表model
 */

class Contract_ext_model extends MY_Model
{
	protected $_table = 'sign_contract_ext';

    public function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * 合同类型
	 * @return type
	 */
	public function getContractType()
	{
		$contracttype = array(
			'shoper' => 1, //服务商合同
			'sitelayout' => 2, //场地布置合同
			'addition' => 3, //增补合同
		);
		
		$contracttype_explan = array(
			'shoper' => '服务商合同', 
			'sitelayout' => '场地布置合同', 
			'addition' => '增补合同', 
		);
		return array($contracttype , $contracttype_explan);
	}

    /**
     * 中间合同状态
     * @return array
     */
    public function getContractExtStatus()
    {
        $contract_ext_status = array(
            'reject' => 0, //驳回
            'submmited' => 1 //提交
        );

        return array($contract_ext_status , array());
    }
	
	/**
     * 条件处理
     */
    public function conditions($cond)
    {
        foreach($cond as $k => $cv)
        {
            if(is_array($cv) && count($cv) > 0){
                $this->erp_conn->where_in($k,$cv);
            }else{
                $this->erp_conn->where($k,$cv);
            }
        }
    }
	
	/**
     * 获取数据
     * @param $condition 查询条件
     * @param $limit 分页条件,array('nums' => 10, 'start' => 20),nums分页条数，start查询起始
     * @param $order 排序条件
     * @param string $sel_fields 查询字段,可逗号分割
     * @return mixed
     */
	public function getList($condition, $limit = array(), $order = array(), $sel_fields = '*')
	{
		$result = $this->findAll($condition, $limit ,$order, $sel_fields = '*');
        return $result;
	}
	
	public function findContractJoinExtra($condition, $limit, $get_nums = false, $sel_fields = '*')
	{
		if($get_nums)
        {
            $this->erp_conn->from($this->_table);
            $this->erp_conn->join('sign_contract_payment_details' , $this->_table . '.id = sign_contract_payment_details.sid');
            foreach($condition as $k => $v)
            {
                if(is_array($v))
                {
                    $this->erp_conn->where_in($k , $v);
                }
                else
                {
                    $this->erp_conn->where($k , $v);
                }

            }
            return $this->erp_conn->count_all_results();
        }
        else
        {
            $this->erp_conn->select($sel_fields);
            $this->erp_conn->from($this->_table);
            $this->erp_conn->join('sign_contract_payment_details' , $this->_table . '.id = sign_contract_payment_details.sid');
            foreach($condition as $k => $v)
            {
                if(is_array($v))
                {
                    $this->erp_conn->where_in($k , $v);
                }
                else
                {
                    $this->erp_conn->where($k , $v);
                }

            }

            $this->erp_conn->order_by($this->_table . '.id' , 'desc');

            if(isset($limit['nums']) && $limit['nums'] > 0)
            {
                $this->erp_conn->limit($limit['nums'] , $limit['start']);
            }

            return $this->erp_conn->get()->result_array();
        }
	}
}