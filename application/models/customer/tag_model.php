<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 客户标签
 */

class Tag_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'erp_client_tag';
	
	/**
	 * 分页每页条数
	 */
	const PAGESIZE = 10;

	    /**
     * 获取所有标签
     * @return array
     */
    public function getAllTags($page = 1, $pagesize = self::PAGESIZE)
    {
		$offset = ($page-1)*$pagesize;
		$db = $this->erp_conn;
		//echo $this->erp_conn->last_query();die;
		$total = $db->count_all_results(self::TBL);
		$result = $db->limit($pagesize)->offset($offset)->order_by('id asc, order desc')->get(self::TBL)->result_array();
		$info = array(
            'total' => $total,
            'rows' => $result
        );
        return $info;
    }
	
//	/**
//     * 获取所有标签(不带分页)
//     * @return array
//     */
//    public function getAll()
//    {
//		$result = $this->erp_conn->get(self::TBL)->result_array();
//        return $result;
//    }
	
	//添加标签
	public function addTag($data)
	{
		if(empty($data))
		{
			return array();
		}
		$result = $this->erp_conn->insert(self::TBL, $data);
		return $result ? $result : array();
	}
	
	//修改标签
	public function editTag($id, $data)
	{
		if(empty($id) || empty($data))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->update(self::TBL, $data);
		return $result ? $result : array();
	}
	
	//根据id查询标签信息
	public function getTagById($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//删除标签
	public function delTag($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->delete(self::TBL);
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//删除多个标签
	public function delTags($ids)
	{
		if(empty($ids))
		{
			return array();
		}
		$count = 0;
		foreach ($ids as $key => $id)
		{
			if($this->delTag($id)) $count++;
		}

        return $count;
	}
	
	//搜索(根据标签名或标签顺序号搜索)
	public function getTagsByWhere($find,$page = 1, $pagesize = self::PAGESIZE)
	{
		$offset = ($page-1)*$pagesize;
		$db = $this->erp_conn;
		//如果查询条件为空，显示全部列表
		if($find != "")
		{
			$where = "`tag_name` like '%$find%'  OR `order` = '$find'";
			$db->where($where);
		}
		//返回数据
		$dbhandle = clone($db);
		$total = $db->count_all_results(self::TBL);
		$result = $dbhandle->limit($pagesize)->offset($offset)->order_by('order desc, id asc')->get(self::TBL)->result_array();
		$info = array(
			"total" => $total,
			"rows" => $result,
		);
		return $info;
	}
	
	/**
	 * 根据标签名查询标签信息
	 * $name 标签名
	 */
	public function getTagByName($name)
	{
		if(empty($name))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('tag_name', $name)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
}