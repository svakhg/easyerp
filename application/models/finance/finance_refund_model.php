<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * author: easywed
 * createTime: 15/12/29 18:06
 * description:财务返款model
 */

class Finance_refund_model extends MY_Model
{
    protected $_table = 'sign_finance_refund';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取返款状态
     * @return array
     */
    public function getWithDrawStatus()
    {
        $wd_status = array(
            'wait_refund' => 1, //待返款
            'done_refund' => 2, //已返款
        );

        $status_explan = array(
            'wait_refund' => '待返款',
            'done_refund' => '已返款'
        );

        return array($wd_status , $status_explan);
    }

    /**
     * 获取提现类型
     * @return array
     */
    public function getWithDrawType()
    {
        $wd_type = array(
            'both' => 1, //双方提现
            'three' => 2, //三方提现
        );

        $type_explan = array(
            'both' => '双方',
            'three' => '三方'
        );

        return array($wd_type , $type_explan);
    }

    /**
     * 同步三方合同返款数据到主站
     * @param array $data
     * @return bool
     */
    public function syncToBackMoney($data = array())
    {
        if(count($data) <= 0)
        {
            return false;
        }

        $ret = $this->curl->post($this->config->item('ew_domain') . '/erp/contract/add-back-money' , $data);

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
     * 同步双方合同返款数据到主站
     * @param array $data
     * @return bool
     */
    public function syncToCash($data = array())
    {
        if(count($data) <= 0)
        {
            return false;
        }

        $ret = $this->curl->post($this->config->item('ew_domain') . '/erp/contract/confirm-cashing' , $data);

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
     * 获取财务返款列表
     * @param $condition
     * @param $limit
     * @param bool $get_nums
     * @param string $sel_fields
     * @return mixed
     */
    public function findExtra($condition,$limit, $get_nums = false, $sel_fields = '*' , $join_type = 'left')
    {

        if($get_nums)
        {
            $this->erp_conn->from($this->_table);
            $this->erp_conn->join('sign_contract_payment_details' , $this->_table . '.payment_id = sign_contract_payment_details.id' , $join_type);

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
        }
        else
        {
            $this->erp_conn->select($sel_fields);
            $this->erp_conn->from($this->_table);
            $this->erp_conn->join('sign_contract_payment_details' , $this->_table . '.payment_id = sign_contract_payment_details.id' , $join_type);

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

            if(isset($limit['nums']) && $limit['nums'] > 0)
            {
                $this->erp_conn->limit($limit['nums'] , $limit['start']);
            }
            return $this->erp_conn->get()->result_array();
        }
    }
}

