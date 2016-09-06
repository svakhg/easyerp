<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Baseset extends App_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('form_validation', 'chinese');
        $this->load->library('form_validation');
        $this->load->helper('array');
        $this->load->model('sys_basesetting_model', 'baseset');
    }

    /**
     * 获取内容列表,分页
     */
    public function get_AllBaseset()
    {
        $inputs = $this->input->get();

        $id = intval($inputs['id']);
        $pagesize = intval($inputs['pagesize']);
        $page = (! empty($inputs['page'])) ? intval($inputs['page']) : 1;
        $keywords = $inputs['find'];

        $result = $this->baseset->getPageList($id, $pagesize, $page, $keywords);

        return (! empty($result['rows'])) ? success($result) : success(array());
    }

    /**
     * 新建添加基础内容
     * method post
     */
    public function post_AddBaseset()
    {
        $inputs = $this->input->post();

        $name = trim($inputs['name']);
        $comment = trim($inputs['comment']);
        $attr = array(
            'setting_id' => $inputs['setting_id'],
            'name' => $name,
            'order' => $inputs['order'],
            'enable' => intval($inputs['enable']),
            'comment' => $comment,
        );

        $query = $this->erp_conn->limit(1)->get_where('erp_sys_basesetting', array('name' => $name));
        if($query->num_rows() != 0){
            return failure('名称已占用');
        }

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('name', '名称', 'regex_match[/[\x{4e00}-\x{9fa5}\w]+$/u]|max_length[5]');
        if ($this->form_validation->run() == FALSE){
            return failure(form_error('name'));
        }
        //执行添加
        $result = $this->baseset->add($attr);

        //添加操作日志
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "添加了内容");

        return success($result);
    }

    /**
     * 编辑基础内容
     * method post
     */
    public function post_EditBaseset()
    {
        $inputs = $this->input->post();
        $id = intval($inputs['id']);

        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }

        $name = trim($inputs['name']);
        $comment = trim($inputs['comment']);
        $attr = array(
            'name' => $name,
            'order' => $inputs['order'],
            'enable' => intval($inputs['enable']),
            'comment' => $comment,
        );

        //更新前
        $info_old = $this->baseset->getInfoById($id);

        //执行编辑
        $result = $this->baseset->edit($id, $attr);

        //更新后
        $info_new = $this->baseset->getInfoById($id);

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
     * 删除基础内容
     * method get
     */
    public function get_DelBaseset()
    {
        $id = $this->input->get('id');
        $id = intval($id);
        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }

        $infos = $this->baseset->getInfoById($id);
        if(empty($infos))
        {
            return failure('未找到要删除的数据');
        }
        $result = $this->baseset->delById($id);
        //添加操作日志
        $log_content_str = "删除了编号为".$id."，名称为'".$infos['name']."'的记录";
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);
        return success($result);
    }

    /**
     * 启用操作
     * method get
     * param ids (str)
     */
    public function get_SwitchOn()
    {
        $ids = $this->input->post('ids');
        if($ids == '')
        {
            return failure('未找到要修改的数据');
        }
        $ids_array = explode(',', $ids);

        foreach($ids_array as $v)
        {
            //检查记录为id的启用状态
            $info = $this->baseset->getInfoById($v);
            if($info['enable'] == Sys_basesetting_model::ENABLE_OFF);
            {
                $result = $this->baseset->OpenButton($v);
                //添加操作日志
                $log_content_str = "启用了基础设置编号为".$v."，名称为'".$info['name']."'的记录";
                $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);
            }
        }
        return success("启用成功");
    }

    /**
     * 禁用用操作
     * method get
     * param ids (str)
     */
    public function get_SwitchOff()
    {
        $ids = $this->input->post('ids');
        if($ids == '')
        {
            return failure('未找到要修改的数据');
        }
        $ids_array = explode(',', $ids);

        foreach($ids_array as $v)
        {
            //检查记录为id的启用状态
            $info = $this->baseset->getInfoById($v);
            if($info['enable'] == Sys_basesetting_model::ENABLE_ON);
            {
                $result = $this->baseset->OffButton($v);
                //添加操作日志
                $log_content_str = "禁用了基础设置编号为".$v."，名称为'".$info['name']."'的记录";
                $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);
            }
        }
        return success("禁用成功");
    }


}