<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * 每天修改过了婚期的合同中款项状态
 × 25：已返首次款 -》 30：待返剩余款项
 */
class Paystatuscron extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function main()
    {
        echo $upd_sql = "UPDATE ew_sign_contract SET contract_status = 30 WHERE contract_status=25 AND FROM_UNIXTIME(wed_date,'%Y-%m-%d') > FROM_UNIXTIME(UNIX_TIMESTAMP(),'%Y-%m-%d')";
        $result = $this->erp_conn->query($upd_sql);
        var_dump($result);
    }


}
