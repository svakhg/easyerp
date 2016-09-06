<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 商机管理公用方法
 * 
 */

class Business_record_model extends MY_Model
{

	const TBL = 'records';
	
	/**
	 * 沟通记录
	 */
	const TYPE_RECORD = 0;
	
	/**
	 * 内部备注
	 */
	const TYPE_REMARK = 1;
	
    public function __construct(){
        parent::__construct();
    }
	
	/**
	 * 添加一条数据
	 * @param type $data
	 * @return int
	 */
	public function increase($data)
	{
		if(empty($data))
		{
			return 0;
		}
		
		$result = $this->erp_conn->insert(self::TBL, $data);
		return $result ? $this->erp_conn->insert_id() : 0;
	}
	
	/**
	 * 获取列表
	 * @param type $where
	 * @param type $page
	 * @param type $pagesize
	 * @return type
	 */
	public function getList($where = array(), $page = 1, $pagesize = 10)
	{
		//分页
		if($page == 1 || $page < 1 )
        {
            $offset = 0;
        }else{
            $offset = ($page -1)*$pagesize;
        }
		$obj = $this->erp_conn->where($where)->limit($pagesize, $offset);
		
		$dbhandle = clone($obj);
		$total = $dbhandle->count_all_results(self::TBL);
		$rows = $obj->get(self::TBL)->result_array();
		
		return array("total" => $total, "rows" => $rows);
	}
	
	/**
	 * 获取一条记录
	 * @param type $id
	 * @return type
	 */
	public function getRow($id)
	{
		if(!$id)
		{
			return array();
		}
		
		return $this->erp_conn->where("id", $id)->get(self::TBL)->row_array();
	}
	
	/**
	 * 修改
	 * @param type $id
	 * @param type $upt
	 * @return type
	 */
	public function updateRow($id, $upt)
	{
		if(empty($id) || empty($upt))
		{
			return array();
		}
		
		return $this->erp_conn->where("id", $id)->update(self::TBL, $upt);
	}
	
	/**
	 * 添加沟通记录同步主站
	 * @param type $erp_id  erp主键id
	 * @param type $tradeno 交易编号
	 * @param type $content 内容
     * @param type $uname 操作人名称
     * @param type $umobile 操作人手机
	 */
	public function syncAddRecord($erp_id, $tradeno, $content , $uname , $umobile, $shopper_ids_visible = '')
	{
		if(empty($erp_id) || empty($tradeno) || empty($content))
		{
			return FALSE;
		}
		
		$post_params = array(
			'erp_id' => $erp_id,
			'business_tradeno' => $tradeno,
			'content' => $content,
			'consultant_name' => $uname,
			'consultant_phone' => $umobile,
			'shopper_ids_visible' => $shopper_ids_visible,
        );
		$ret = $this->curl->post($this->config->item('ew_domain').'/erp/business/communicate-records-add', $post_params);

        $resp = json_decode($ret , true);
        if($resp['result'] == 'succ')
        {
            return true;
        }
        else
        {
            return false;
        }
	}
	
	/**
	 * 修改沟通记录同步主站
	 * @param type $erp_id  erp主键id
	 * @param type $tradeno 交易编号
	 * @param type $content 内容
	 * @return boolean
	 */
	public function syncEditRecord($erp_id, $tradeno, $content , $uname , $umobile, $shopper_ids_visible = '')
	{
		if(empty($erp_id) || empty($tradeno) || empty($content))
		{
			return FALSE;
		}
		
		$post_params = array(
			'erp_id' => $erp_id,
			'business_tradeno' => $tradeno,
			'content' => $content,
			'consultant_name' => $uname,
			'consultant_phone' => $umobile,
			'shopper_ids_visible' => $shopper_ids_visible,
        );
		$ret = $this->curl->post($this->config->item('ew_domain').'/erp/business/communicate-records-update', $post_params);

        $resp = json_decode($ret , true);
        if($resp['result'] == 'succ')
        {
            return true;
        }
        else
        {
            return false;
        }
	}
	
	/**
	 * 删除沟通记录同步主站
	 */
	public function syncDelRecord()
	{
		
	}
}
