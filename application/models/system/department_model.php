<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 角色
 */

class Department_model extends MY_Model
{

    /**
     * 数据库表名
     * @var string
     */

	protected $_table = 'erp_sys_department';

	const ALIAS_HSGWB = 'HLGWB';
	const ALIAS_YLB = 'YLB';

	public function getWedAdviser()
	{
		return $this->erp_conn->where('alias', self::ALIAS_HSGWB)->get($this->_table)->row_array();
	}

	public function getOperater()
	{
		return $this->erp_conn->where('alias', self::ALIAS_YLB)->get($this->_table)->row_array();
	}

	/**
	 * 获取部门人员列表
	 */
	public function getList($id)
	{
		$this->load->model('sys_user_model', 'sum');
		return $this->sum->erp_conn->where('deparment', $id)->get($this->_table)->result_array();
	}
}