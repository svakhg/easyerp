<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 每天将三天前跟进中的商机置为三天以上跟进中-系统设置
 * 3跟进中 -> 15三天以上跟进中-系统设置
 * 更新状态见business_model.php中function getBusinessStatus()
 */

class Followtoinvalid extends Base_Controller
{
	private $ruleDay = 3;//间隔天数

    public function __construct()
    {
        parent::__construct();
    }

    public function main()
    {
        //取消自动更新 by luguangqing
        exit;
        echo $upd_sql = "UPDATE `ew_business` SET `status` = 15 WHERE `status` = 3 AND FROM_UNIXTIME(createtime, '%Y-%m-%d') < FROM_UNIXTIME(UNIX_TIMESTAMP()-".(int)$this->ruleDay."*24*3600, '%Y-%m-%d')";
        $result = $this->erp_conn->query($upd_sql);
        var_dump($result);
    }


}
