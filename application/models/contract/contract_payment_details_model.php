<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * author: easywed
 * createTime: 15/10/14 16:28
 * description:款项表model
 */

class Contract_payment_details_model extends MY_Model
{
	protected $_table = 'sign_contract_payment_details';

    public function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * 款项状态
	 * @return type
	 */
	public function getPaymentStatus()
	{
		$paymentstatus = array(
			'to_confirm' => 1, //待确认
			'confirmed' => 5, //已确认
			'reject' => 10, //已驳回
		);
		
		$paymentstatus_explan = array(
			'to_confirm' => '待确认', 
			'confirmed' => '已确认', 
			'reject' => '已驳回', 
		);
		return array($paymentstatus , $paymentstatus_explan);
	}

	/**
	 * 支付方式
	 * @return type
	 */
	public function getPayMode()
	{
		$paymode = array(
			'store' => 1, //现下门店
            'alipay' => 2, //支付宝-转账
			'e_bank' => 3, //网银转账
			'cash' => 4, //现金
            'other' => 5, //其他
            'weixin_face' => 6, //微信-当面付
            'alipay_face' => 7, //支付宝-当面付
            'weixin_web' => 8, //微信-网站
            'alipay_web' => 9, //支付宝-网站
            'weixin_mobile' => 10, // 微信 - M站
            'alipay_mobile' => 11, // 支付宝 - M站
		);
		
		$paymode_explan = array(
			'store' => '线下门店',
			'alipay' => '支付宝-转账',
			'e_bank' => '网银转账', 
			'cash' => '现金',
            'alipay_face' => '支付宝-当面付',
            'alipay_web' => '支付宝-网站',
            'weixin_face' => '微信-当面付',
            'weixin_web' => '微信-网站',
			'other' => '其他',
            'weixin_mobile' => '微信-M站',
            'alipay_mobile' => '支付宝-M站'
		);
		return array($paymode , $paymode_explan);
	}
	
	/**
	 * 款项类型
	 * @return type
	 */
	public function getPaymentType()
	{
		$paymenttype = array(
			'advance' => 1, //定金
			'shoper_fund' => 2, //服务商款
			'sitelayout_fund' => 3, //场地布置款
			'addition_fund' => 4, //增补款
			'final_fund' => 5, //尾款
            'other' => 8, //其他
			'payback' => 10, //回款
            'before_payback' => 15, //婚礼前回款
            'after_payback' => 20, //婚礼后回款
            'both_payback' => 25, //双方合同回款
		);
		
		$paymenttype_explan = array(
			'advance' => '定金', 
			'shoper_fund' => '服务商款', 
			'sitelayout_fund' => '场地布置款', 
			'addition_fund' => '增补款', 
			'final_fund' => '尾款',
            'other' => '其他', //其他
			'payback' => '回款',
            'before_payback' => '婚礼前回款',
            'after_payback' => '婚礼后回款',
            'both_payback' => '双方合同回款'
		);
		return array($paymenttype , $paymenttype_explan);
	}
    /**
     * 获取收款项列表
     * @param $condition
     * @param $limit
     * @param bool $get_nums
     * @param string $sel_fields
     * @return mixed
     */

    public function findPaymentJoinExtra($condition,$limit, $get_nums = false, $sel_fields = '*')
    {

        if($get_nums)
        {
            $this->erp_conn->from($this->_table);
            $this->erp_conn->join('sign_contract' , $this->_table . '.cid = sign_contract.id');
            foreach($condition as $k => $v)
            {
                if(is_array($v))
                {
                    $this->erp_conn->where_in($k , $v);
                }
                else
                {
                    $this->erp_conn->where($k , $v);
                }

            }
            $this->erp_conn->where('fund_type !=' , 10);
          return $this->erp_conn->count_all_results();
          // echo $this->erp_conn->last_query();
        }
        else
        {
            $this->erp_conn->select($sel_fields);
            $this->erp_conn->from('sign_contract');
            $this->erp_conn->join($this->_table , $this->_table . '.cid = sign_contract.id');
            foreach($condition as $k => $v)
            {
                if(is_array($v))
                {
                    $this->erp_conn->where_in($k , $v);
                }
                else
                {
                    $this->erp_conn->where($k , $v);
                }

            }
            $this->erp_conn->where('fund_type !=' , 10);
            $this->erp_conn->order_by($this->_table . '.id' , 'asc');

            if(isset($limit['nums']) && $limit['nums'] > 0)
            {
                $this->erp_conn->limit($limit['nums'] , $limit['start']);
            }
            return $this->erp_conn->get()->result_array();
        }
    }
    //修改收款状态
    public function findCid($id,$arr)
    {
       return $this->erp_conn->where('id', $id)->update($this->_table,$arr);
    }

	
	/**
     * 条件处理
     */
    public function conditions($cond)
    {
        foreach($cond as $k => $cv)
        {
            if(is_array($cv) && count($cv) > 0){
                $this->erp_conn->where_in($k,$cv);
            }else{
                $this->erp_conn->where($k,$cv);
            }
        }
    }
	
