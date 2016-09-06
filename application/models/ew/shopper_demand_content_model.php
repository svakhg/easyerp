<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: abel
 * Date: 2015/6/8
 * Time: 14:58
 */
class Shopper_demand_content_model extends MY_Model {

    const TBL = 'shopper_demand_content';

    const UNEXAMINED_STATUS_NO_SHOPPER = 1;//未审核.无商家

    const UNEXAMINED_STATUS_HAVE_SHOPPER = 4;//未审核.有商家

    public function __construct(){
        parent::__construct();

    }

    /*
     * 查询待审核商家需求 总数
     */
    public function getWait_examined_bid_demand(){

        $result = $this->ew_conn
            ->where('status',self::UNEXAMINED_STATUS_NO_SHOPPER)->or_where('status',self::UNEXAMINED_STATUS_HAVE_SHOPPER)
            ->from(self::TBL)
            ->count_all_results();
//        echo $result;die();
        return $result;
    }
}