<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * author: easywed
 * createTime: 15/10/14 14:16
 * description:商机计数表model,此表存储每个手机号所添加商机的数量
 */

class Business_counter_model extends MY_Model
{
    protected $_table = 'business_counter';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 更新计数
     * @param string $op
     * @param int $times
     * @param $conditions
     * @return mixed
     */
    public function updateCounts($op = '+' , $times = 1 , $conditions){
        $this->erp_conn->set('counts' , 'counts' . $op . $times , false);

        foreach($conditions as $k => $v)
        {
            $this->erp_conn->where($k,$v);
        }

        $this->erp_conn->update($this->_table);
        return $this->erp_conn->affected_rows();
    }
}