	/**
     * 获取数据
     * @param $condition 查询条件
     * @param $limit 分页条件,array('nums' => 10, 'start' => 20),nums分页条数，start查询起始
     * @param $order 排序条件
     * @param string $sel_fields 查询字段,可逗号分割
     * @return mixed
     */
	public function getList($condition, $limit = array(), $order = array(), $sel_fields = '*')
	{
		$result = $this->findAll($condition, $limit ,$order, $sel_fields = '*');
        return $result;
	}

    /**
     * 同步收款状态到主站
     * @param $data
     * @return bool
     */
    public function syncPaymentStatus($data)
    {
        if(count($data) <= 0)
        {
            return false;
        }

        $ret = $this->curl->post($this->config->item('ew_domain') . '/erp/contract/fund-audit' , $data);

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
	 * 录入回款同步主站
	 * @param type $contract_num
	 * @param type $amount
	 * @param type $mode
	 * @param type $remark
	 * @return boolean
	 */
	public function syncAddPayment($contract_num, $amount, $mode, $remark = "")
	{
		if(empty($contract_num) || empty($amount) || empty($mode))
		{
			return FALSE;
		}
		
		 $post_params = array(
            "contract_num" => $contract_num,
			"amount" => $amount,
			"mode" => $mode,
			"remark" => $remark,
        );
		$ret = $this->curl->post($this->config->item('ew_domain').'/erp/contract/recvied-money', $post_params);

        $resp = json_decode($ret , true);
        if($resp['result'] == 'succ')
        {
			$result = array(
				"result" => TRUE,
				"msg" => "",
			);
        }
        else
        {
            $result = array(
				"result" => FALSE,
				"msg" => $resp["msg"],
			);
        }
		return $result;
	}
    /**
     * 获收支项列表
     * @param $condition
     * @param $limit
     * @param bool $get_nums
     * @param string $sel_fields
     * @return mixed
     */

    public function findExtra($condition,$limit, $get_nums = false, $sel_fields = '*' , $join_type = '')
    {

        if($get_nums)
        {
            $this->erp_conn->from($this->_table);
            if(!empty($join_type))
            {
                $this->erp_conn->join('sign_contract' , $this->_table . '.cid = sign_contract.id' , $join_type);
            }
            else
            {
                $this->erp_conn->join('sign_contract' , $this->_table . '.cid = sign_contract.id');
            }

            foreach($condition as $k => $v)
            {
                if(is_array($v))
                {
                    $this->erp_conn->where_in($k , $v);
                }
                else
                {
                    $this->erp_conn->where($k , $v);
                }

            }

            return  $this->erp_conn->count_all_results();
            //echo $this->erp_conn->last_query();
        }
        else
        {
            $this->erp_conn->select($sel_fields);
            $this->erp_conn->from($this->_table);
            if(!empty($join_type))
            {
                $this->erp_conn->join('sign_contract' , $this->_table . '.cid = sign_contract.id' , $join_type);
            }
            else
            {
                $this->erp_conn->join('sign_contract' , $this->_table . '.cid = sign_contract.id');
            }

            foreach($condition as $k => $v)
            {
                if(is_array($v))
                {
                    $this->erp_conn->where_in($k , $v);
                }
                else
                {
                    $this->erp_conn->where($k , $v);
                }

            }
            $this->erp_conn->order_by($this->_table . '.pay_time' , 'desc');

            if(isset($limit['nums']) && $limit['nums'] > 0)
            {
                $this->erp_conn->limit($limit['nums'] , $limit['start']);
            }
            return $this->erp_conn->get()->result_array();
        }
    }

    /**
     * 根据合同ID和资金状态获取对应的资金总额
     *
     * @param int $contract_id 合同ID
     * @param int $status 资金状态
     */
    public function findByContractID($contract_id, $status = -1)
    {
        if(!is_numeric($contract_id) || $contract_id < 0)
        {
            return;
        }

        $this->erp_conn->from($this->_table);

        $this->erp_conn->where('cid', $contract_id);

        list($paymentstatus, $paymentstatus_explan) = $this->getPaymentStatus();
        if(in_array($status, $paymentstatus))
        {
            $this->erp_conn->where('status', $status);
        }

        $result = $this->erp_conn->select_sum('amount')->get()->row_array();

        return !empty($result) && isset($result['amount']) ? $result['amount'] : 0;

    }
}