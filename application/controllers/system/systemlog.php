<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Systemlog extends App_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('system/systemlog/index');
    }

    /**
     * 获取操作内容列表,分页
     */
    public function get_AllLogs()
    {
        $inputs = $this->input->get();
        $pagesize = intval($inputs['pagesize']);
        $page = (! empty($inputs['page'])) ? intval($inputs['page']) : 1;

        //查询条件
        $key = array(
            'user' => trim($inputs['user']),
            'do_type' => trim($inputs['do_type']),
            'module' => intval($inputs['module']),
            'order_bill' => $inputs['order_bill'],
            'content' => $inputs['content'],
            'from' => $inputs['from'],
            'to' => $inputs['to'],
        );

        //拼装sql查询语句
        if($key['user'] != ''){
            $sql_user = " and erp_user.username like '%".$key['user']."%' ";
        }else{
            $sql_user = '';
        }

        if($key['do_type'] != ''){
            $sql_do_type = " and erp_func.do_type = ".$key['do_type']."";
        }else{
            $sql_do_type = '';
        }

        if($key['module'] != ''){
            $ids_list = $this->func->getAllCld($key['module']);
            $sql_module = " and erp_log.func_id in (".implode(',', $ids_list).")";
        }else{
            $sql_module = '';
        }

        if($key['from'] != ''){
            $sql_from = " and erp_log.create_time >= '".$key['from']."'";
        }else{
            $sql_from = '';
        }

        if($key['to'] != ''){
            $sql_to = " and erp_log.create_time <= '".$key['to']."'";
        }else{
            $sql_to = '';
        }

        if($key['content'] != ''){
            $sql_content = " and erp_log.content like '%".$key['content']."%' ";
        }else{
            $sql_content = '';
        }

        if($page != 1){
            $sql_limit = " limit ".($page - 1)*$pagesize." , ".$pagesize." ";
        }else{
            $sql_limit = " limit ".$pagesize." ";
        }

        $con = "SELECT erp_user.username as user, erp_log.func_id, erp_func.func_name as function_name, erp_log.id, erp_log.content, erp_log.operate_ip, erp_log.create_time from ew_erp_sys_log as erp_log join ew_erp_sys_func as erp_func on  erp_func.id = erp_log.func_id join ew_erp_sys_user as erp_user on erp_user.id = erp_log.user_id where erp_log.type = 0 and erp_user.id = erp_log.user_id and erp_func.id = erp_log.func_id ";

        $base = $con . $sql_user. $sql_do_type. $sql_module . $sql_from . $sql_to .$sql_content;
        $order_by = " order by erp_log.id desc ";
        $sql = $base. $order_by. $sql_limit;
//        echo $sql;die();
        //获取所有符合条件的数据数量
        $result_all = $this->erp_conn->query($base)->result_array();
        $result = $this->erp_conn->query($sql)->result_array();

        $infos = array(
            'total' => count($result_all),
            'rows' => $result,
        );
        return (! empty($infos)) ? success($infos) : success(array());
    }
    /**
     * 获取系统内容列表,分页
     */
    public function get_AllLogs2(){
        $inputs = $this->input->get();
        $pagesize = intval($inputs['pagesize']);
        $page = (! empty($inputs['page'])) ? intval($inputs['page']) : 1;

        //查询条件
        $key = array(
            'user' => trim($inputs['user']),
            'content' => trim($inputs['content']),
            'from' => $inputs['from'],
            'to' => $inputs['to'],
        );
        $result = $this->log->getPageList($pagesize, $page, $key);

        return (! empty($result['rows'])) ? success($result) : success(array());
    }

}
