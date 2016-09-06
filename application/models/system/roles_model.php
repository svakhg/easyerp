<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 角色
 */

class Roles_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'ew_erp_sys_role';
	
	/**
	 * 分页每页条数
	 */
	const PAGESIZE = 10;
	
	/**
	 * 超级管理员角色id
	 */
	const ADMIN_ROLE = 1;
	
	public function getInfoByName($name)
	{
		if(empty($name))
		{
			return array();
		}

		$result = $this->erp_conn->where('role_name', $name)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	public function getInfoById($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//删除角色
	public function delRole($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->delete(self::TBL);
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//删除多个角色
	public function delRoles($ids)
	{
		if(empty($ids))
		{
			return array();
		}
		$count = 0;
		foreach ($ids as $key => $id)
		{
			if($this->delRole($id)) $count++;
		}

        return $count;
	}
	
	//获取角色的权限
	public function getAuth($role_id)
	{
		//如果是超级管理员，取所有权限列表
		if(empty($role_id))
		{
			return array();
		}
		$result = $this->erp_conn->select("func_id")->where("id", $role_id)->get(self::TBL)->row_array();
		$result = (!empty($result)) ? $result : array();
		
		return $result;
	}
}