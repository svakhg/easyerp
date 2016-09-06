<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 系统日志
 */

class Sys_log_model extends MY_Model{

    /**
     * 数据库表名
     * @var string
     */
	const TBL = 'ew_erp_sys_log';

    //操作日志
    const OPER_TYPE = 0;
    //系统日志
    const LOGIN_TYPE = 1;

    /**
     * 记录日志
     */
    public function add($data)
    {
        if(empty($data))
        {
            return failure('参数错误');
        }
        return $this->erp_conn->insert(self::TBL, $data);
    }

    public function addlog($log_userid, $type=1, $log_content_str='',$order_bill='')
    {
        //拿到接口
        $CI = & get_instance();
        //取得功能id
        $directory = substr($CI->router->fetch_directory(),0,-1); //分组目录
        $controller = $CI->router->fetch_class();   //当前控制器
        $action = $CI->router->fetch_method();    // 当前使用方法
        $controller_url = $directory ? $directory.'/'.$controller  : $controller;  //控制器名
        $func_id = $CI->func->getIdByController($controller_url, $action);
        $func_id = ! $func_id ? 0 : $func_id;

        if($log_userid)
        {
            $data['user_id'] = $CI->session->userdata('admin_id');
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['func_id'] = $func_id;
            $data['operate_ip'] = $CI->input->ip_address();
            $data['operate_mac'] = '';
            $data['content'] = $log_content_str;
            $data['client_version'] = 1;
            $data['order_bill'] = $order_bill;
            $data['type'] = $type;

            $this->erp_conn->insert(self::TBL, $data);
        }
    }

    /**
     * 删除
     */
    public function delById($id)
    {
        $result = $this->erp_conn->where('id', $id)->delete(self::TBL);
        return $result ? "删除成功" : "删除失败";
    }


    /**
     * 根据关键字数组获取指定的数据
     */
    public function getAllInfos()
    {
        $this->erp_conn->from(self::TBL);
        $result = $this->erp_conn->get()->result_array();
        return (! empty($result)) ? $result : array();
    }

    /**
     * 分页获取数据
     * @param $id
     * @param $pagesize
     * @param int $page
     * @return array
     */
    public function getPageList($pagesize, $page = 1, $key = array('user' => '','content' => '','from' => '','to' => ''))
    {
        $offset = '';
        if($page == 1 || $page < 1 )
        {
            $offset = 1;
        }else{
            $offset = ($page -1)*$pagesize;
        }

        $like = array();
        $where = array();
        //操作类型为1是系统
        $where['log.type'] = self::LOGIN_TYPE;

        if($key['user']!= '')
        {
            $like['user.username'] = $key['user'];
        }

        if(! empty($key['content']))
        {
            $like['log.content'] = $key['content'];
        }

        if($key['from']!= '')
        {
            $where['log.create_time >= '] = $key['from'];
        }

        if($key['to']!= '')
        {
            $where['log.create_time <= '] = $key['to'];
        }

        if($offset != 1)
        {
            $this->erp_conn->offset($offset);
        }

        $result = $this->erp_conn->select('log.id, user.username as user, log.content, log.operate_ip, log.create_time')->from(self::TBL." as log")->join('ew_erp_sys_user as user','user.id = log.user_id')->where($where)->like($like)->order_by('log.id desc')->limit($pagesize)->get()->result_array();
        $result = (! empty($result)) ? $result : array();
//        echo $this->erp_conn->last_query();die();
        $total = $this->erp_conn->select('log.id, user.username as user, log.content, log.operate_ip, log.create_time')->from(self::TBL." as log")->join('ew_erp_sys_user as user','user.id = log.user_id')->where($where)->like($like)->count_all_results();

        $infos = array(
            'total' => $total,
            'rows' => $result,
        );
        return $infos;
    }
}