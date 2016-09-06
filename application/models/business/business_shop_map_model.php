<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * description:商机与商家映射表model,此表存储每个商机所对应的商家
 */

class Business_shop_map_model extends MY_Model
{
	protected $_table = 'business_shop_map';
	
    const TBL = 'business_shop_map';
	
	/**
	 * 已签约
	 */
	const STATUS_SIGN = 1;
	
	/**
	 * 已丢单
	 */
	const STATUS_LOST = 2;
	
	/**
	 * 商家应标（n进4）
	 */
	const STATUS_ALLOW = 5;
	
	/**
	 * 商家约见（4进2）（原未见面时无状态）
	 */
	const STATUS_NOT = 0;
	

	/**
	 * 已见面
	 */
	const FACE_STATUS_MEET = 2;

	/**
	 * 未见面
	 */
	const FACE_STATUS_NOT_MEET = 1;

    public function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * 添加一条记录
	 * @param array $data 以为数组
	 * @return int 插入记录id
	 */
	public function increase($data)
	{
		if(empty($data))
		{
			return 0;
		}
		
		$data["allocatetime"] = time();
		
		$result = $this->erp_conn->insert(self::TBL, $data);
		return $result ? $this->db->insert_id() : 0;
	}
	
	/**
	 * 添加多条记录
	 * @param type $data_arr  二维数组
	 * @return mixed 插入成功返回执行条数，失败返回false
	 */
	public function increase_batch($data_arr)
	{
		if(empty($data_arr))
		{
			return 0;
		}
		
		$result = $this->erp_conn->insert_batch(self::TBL, $data_arr);
		return $result;
	}
	
	/**
	 * 根据id取一条记录
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
	 * 删除一条记录
	 * @param type $id
	 * @return type
	 */
	public function removeRow($id)
	{
		return $this->erp_conn->where("id", $id)->delete(self::TBL);
	}
	
	/**
	 * 根据商机id获取列表
	 * @param type $bid
	 * @return type
	 */
	public function getShoperidsBybid($bid)
	{
		if(!$bid)
		{
			return array();
		}
		
		if(is_array($bid))
        {
            return $this->erp_conn->where_in("bid", $bid)->get(self::TBL)->result_array();
        }
        else
        {
            return $this->erp_conn->where("bid", $bid)->get(self::TBL)->result_array();
        }
	}

    /**
     * 根据商机id获取应标列表
     * @param type $bid
     * @return type
     */
    public function getShoperids($bid,$shop_id)
    {
        if(!$bid || !$shop_id)
        {
            return array();
        }
        return $this->erp_conn->where("bid", $bid)->where_in('shop_id',$shop_id)->where('status',self::STATUS_NOT)->get(self::TBL)->result_array();

    }
	
	/**
	 * 取列表 带分页
	 * @param type $condition
     * @param $limit 分页条件,array('nums' => 10, 'start' => 20),nums分页条数，start查询起始
     * @param $order 排序
     * @param string $sel_fields 查询字段
     * @return array
	 */
	public function getShoperList($condition, $limit ,$order, $sel_fields = '*')
	{
		$query_data = $this->findAll($condition, $limit ,$order, $sel_fields = '*');
		
		return $query_data;
	}
	
	

    /**
     * 根据shopid获取列表
     * @param int $shop_id
     * @return array
     */
    public function getInfoByShopId($shop_id = 0)
    {
        if($shop_id <= 0)
        {
            return array();
        }

        return $this->erp_conn->where('shop_id' , $shop_id)->get(self::TBL)->result_array();
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
     * 同步策划师丢单信息至主站
     * @param $tradeno
     * @param $shoper_uid
     * @return bool
     */
    public function syncLostOrder($tradeno , $shoper_uid , $reason = '')
    {
        if(!$tradeno || !$shoper_uid)return false;
        $post_params = array(
            'tradeno' => $tradeno,
            'shopper_uid' => $shoper_uid,
            'reason' => $reason
        );
        $ret = $this->curl->post($this->config->item('ew_domain').'/erp/business/shopper-throw', $post_params);

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
}

