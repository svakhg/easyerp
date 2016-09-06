<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * author: easywed
 * createTime: 15/12/25 11:27:58
 * description:顾问工具-短信model.
 */

class Tools_model extends MY_Model
{
    const TBL = 'tools';

    /**
     * 给客户推荐酒店
     */
    const STATUS_CUSTOMER = 1;

    /**
     * 给销售客户信息
     */
    const STATUS_SALES = 2;

    /**
     * 给客户挽救短信
     */
    const STATUS_SAVE = 3;


    /*
     * 发送短信
     */
	
    public function increase($data){
        if(!$data['bid']){
            return failure('bid error!');
        }
        $result = $this->erp_conn->insert(self::TBL, $data);
        return $result ? $this->erp_conn->insert_id() : 0;
    }
    //获取顾问工具 短信列表
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
	
}