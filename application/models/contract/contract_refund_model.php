<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * author: easywed
 * createTime: 15/10/14 16:28
 * description:签约主表model
 */

class Contract_refund_model extends MY_Model
{
    protected $_table = 'sign_refund_apply';

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 回款申请的状态
     */
    public function getRefundStatus()
    {
        $status = array(
            'confirming' => 5,
            'confirmed' => 10,
            'finished' => 15,
            'rejected' => 20,
            );

        $status_explan = array(
            'confirming' => "待确认",
            'confirmed' => "已确认",
            'finished' => "已完成",
            'rejected' => "已驳回",
            );
        return array($status,$status_explan);
    }

    /**
     * 同步驳回状态到主站
     */
    public function syncRefundStatus($data)
    {
        if(count($data) <= 0)
        {
            return false;
        }

        $ret = $this->curl->post($this->config->item('ew_domain') . '/erp/contract/audit-back-money' , $data);

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
     * 获取回款申请列表
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
            $this->erp_conn->join('sign_contract' , $this->_table . '.cid = sign_contract.id' , $join_type);

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
            $this->erp_conn->join('sign_contract' , $this->_table . '.cid = sign_contract.id' , $join_type);

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