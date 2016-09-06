<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 客户标签
 */

class Login_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'erp_sys_user';
    //账号正常状态
    const STATUS_NORMAL = 1;
    //账号禁用
    const STATUS_FOBBIDEN = 0;
    /**
     * 登录
     * @return array
     */
    public function signin($username = "", $password = "")
    {
		$username = trim($username);
		$password = md5(trim($password));
//        print_r($this->erp_conn);exit;
//        $ressult = $this->erp_conn->where('status',self::STATUS_NORMAL)->where(array('username' => $username, "password" => $password))->get(self::TBL)->row_array();
		$ressult = $this->erp_conn->where('status',self::STATUS_NORMAL)->where(array('mobile' => $username, "password" => $password))->get(self::TBL)->row_array();
        return (! empty($ressult)) ? $ressult : array();
    }

}