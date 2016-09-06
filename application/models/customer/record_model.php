<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 客户档案
 */

class Record_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'erp_client_file';
	
	/**
	 * 分页每页条数
	 */
	const PAGESIZE = 10;


    /**
     * 获取所有档案记录
     * @return array
     */
    public function getAllRecords()
    {
        $result = $this->erp_conn->order_by('cli_number asc')->get(self::TBL)->result_array();
		$total = count($result);
		$result = (! empty($result)) ? $result : array();
		$info = array(
            'total' => $total,
            'rows' => $result
        );
        return $info;
    }
	
	//添加客户档案记录
	public function addRecord($inputs)
	{
		if(empty($inputs))
		{
			return 0;
		}
		$init['cli_number'] = "CU".time().rand(0,9);  //客户编号  CU+11位数字
		$init['ew_uid'] = isset($inputs["ew_uid"]) ? $inputs["ew_uid"] : 0;
		$init['cli_name'] = isset($inputs["cli_name"]) ? $inputs["cli_name"] : "";
		$init['cli_source'] = isset($inputs["cli_source"]) ? $inputs["cli_source"] : 0;
		$init['channel'] = ! empty($inputs["channel"]) ? $inputs["channel"] : 0;
		$init["cli_gender"] = isset($inputs["cli_gender"]) ? $inputs["cli_gender"] : 0;
		$init["cli_birth"] = isset($inputs["cli_birth"]) ? $inputs["cli_birth"] : 0000-00-00;
		$init["cli_constellation"] = isset($inputs["cli_constellation"]) ? $inputs["cli_constellation"] : "";
		$init["cli_edu"] = isset($inputs["cli_edu"]) ? $inputs["cli_edu"] : 0;
		$init["cli_nick"] = isset($inputs["cli_nick"]) ? $inputs["cli_nick"] : "";
		$init["cli_race"] = isset($inputs["cli_race"]) ? $inputs["cli_race"] : "";
		$init["cli_weibo"] = isset($inputs["cli_weibo"]) ? $inputs["cli_weibo"] : "";
		$init["cli_mobile"] = isset($inputs["cli_mobile"]) ? $inputs["cli_mobile"] : "";
		$init["cli_tel"] = isset($inputs["cli_tel"]) ? $inputs["cli_tel"] : "";
		$init["cli_defined_phone"] = isset($inputs["cli_defined_phone"]) ? $inputs["cli_defined_phone"] : "";   //待修改，用，链接
		$init["cli_qq"] = isset($inputs["cli_qq"]) ? $inputs["cli_qq"] : "";  //待修改，用，链接
		$init["cli_weixin"] = isset($inputs["cli_weixin"]) ? $inputs["cli_weixin"] : "";   //待修改，用，链接
		$init["cli_blood"] = isset($inputs["cli_blood"]) ? $inputs["cli_blood"] : 0;
		$init["cli_email"] = isset($inputs["cli_email"]) ? $inputs["cli_email"] : "";
		$init["country"] = isset($inputs["country"]) ? $inputs["country"] : 0;
		$init["province"] = isset($inputs["province"]) ? $inputs["province"] : 0;
		$init["city"] = isset($inputs["city"]) ? $inputs["city"] : 0;
		$init["street"] = isset($inputs["street"]) ? $inputs["street"] : "";
		$init["postcode"] = isset($inputs["postcode"]) ? $inputs["postcode"] : "";
		$init["comment"] = isset($inputs["comment"]) ? $inputs["comment"] : "";
		$init["tag"] = isset($inputs["tag"]) ? $inputs["tag"] : "";
		$init["created"] = date("Y-m-d H:i:s");  
		//根据客户的信息自动匹配分组
		$init["team"] = 1;
		$result = $this->erp_conn->insert(self::TBL, $init);
		$id = $this->erp_conn->insert_id();
		return $id > 0 ? $id : 0;
	}
	
	//修改档案记录
	public function editRecord($id, $inputs)
	{
		if(empty($id) || empty($inputs))
		{
			return array();
		}
		$init['cli_name'] = $inputs["cli_name"] ? $inputs["cli_name"] : "";
		$init['cli_source'] = $inputs["cli_source"] ? $inputs["cli_source"] : 0;
		$init['channel'] = $inputs["channel"] ? $inputs["channel"] : 0;
		$init["cli_gender"] = isset($inputs["cli_gender"]) ? $inputs["cli_gender"] : 0;
		$init["cli_birth"] = $inputs["cli_birth"] ? $inputs["cli_birth"] : "";
		$init["cli_constellation"] = $inputs["cli_constellation"] ? $inputs["cli_constellation"] : "";
		$init["cli_edu"] = $inputs["cli_edu"] ? $inputs["cli_edu"] : 0;
		$init["cli_nick"] = $inputs["cli_nick"] ? $inputs["cli_nick"] : "";
		$init["cli_race"] = $inputs["cli_race"] ? $inputs["cli_race"] : "";
		$init["cli_weibo"] = $inputs["cli_weibo"] ? $inputs["cli_weibo"] : "";
		$init["cli_mobile"] = $inputs["cli_mobile"] ? $inputs["cli_mobile"] : "";
		$init["cli_tel"] = isset($inputs["cli_tel"]) ? $inputs["cli_tel"] : "";
		$init["cli_defined_phone"] = isset($inputs["cli_defined_phone"]) ? $inputs["cli_defined_phone"] : "";   //待修改，用，链接
		$init["cli_qq"] = $inputs["cli_qq"] ? $inputs["cli_qq"] : "";  //待修改，用，链接
		$init["cli_weixin"] = $inputs["cli_weixin"] ? $inputs["cli_weixin"] : "";   //待修改，用，链接
		$init["cli_blood"] = $inputs["cli_blood"] ? $inputs["cli_blood"] : 0;
		$init["cli_email"] = $inputs["cli_email"] ? $inputs["cli_email"] : "";
		$init["country"] = $inputs["country"] ? $inputs["country"] : 0;
		$init["province"] = $inputs["province"] ? $inputs["province"] : 0;
		$init["city"] = $inputs["city"] ? $inputs["city"] : 0;
		$init["street"] = $inputs["street"] ? $inputs["street"] : "";
		$init["postcode"] = $inputs["postcode"] ? $inputs["postcode"] : "";
		$init["comment"] = $inputs["comment"] ? $inputs["comment"] : "";
		$init["tag"] = $inputs["tag"] ? $inputs["tag"] : "";
		$init["team"] = 1;  //根据客户的信息自动匹配分组
		
		$result = $this->erp_conn->where('id', $id)->update(self::TBL, $init);
		return $result ? $result : array();
	}




	//根据id查询档案记录
	public function getRecordById($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//根据手机号查询档案记录
	public function getRecordByPhone($phone)
	{
		if(empty($phone))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('cli_mobile', $phone)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//删除档案记录
	public function delRecord($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('id', $id)->delete(self::TBL);
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
	
	//删除多个档案记录
	public function delRecords($ids)
	{
		if(empty($ids))
		{
			return array();
		}
		$count = 0;
		foreach ($ids as $key => $id)
		{
			if($this->delRecord($id)) $count++;
		}

        return $count;
	}
	
	/**
	 * 根据关键字查询客户档案记录
	 * @param type $keywords
	 * @param type $page
	 * @param type $pagesize
	 * @param type $where
	 */
	public function getRecordByKeyword($keywords, $page = 1, $pagesize = self::PAGESIZE, $where = array())
	{
		$offset = ($page-1)*$pagesize;
		$total = $this->erp_conn->count_all_results(self::TBL);
		if($keywords != "")
		{
			$result = $this->erp_conn->where($where)->limit($pagesize)->offset($offset)->order_by('id desc')->get(self::TBL)->result_array();
		}else
		{
			$where_sql = "concat(cli_name,cli_nick,cli_number) LIKE '%$keywords%'";
			$result = $this->erp_conn->where($where_sql)->where($where)->limit($pagesize)->offset($offset)->order_by('id desc')->get(self::TBL)->result_array();
		}
		$info = array(
			"total" => $total,
			"rows" => $result,
		);
		return $info;
	}
	
	//根据ew_uid查询档案记录
	public function getRecordByEwuid($id)
	{
		if(empty($id))
		{
			return array();
		}
		
		$result = $this->erp_conn->where('ew_uid', $id)->get(self::TBL)->row_array();
		$result = (! empty($result)) ? $result : array();

        return $result;
	}
}