<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 地区联级
 */

class Region_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'ew_erp_regions';

    /**
	 * 根据parent_id获取地区信息
	 * @param type $parent_id 父id
	 * @param type $select 查询的字段
	 */
	public function getInfoByPid($parent_id = 0, $select = "")
	{
		if($parent_id === "")
		{
			return array();
		}
		$result = $this->erp_conn->where("parent_id", $parent_id)->select($select)->get(self::TBL)->result_array();
		
		return $result;
	}
	
	/**
	 * 根据地区名称获取地区信息
	 * @param type $name
	 * @return type
	 */
	public function getRegionByName($name)
	{
		if(empty($name))
		{
			return array();
		}
		$result = $this->erp_conn->where("name", $name)->get(self::TBL)->row_array();
		return $result;
	}
	
	/**
	 * 根据地区id获取地区信息
	 * @param type $id
	 */
	public function getRegionById($id)
	{
		if(empty($id))
		{
			return array();
		}
		$result = $this->erp_conn->where("id", $id)->get(self::TBL)->row_array();
		
		return $result;
	}

    /**
     * 获取所有地区数据
     * @return array
     */
    public function getAll()
    {
        $result = array();
        $query_data = $this->erp_conn->get(self::TBL)->result_array();
        foreach($query_data as $val)
        {
            $result[$val['id']] = $val['name'];
        }
        return $result;
    }
}