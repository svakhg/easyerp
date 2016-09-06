<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * author: easywed
 * createTime: 15/10/14 16:28
 * description:签约主表model
 */

class Contract_model extends MY_Model
{
    protected $_table = 'sign_contract';

    public function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * 合同状态
	 * @return type
	 */
	public function getContractStatus()
	{
		 $contractstatus = array(
            'to_upload' => 1, //待上传合同
            'to_confirm' => 10, //待确认合同
            'confirmed' => 20, //已确认
            'completed' => 30, //已完成
            'stop' => 40, //已中止
			'reject' => 50, //已驳回
			'invalid' => 60, //无效
        );

        $contractstatus_explan = array(
            'to_upload' => '待上传合同',
            'to_confirm' => '待确认合同',
            'confirmed' => '已确认',
            'completed' => '已完成',
            'stop' => '已中止',
			'reject' => '已驳回',
			'invalid' => '无效', 
        );
        return array($contractstatus , $contractstatus_explan);
	}
	
	/**
	 * 归档状态
	 * @return type
	 */
	public function getArchiveStatus()
	{
		$archivestatus = array(
			'no_archive' => 1, //未归档
			'archived' => 2, //已归档
		);
		
		$archivestatus_explan = array(
			'no_archive' => '未归档', 
			'archived' => '已归档', 
		);
		 return array($archivestatus , $archivestatus_explan);
	}

    /**
     * 合同类型
     * @return array
     */
    public function getTypes()
    {
        $type_value = array(
            'two' => 2,
            'three' => 1
        );
        $type_explan = array(
            'two' => '双方合同',
            'three' => '三方合同'
        );
        return array($type_value, $type_explan);
    }
	
	/**
	 * 款项状态
	 * @return type
	 */
	public function getFundStatus()
	{
		$fundstatus = array(
			'topay_advance' => 1, //待付定金
			'paid_advance' => 10, //已付定金
			'first_back' => 20, //待首次返款
			'already_first_back' => 25, //已返首次款
			'remainder_back' => 30, //待返剩余款项
			'all_back' => 40, //已全部返款
		);
		
		$fundstatus_explan = array(
			'topay_advance' => '待付定金', 
			'paid_advance' => '已付定金', 
			'first_back' => '待首次返款', 
			'already_first_back' => '已返首次款',
			'remainder_back' => '待返剩余款项', 
			'all_back' => '已全部返款', 
		);
		 return array($fundstatus , $fundstatus_explan);
	}
	
	/**
	 *  搜索条件处理
	 * @param array $cond
	 */
	public function conditions(array $cond)
	{
		$this->erp_conn->select("$this->_table.*, b.tradeno, b.follower_uid")->join("business as b", "$this->_table.bid = b.id", 'left');
		// 常规where处理
        if(!empty($cond['autowhere']) && is_array($cond['autowhere']))
        {
            foreach ($cond['autowhere'] as $field => $val)
            {
                if(is_array($val))
                {
                    $this->erp_conn->where_in($field, $val);
                }
                else
                {
                    $this->erp_conn->where($field, $val);
                }
            }
        }
		
		//签约时间处理
        if(!empty($cond['sign_time_start']) || !empty($cond['sign_time_end']))
        {
            $start = !empty($cond['sign_time_start']) ? strtotime($cond['sign_time_start']) : strtotime(date('Ymd'));
            $end = !empty($cond['sign_time_end']) ? strtotime($cond['sign_time_end']) : strtotime(date('Ymd') . '235959');
            if($start > $end)
            {
                $this->erp_conn->where("sign_time BETWEEN {$end} AND {$start}");
            }
            else
            {
                $this->erp_conn->where("sign_time BETWEEN {$start} AND {$end}");
            }
        }
		
		// 婚礼日期日期处理
        if(!empty($cond['wed_date_start']) || !empty($cond['wed_date_end']))
        {
            $start = !empty($cond['wed_date_start']) ? strtotime($cond['wed_date_start']) : strtotime(date('Ymd'));
            $end = !empty($cond['wed_date_end']) ? strtotime($cond['wed_date_end']) : strtotime(date('Ymd') . '235959');
            if($start > $end)
            {
                $this->erp_conn->where("sign_contract.wed_date BETWEEN {$end} AND {$start}");
            }
            else
            {
                $this->erp_conn->where("sign_contract.wed_date BETWEEN {$start} AND {$end}");
            }
        }

        // 上传合同时间处理
        if(!empty($cond['upload_date_start']) || !empty($cond['upload_date_end']))
        {
            $start = !empty($cond['upload_date_start']) ? strtotime($cond['upload_date_start']) : strtotime(date('Ymd'));
            $end = !empty($cond['upload_date_end']) ? strtotime($cond['upload_date_end']) : strtotime(date('Ymd') . '235959');
            if($start > $end)
            {
                $this->erp_conn->where("sign_contract.upload_time BETWEEN {$end} AND {$start}");
            }
            else
            {
                $this->erp_conn->where("sign_contract.upload_time BETWEEN {$start} AND {$end}");
            }
        }
	}
	
