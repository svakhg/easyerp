<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 功能表
 * author by Abel
 */

class Sys_func_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'erp_sys_func';

    //启用状态
    const ENABLE_ON = 1;

    //关闭状态
    const ENABLE_OFF = 0;
	
    //按钮
    const IS_BUTTON = 1;

    //非按钮
    const NOT_BUTTON = 0;

    /**
     * 添加权限管理
     * return array
     */

    public function add($data)
    {
        if(empty($data))
        {
            return failure('参数错误');
        }
        return $this->erp_conn->insert(self::TBL, $data);
    }

    /**
     * 编辑权限
     *
     */
    public function edit($id, $data)
    {
        $result = $this->erp_conn->where('id', $id)->update(self::TBL, $data);
        return $result ? "修改成功" : "修改失败";
    }

    /**
     * 删除权限
     */
    public function delById($id)
    {
        $result = $this->erp_conn->where('id', $id)->delete(self::TBL);
        return $result ? "删除成功" : "删除失败";
    }

    /**
     * 根据id获取数据
     */
    public function getInfoById($id)
    {
        $result = $this->erp_conn->where('id', $id)->get(self::TBL)->row_array();
        return (! empty($result)) ? $result : array();
    }

    /**
     * 获取所有功能数据
     * @return array
     */
    public function getAllInfos()
    {
        $result = $this->erp_conn->where('enable', self::ENABLE_ON)->get(self::TBL)->result_array();
        return (! empty($result)) ? $result : array();

    }

    /**
     * 根据pid和等级获取功能数据
     * @return array()
     */
    public function getInfos($pid = '', $level = 1, $auth_arr = array())
    {
		if($auth_arr == array())
		{
//			$result = $this->erp_conn->where('pid', $pid)->where('level', $level)->where('enable', self::ENABLE_ON)->get(self::TBL)->result_array();
			return array();
			
		}else
		{
			$result = $this->erp_conn->where_in("id", $auth_arr)->where('pid', $pid)->where('level', $level)->where('enable', self::ENABLE_ON)->get(self::TBL)->result_array();
		}
        return (! empty($result)) ? $result : array();
    }

    public function getInfosByPid($pid, $select='')
    {
        $query = $this->erp_conn->where('pid', $pid)->select($select)->get(self::TBL)->result_array();
        $result = (! empty($query)) ? $query : array();
        return $result;
    }

    public function getInfosByPid2($pid)
    {
        $query = $this->erp_conn->where('pid', $pid)->where('is_button', self::NOT_BUTTON)->get(self::TBL)->result_array();
        $result = (! empty($query)) ? $query : array();
        return $result;
    }

    public function getAllInfosByPid($pid, $select='')
    {
        $query = $this->erp_conn->where('pid', $pid)->select($select)->get(self::TBL)->result_array();
        $result = (! empty($query)) ? $query : array();
        return $result;
    }
	
	//取权限菜单列表
	public function permission_lst($pid = 0)
	{
		$info = $this->getInfosByPid($pid,'id,func_name as text');
		$tree_arr = array();
		if(!empty($info))
		{
			foreach ($info as $key => $rows)
			{
				if(!empty($rows))
				{
					$rows["children"] = $this->permission_lst($rows["id"]);
					$tree_arr[] = $rows;
				}
			}
		}
		return $tree_arr;
//		print_r($tree_arr);
	}

    /**
     * 根据当前记录id获取子节点的信息
     */
    public function getAllCld($pid = 0)
    {
        $info = $this->getInfosByPid($pid,'id, level, pid');
        foreach($info as $v)
        {
            $row_str[] = $v['id'];
        }
        $str = $this->_tree($row_str);
        foreach($str as &$val){
            $val = "'".$val."'";
        }
        return $str;
    }

    public function _tree($arr)
    {
        foreach($arr as $v)
        {
            $_info = $this->getInfosByPid($v);
            if(! empty($_info))
            {
                foreach($_info as $_v)
                {
                    $list[] = $_v['id'];
                }
                $arr = $this->_tree($list);
            }
        }
        return $arr;
    }
	/**
	 * 根据控制器名和方法名取权限id
	 * $controller 控制器名
	 * $action 方法名
	 */
	public function getIdByController($controller, $action)
	{
		if($controller == "home" && $action == "index")
		{
			$controller = $action = "/";
		}
		$result = $this->erp_conn->where("controller", $controller)->where("action", $action)->get(self::TBL)->row_array();
		
		return (!empty($result)) ? $result["id"] : 0;
	}
	
	/**
	 * 根据func_name获取权限信息
	 * @param type $func_name
	 */
	public function getInfoByName($func_name)
	{
		if(empty($func_name))
		{
			return array();
		}
		$result = $this->erp_conn->where("func_name", $func_name)->get(self::TBL)->row_array();
		
		return (!empty($result)) ? $result : array();
	}
	

}