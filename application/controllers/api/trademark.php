<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trademark extends App_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('array');
        $this->load->model('sys_trademarksetting_model', 'trademark');
    }

    /**
     * 获取内容列表,分页
     */
    public function get_AllTrademark()
    {
        $inputs = $this->input->get();

        $id = intval($inputs['id']);
        $pagesize = intval($inputs['pagesize']);
        $page = (! empty($inputs['page'])) ? intval($inputs['page']) : 1;
        $keywords = $inputs['find'];


        $result = $this->trademark->getPageList($id, $pagesize, $page, $keywords);

        return (! empty($result['rows'])) ? success($result) : success(array());
    }

    /**
     * 新建添加基础内容
     * method post
     */
    public function post_AddTrademark()
    {
        $inputs = $this->input->post();

        $name = trim($inputs['name']);
        $comment = trim($inputs['comment']);
        $attr = array(
            'mark_id' => $inputs['mark_id'],
            'name' => $name,
            'color' => $inputs['color'],
            'enable' => intval($inputs['enable']),
            'comment' => $comment,
        );
        //执行
        $result = $this->trademark->add($attr);
        //添加操作日志
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, "添加了内容");
        return success($result);
    }

    /**
     * 编辑基础内容
     * method post
     */
    public function post_EditTrademark()
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
            'color' => $inputs['color'],
            'enable' => intval($inputs['enable']),
            'comment' => $comment,
        );
        //更新前
        $info_old = $this->trademark->getInfoById($id);

        //执行编辑
        $result = $this->trademark->edit($id, $attr);

        //更新后
        $info_new = $this->trademark->getInfoById($id);

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

    /**
     * 删除基础内容
     * method get
     */
    public function get_DelTrademark()
    {
        $id = $this->input->get('id');
        $id = intval($id);
        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }

        $infos = $this->trademark->getInfoById($id);
        if(empty($infos))
        {
            return failure('未找到要删除的数据');
        }
        $result = $this->trademark->delById($id);
        //添加操作日志
        $log_content_str = "删除了编号为".$id."的记录";
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
            $info = $this->trademark->getInfoById($v);
            if($info['enable'] == Sys_trademarksetting_model::ENABLE_OFF);
            {
                $result = $this->trademark->OpenButton($v);
                //添加操作日志
                $log_content_str = "启用了交易标记编号为".$v."，名称为'".$info['name']."'的记录";
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
            $info = $this->trademark->getInfoById($v);
            if($info['enable'] == Sys_trademarksetting_model::ENABLE_ON);
            {
                $result = $this->trademark->OffButton($v);
                //添加操作日志
                $log_content_str = "禁用了基础设置编号为".$v."，名称为'".$info['name']."'的记录";
                $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);
            }
        }
        return success("禁用成功");
    }

    /**
     * 获取交易标记颜色字典
     */
    public function getAllColors(){
        $result = $this->erp_conn->select('id,color_name as text, HEX_value')->get('ew_erp_colors')->result_array();
        if(empty($result))
        {
            return success("没有数据");
        }

        $colors = (! empty($result)) ? success($result) : array();
    }
}