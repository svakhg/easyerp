<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: abel
 * Date: 2015/6/8
 * Time: 14:58
 */
class Shopper_demand_order_model extends MY_Model {

    const TBL = 'shopper_demand_order';

    const UNEXAMINED_STATUS = 21;

    public function __construct(){
        parent::__construct();

    }

    /*
     * 查询待审的商家意向书总数
     */
    public function getWait_examined_letterOrder(){

        $result = $this->ew_conn
            ->where('status',self::UNEXAMINED_STATUS)
            ->where('wish <>','')
            ->from(self::TBL)
            ->count_all_results();
//        echo $result;die();
        return $result;
    }
}