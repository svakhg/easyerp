<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Base_Controller
 * 本站核心基类
 * 不需做权限及登陆验证的继承此类，如对外接口
 */
class Base_Controller extends CI_Controller
{
    function __construct()
    {
        header("Content-type:text/html;charset=utf-8");
        parent::__construct();
        $this->erp_conn = $this->load->database('erp',true);//载入默认的erp数据库
        $this->ew_conn = $this->load->database('easywed',true);//载入主站数据库
        $this->load->library('curl');
        $this->load->library('sms');
        $this->load->helper('static_url_version');
        require_once dirname(__DIR__).'/libraries/aliyun-oss/autoload.php';
    }

}


/**
 * Class App_Controller
 * 本站基类
 * 需要做权限及登陆验证的模块要继承此类
 */
class App_Controller extends Base_Controller{

    protected  $_data = array();

    function __construct()
    {
        parent::__construct();
        //验证后台权限
        if(!$this->session->userdata('admin')){
            redirect('account/login');
        }
        $this->_pre_construct();
    }

    protected function _pre_construct()
    {
        $this->load->helper('array');
        $this->load->model('sys_func_model', 'func');
        $this->load->model('sys_log_model','log');

        $this->_data['config'] = $this->config->config;
        //获取用户权限
        $id = $this->session->userdata("admin_id");
        $this->load->model('sys_user_model', 'user');
        $user_auth = $this->user->getUserPermisssions($id);

        //判断用户是否有权限  取控制器名和方法名
        $directory = substr($this->router->fetch_directory(),0,-1); //分组目录
        $controller = $this->router->fetch_class();   //当前控制器
        $action = $this->router->fetch_method();    // 当前使用方法
        $controller_url = $directory ? $directory.'/'.$controller  : $controller;  //控制器名
//---------------------------------------------------------------------------------
        //获取一二级菜单信息 代码块
        $func_infos = $this->func->getInfos(0, 1, $user_auth);
        foreach($func_infos as &$v)
        {
            $func_two = $this->func->getInfos($v['id'], 2, $user_auth);
            $v['func_two'] = ! empty($func_two) ? $func_two : array();
            if(!empty($func_two) && empty($curr_page_id))
            {
                foreach ($func_two as $key => $value)
                {
                    if($controller_url == $value['controller'] && $action == $value['action'])
                    {
                        $curr_page_id = $value['id'];
                        break;
                    }
                }
            }
        }
        $this->_data['func_infos'] = $func_infos;
        //获取当前菜单功能信息
        $cur_id = $this->func->getIdByController($controller_url, 'index');//获取当前菜单的id
        $cur_info = $this->func->getInfoById($cur_id);
        $this->_data['cur_info'] = $cur_info;
//---------------------------------------------------------------------------------
        // 获取当前页面的子元素 也就是数据库中的第三级内容
        $this->_data['page_permission'] = array();
        if(!empty($curr_page_id))
        {
            $page_child = $this->func->getInfosByPid($curr_page_id);
            foreach($page_child as $val)
            {
                if(in_array($val['id'], $user_auth))
                {
                    $this->_data['page_permission'][] = $val['controller'] . '/' . $val['action'];
                }
            }
        }
//---------------------------------------------------------------------------------
        if($controller_url == "home" && $action == "index")
        {
            $this->load->view('header/header_view.php',$this->_data);
        }else
        {
            $auth_id = $this->func->getIdByController($controller_url, $action);
            if(!in_array($auth_id, $user_auth))
            {
                //echo 111;exit();
                show_404();
            }else
            {
                $this->load->view('header/header_view.php',$this->_data);
            }
        }
    }
}
