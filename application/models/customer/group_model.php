<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 客户分组
 */

class Group_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'erp_client_team';
	
	/**
	 * 分页每页条数
	 */
	const PAGESIZE = 10;


    /**
     * 获取所有分组
     * @return array
     */
    public function getAllGroups($page = 1, $pagesize = self::PAGESIZE)
    {
		$offset = ($page-1)*$pagesize;
		$db = $this->erp_conn;
		//echo $this->erp_conn->last_query();die;
		$total = $db->count_all_results(self::TBL);
		$result = $db->limit($pagesize)->offset($offset)->order_by('team_num asc')->get(self::TBL)->result_array();
		$info = array(
            'total' => $total,
            'rows' => $result
        );
        return $info;
    }
	
	//添加分组
	public function addTeam($data)
	{
		if(empty($data))
		{
			return array();
		}
		$result = $this->erp_conn->insert(self::TBL, $data);
		return $result ? $result : array();
	}
	
	//修改分组
	public function editTeam($id, $data)
	{
		if(empty($id) || empty($data))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->update(self::TBL, $data);
		return $result ? $result : array();
	}
	
	//根据id查询分组信息
	public function getTeamById($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//删除分组
	public function delTeam($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->delete(self::TBL);
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//删除多个分组
	public function delTeams($ids)
	{
		if(empty($ids))
		{
			return array();
		}
		$count = 0;
		foreach ($ids as $key => $id)
		{
			if($this->delTeam($id)) $count++;
		}

        return $count;
	}
	
	//根据分组编号查询
	public function getTeamByNum($num)
	{
		if(empty($num))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('team_num', $num)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//根据分组名称查询
	public function getTeamByName($name)
	{
		if(empty($name))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('team_name', $name)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//搜索(根据分组名或分组编号搜索)
	public function getTeamByWhere($find,$page = 1, $pagesize = self::PAGESIZE)
	{
		$offset = ($page-1)*$pagesize;
		$db = $this->erp_conn;
		//如果查询条件为空，显示全部列表
		if($find != "")
		{
			$where = "team_name like '%$find%'  OR team_num = '$find'";
			$db->where($where);
		}
		//返回数据
		$dbhandle = clone($db);
		$total = $db->count_all_results(self::TBL);
		$result = $dbhandle->limit($pagesize)->offset($offset)->order_by('team_num asc')->get(self::TBL)->result_array();
		$info = array(
			"total" => $total,
			"rows" => $result,
		);
		return $info;
	}
}