	/**
	 * 中止合同同步主站
	 * @param type $contract_num 合同编号
	 */
	public function syncStopContract($contract_num, $reason)
	{
		if(empty($contract_num) || empty($reason))
		{
			return FALSE;
		}
		
		$post_params = array(
			"contract_num" => $contract_num,
			"reason" => $reason,
		);
		$ret = $this->curl->post($this->config->item('ew_domain').'/erp/contract/abeyance', $post_params);

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
	 * 返款完成同步主站
	 * @param type $contract_num
	 */
	public function syncComplete($contract_num)
	{
		if(empty($contract_num))
		{
			return FALSE;
		}
		
		$post_params = array(
			"contract_num" => $contract_num,
		);
		// $ret = $this->curl->post($this->config->item('ew_domain').'/erp/contract/confirm-recvied-money', $post_params);
        $ret = $this->curl->post($this->config->item('ew_domain').'/erp/contract/completed', $post_params);
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

    //	================12月改版新加内容（合同与钱单独审核）==================
    /**
     * 确认合同同步主站
     * @param type $contract_num
     */
    public function syncConfirmContract($contract_num)
    {
        if(empty($contract_num))
        {
            return FALSE;
        }

        $post_params = array(
          "contract_num" => $contract_num,
          "status" => 1,
        );
        $ret = $this->curl->post($this->config->item('ew_domain').'/erp/contract/audit', $post_params);

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
     * 驳回合同同步主站
     * @param type $contract_num 合同编号
     */
    public function syncRejectContract($contract_num, $reason)
    {
        if(empty($contract_num) || empty($reason))
        {
            return FALSE;
        }

        $post_params = array(
          "contract_num" => $contract_num,
          "status" => 0,
          "reason" => $reason,
        );
        $ret = $this->curl->post($this->config->item('ew_domain').'/erp/contract/audit', $post_params);

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
     * 合同归档同步主站
     * @param type $contract_num 合同编号
     */
    public function syncArchive($contract_num)
    {
        if(empty($contract_num))
        {
            return FALSE;
        }

        $post_params = array(
          "contract_num" => $contract_num,
        );
        $ret = $this->curl->post($this->config->item('ew_domain').'/erp/contract/archive', $post_params);

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

    /*
     * 查询签约信息是否存在
     *
     * */
    public function  getfindById($id){
        if(empty($id) || !is_numeric($id)){
            return failure('合同id错误');
        }
        $where =' id ='.$id.' and contract_status < 60 and is_del = 0';
        $sql = 'select id,contract_status,contract_num,type,funds_status from ew_sign_contract where '.$where;
        $payment = $this->erp_conn->query($sql)->row_array();
        if(!empty($payment)){
            return $payment;
        }else{
            return '';
        }
    }
}
