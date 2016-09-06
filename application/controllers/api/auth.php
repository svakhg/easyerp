<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper('array');
    }
    /**
     * 添加模块
     * method post
     */

	public function post_AddModule()
	{
        $inputs = $this->input->post();
        $func_name = trim($inputs['func_name']);
        $func_comment = trim($inputs['func_comment']);
        $attr = array(
            'level' => 1,
            'pid' => 0,
            'func_name' => $func_name,
            'controller' => '',
            'action' => '',
            'style' => '',
            'enable' => intval($inputs['enable']),
            'func_comment' => $func_comment,
        );
        $result = $this->func->add($attr);
        //添加操作日志
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "添加了内容");

        return $result ? success("添加成功！") : failure("添加失败！");

	}

    /**
     * 编辑模块
     * method post
     */
    public function post_EditModule()
    {
        $inputs = $this->input->post();
        $id = intval($inputs['id']);

        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }

        $func_name = trim($inputs['func_name']);
        $func_comment = trim($inputs['func_comment']);
        $attr = array(
            'func_name' => $func_name,
            'enable' => intval($inputs['enable']),
            'func_comment' => $func_comment,
        );
        //更新前
        $info_old = $this->func->getInfoById($id);
        //执行编辑
        $result = $this->func->edit($id, $attr);
        //更新后
        $info_new = $this->func->getInfoById($id);

        //变更对比
        $str = array();
        foreach($info_old as $key => $v){
            if($info_new[$key] != $v)
            {
                $str[] = $key.":".$v."=>".$info_new[$key];
            }
        }

        if(!empty($str)){
            $log_content_str = implode(',', $str);
            //添加操作日志
            $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);
        }
        return success($result);
    }

    /**
     * 删除权限管理
     * method get
     */
    public function get_DelAuth()
    {
        $ids = $this->input->post('ids');
        if($ids == '')
        {
            return failure('未找到要删除的数据');
        }
        $ids_array = explode(',', $ids);

        foreach($ids_array as $v)
        {
            //检查记录为id的启用状态
            $infos = $this->func->getInfoById($v);
            if(empty($infos))
            {
                return failure('未找到要删除的数据');
            }
            $result = $this->func->delById($v);
        }
        //添加操作日志
        $log_content_str = "删除了权限设置编号为".$ids."的记录";
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);
        return success("删除成功");
    }

    /**
     * 添加链接权限
     * method post
     */
    public function post_AddLink()
    {
        $inputs = $this->input->post();
        $pid = intval($inputs['pid']);

        if(! is_numeric($pid) || $pid <= 0)
        {
            return failure('参数错误');
        }

        $module_info = $this->func->getInfoById($pid);

        if(! empty($module_info))
        {
            $level = $module_info['level'];
            $pid = $module_info['id'];
        }

        $url_array = explode('/', $inputs['url']);
        //处理url
        $url_temp = explode_url($url_array);

        $func_name = trim($inputs['func_name']);
        $func_comment = trim($inputs['func_comment']);
        $attr = array(
            'level' => $level+1,
            'pid' => $pid,
            'func_name' => $func_name,
            'controller' => $url_temp[0],
            'action' => $url_temp[1],
            'style' =>$inputs['style'],
            'enable' => intval($inputs['enable']),
            'func_comment' => $func_comment,
            'is_show' =>$inputs['is_show'],
        );

        $result = $this->func->add($attr);
        //添加操作日志
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "添加了内容");
        return $result ? success("添加成功！") : failure("添加失败！");
    }

    /**
     * 编辑链接权限
     * method post
     */
    public function post_EditLink()
    {
        $inputs = $this->input->post();
        $id = intval($inputs['id']);

        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }

        $url_array = explode('/', $inputs['url']);
        //处理url
        $url_temp = explode_url($url_array);

        $func_name = trim($inputs['func_name']);
        $func_comment = trim($inputs['func_comment']);
        $attr = array(
            'func_name' => $func_name,
            'controller' => $url_temp[0],
            'action' => $url_temp[1],
            'style' =>$inputs['style'],
            'enable' => intval($inputs['enable']),
            'func_comment' => $func_comment,
            'is_show' =>$inputs['is_show'],
        );

        //更新前
        $info_old = $this->func->getInfoById($id);
        //执行编辑
        $result = $this->func->edit($id, $attr);
        //更新后
        $info_new = $this->func->getInfoById($id);

        //变更对比
        $str = array();
        foreach($info_old as $key => $v){
            if($info_new[$key] != $v)
            {
                $str[] = $key.":".$v."=>".$info_new[$key];
            }
        }
        if(!empty($str)){
            $log_content_str = implode(',', $str);
            //添加操作日志
            $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);
        }

        return success($result);
    }

    /**
     * 添加页面权限
     * method post
     */
    public function post_AddPage()
    {
        $inputs = $this->input->post();
        $pid = intval($inputs['pid']);

        if(! is_numeric($pid) || $pid <= 0)
        {
            return failure('参数错误');
        }

        $module_info = $this->func->getInfoById($pid);

        if(! empty($module_info))
        {
            $level = $module_info['level'];
            $pid = $module_info['id'];
        }

        $func_name = trim($inputs['func_name']);
        $func_comment = trim($inputs['func_comment']);
        $is_button = intval($inputs['is_button']);
        if($is_button)
        {
            $url_array = explode('/', $inputs['url']);
            //处理url
            $url_temp = explode_url($url_array);

            $attr = array(
                'level' => $level+1,
                'pid' => $pid,
                'func_name' => $func_name,
                'controller' => $url_temp[0],
                'action' => $url_temp[1],
                'style' => '',
                'is_button' => $is_button,
                'enable' => intval($inputs['enable']),
                'func_comment' => $func_comment,
            );
        }else{
            $attr = array(
                'level' => $level+1,
                'pid' => $pid,
                'func_name' => $func_name,
                'controller' => '',
                'action' => '',
                'style' => '',
                'is_button' => $is_button,
                'enable' => intval($inputs['enable']),
                'func_comment' => $func_comment,
            );
        }

        $result = $this->func->add($attr);
        //添加操作日志
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "添加了内容");
        return $result ? success("添加成功！") : failure("添加失败！");
    }
    /**
     * 编辑链接权限
     * method post
     */
    public function post_EditPage()
    {
        $inputs = $this->input->post();
        $id = intval($inputs['id']);

        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }

        $func_name = trim($inputs['func_name']);
        $func_comment = trim($inputs['func_comment']);

        $is_button = intval($inputs['is_button']);

        if($is_button){

            $url_array = explode('/', $inputs['url']);
            //处理url
            $url_temp = explode_url($url_array);

            $attr = array(
                'func_name' => $func_name,
                'controller' => $url_temp[0],
                'action' => $url_temp[1],
                'is_button' => $is_button,
                'enable' => intval($inputs['enable']),
                'func_comment' => $func_comment,
            );
        }else{
            $attr = array(
                'func_name' => $func_name,
                'is_button' => $is_button,
                'enable' => intval($inputs['enable']),
                'func_comment' => $func_comment,
            );
        }
        //更新前
        $info_old = $this->func->getInfoById($id);
        //执行编辑
        $result = $this->func->edit($id, $attr);
        //更新后
        $info_new = $this->func->getInfoById($id);

        //变更对比
        $str = array();
        foreach($info_old as $key => $v){
            if($info_new[$key] != $v)
            {
                $str[] = $key.":".$info_new[$key]."=>".$v;
            }
        }
        if(!empty($str)){
            $log_content_str = implode(',', $str);
            //添加操作日志
            $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);
        }
        return success($result);
    }


//用于三级联动
    /**
     * 二级数据
     * method get
     */
    public function get_AllModules()
    {
        $infos = $this->func->getInfosByPid(0);
        return success($infos);
    }
    /**
     * 二级数据
     * method get
     */
    public function get_AllLinks()
    {
        $id = $this->input->get('id');
        $id = intval($id);
        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }
        $infos = $this->func->getInfosByPid($id);
        foreach($infos as &$v)
        {
            $v['url'] = $v['controller']."/".$v['action'];
        }
        return success($infos);
    }

    /**
     * 三级数据
     * method get
     */
    public function get_AllBasesettings()
    {
        $id = $this->input->get('id');
        $id = intval($id);
        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }
        $infos = $this->func->getAllInfosByPid($id);
        foreach($infos as &$v)
        {
            $v['url'] = $v['controller']."/".$v['action'];
        }
        return success($infos);
    }
}