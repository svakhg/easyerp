<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * description:商机与商家映射表model,此表存储每个商机所对应的商家
 */

class Business_adviser_log_model extends MY_Model
{
	protected $_table = 'business_adviser_log';

	public function __construct()
    {
        parent::__construct();
    }

	/**
	 * 插入日志
	 */
	public function add_batch($data)
	{
		return $this->erp_conn->insert_batch($this->_table, $data);
	}
}