<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employ extends App_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('form_validation', 'chinese');
        $this->load->library('form_validation');
        $this->load->helper('array');
        $this->load->model('sys_user_model', 'user');
        $this->load->helper('functions');// 载入公共函数

    }

    /**
     * 添加员工
     * method post
     */
    public function post_AddUser()
    {
        $inputs = $this->input->post();
        $username = trim($inputs['username']);
        $num_code = $this->input->post('num_code');
        $mobile = trim($inputs['mobile']);

        //检查名字
        $user_info = $this->user->getInfoByUserName($username);
        if(! empty($user_info))
        {
            return failure("用户名已经被占用");
        }

        //检查编号
        $code_info = $this->user->getInfoByNumCode($num_code);
        if(! empty($code_info))
        {
            return failure("编号已经被占用");
        }

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('username', '员工姓名', 'required|regex_match[/[\x{4e00}-\x{9fa5}\w]+$/u]|min_length[1]|max_length[30]');
        $this->form_validation->set_rules('num_code', '员工编号', 'required|regex_match[/[a-zA-Z0-9]+/]|min_length[1]|max_length[8]');
        $this->form_validation->set_rules('newpwd', '密码', 'required|min_length[6]|max_length[16]');
        if ($this->form_validation->run() == FALSE){
            return failure(validation_errors());
        }

//        if ($this->form_validation->run() == FALSE){
//            return failure(form_error('num_code'));
//        }

        $password = md5(trim($inputs['newpwd']));
		//员工头像
        if($inputs['img_value'] != ""){
    		$image = $this->upload_image($inputs['img_value']);
        }else{
            $image = "";
        }
        $attr = array(
            'ew_uid' => 0,
            'username' => $username,
            'password' => $password,
            'num_code' => $inputs['num_code'],
            'department' => $inputs['department'],
            'mobile' => $mobile,
            'role_id' => $inputs['role_id'],
            'satrap' => $inputs['satrap'],
			'is_test' => $inputs['is_test'],
			'avator' => $image, //员工头像
            'create_time' => date('Y-m-d H:i:s')
        );

        $result = $this->user->add($attr);
        //添加操作日志
        $this->log->addlog($this->session->userdata('admin_id'), 1, "添加了内容");

        return $result ? success("成功") : failure("失败");
    }

    /*
     * 上传图片
     *
     */
	public function upload_image($base64){
        $bucket = "easywed-image";
        $oss_dir = "erp/head/".date("Ymd")."/";
        $filename = md5(date('YmdHis').rand(1000,9999)).'.png';
		$aliyun_oss_config = $this->config->config['aliyun_oss'];
		
        // if (! file_exists ( $_SERVER['DOCUMENT_ROOT'].'/uploads')) {
        //     mkdir ( $_SERVER['DOCUMENT_ROOT'].'/uploads' );
        //     @chmod ( $_SERVER['DOCUMENT_ROOT'].'/uploads', 0777 );
        // }
        // if (! file_exists ( $_SERVER['DOCUMENT_ROOT'].'/uploads/employs' )) {
        //     mkdir ( $_SERVER['DOCUMENT_ROOT'].'/uploads/employs' );
        //     @chmod ( $_SERVER['DOCUMENT_ROOT'].'/uploads/employs', 0777 );
        // }
        // if (! file_exists ( $_SERVER['DOCUMENT_ROOT'].'/uploads/employs/'.date("Ymd") )) {
        //     mkdir ( $_SERVER['DOCUMENT_ROOT'].'/uploads/employs/'.date("Ymd") );
        //     @chmod ( $_SERVER['DOCUMENT_ROOT'].'/uploads/employs/'.date("Ymd"), 0777 );
        // }
        // $source_dir = 'uploads/employs/'.date("Ymd").'/';
		$find = array("data:base64,","data:image/jpeg;base64,","data:image/png;base64,","data:image/gif;base64,","data:image/jpg;base64,");
        $stream = base64_decode(str_replace($find, '', $base64));
        // $image = file_put_contents($source_dir.$filename, $stream);
        try{
            $ossClient = new OSS\OssClient($aliyun_oss_config['AccessKeyId'], $aliyun_oss_config['AccessKeySecret'], $aliyun_oss_config['ossServer']);
            $ossClient->putObject($bucket, $oss_dir.$filename, $stream);
        } catch (OssException $e) {
            print $e->getMessage();exit;
        }
        return $oss_dir.$filename;
	}

    /**
     * 编辑员工
     * method post
     */
    public function post_EditUser()
    {
        $inputs = $this->input->post();
        $id = intval($inputs['id']);
        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }

        $username = trim($inputs['username']);
        //检查名字
        $user_info = $this->user->getInfoByUserName($username);
        if(! empty($user_info) && $user_info['username'] != $username)
        {
            return failure("用户名已经被占用");
        }
        //手机号码
        $user_mobile = trim($inputs['mobile']);

        $num_code = $this->input->post('num_code');
        //检查编号
        $code_info = $this->user->getInfoByNumCode($num_code);
        if(! empty($code_info) && $code_info['num_code'] != $num_code)
        {
            return failure("编号已经被占用");
        }
		//员工头像
		if($inputs['img_value'] != ""){
            $image = $this->upload_image($inputs['img_value']);
        }else{
			 $avator = $this->erp_conn->select("avator")->where('id', $id)->get('erp_sys_user')->row_array();
			 $image = $avator['avator'];
        }
        if(! empty($inputs['newpwd'])){
            $attr = array(
                'username' => $username,
                'password' => md5(trim($inputs['newpwd'])),
                'num_code' => $inputs['num_code'],
                'department' => $inputs['department'],
                'mobile' => $user_mobile,
				'avator' => $image,
                'role_id' => $inputs['role_id'],
            );
        }else{
            $attr = array(
                'username' => $username,
                'num_code' => $inputs['num_code'],
                'department' => $inputs['department'],
                'mobile' => $user_mobile,
				'avator' => $image,
                'role_id' => $inputs['role_id'],
            );
        }
        $attr['satrap'] = $inputs['satrap'];
		$attr['is_test'] = $inputs['is_test'];
        //更新前
        $info_old = $this->user->getInfoById($id);
        //执行编辑
        $result = $this->user->edit($id, $attr);
        //更新后
        $info_new = $this->user->getInfoById($id);

        //变更内容
        $str = array();
        foreach($info_old as $key => $v){
            if($info_new[$key] != $v)
            {
                $str[] = $key.":".$info_new[$key]."=>".$v;
            }
        }
        $log_content_str = implode(',', $str);

        //添加操作日志
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::OPER_TYPE, $log_content_str);

        return $result ? success("成功") : failure("失败");
    }

    /**
     * 删除内容
     * method get
     */
    public function get_DelUser()
    {
        $id = $this->input->get('id');
        $id = intval($id);
        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }

        $infos = $this->user->getInfoById($id);
        if(empty($infos))
        {
            return failure('未找到要删除的数据');
        }
        $result = $this->user->delById($id);
        return success($result);
    }

    /**
     * 获取字典表部门和角色
     */
    public function get_Dic()
    {
        //所有角色
        $roles = $this->erp_conn->select('id, role_name as text')->get('erp_sys_role')->result_array();
        $role = (! empty($roles)) ? $roles : array();

        //所有的部门
        $depart = $this->erp_conn->select('id, name as text')->get('erp_sys_department')->result_array();
        $department = (! empty($depart)) ? $depart : array();

        $infos = array(
            'department' =>$department,
            'role' => $role
        );
        return success($infos);
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
            $info = $this->user->getInfoById($v);
            if($info['status'] == Sys_user_model::STATUS_STOP);
            {
                $result = $this->user->OpenButton($v);
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
            $info = $this->user->getInfoById($v);
            if($info['status'] == Sys_user_model::STATUS_NORMAL);
                {
                $result = $this->user->OffButton($v);
            }
        }
        return success("禁用成功");
    }

    /**
     * 获取内容列表,分页
     */
    public function get_AllEmploys()
    {
        $inputs = $this->input->get();
        $pagesize = intval($inputs['pagesize']);
        $page = (! empty($inputs['page'])) ? intval($inputs['page']) : 1;

        //查询条件
        $key = array(
            'name' => trim($inputs['name']),
            'code' => trim($inputs['code']),
            'department' => intval($inputs['department']),
            'status' => $inputs['status'],
        );

        $result = $this->user->getPageList($pagesize, $page, $key);
        foreach($result['rows'] as $k => &$v){
           $v['avator'] = $v["avator"] ? get_oss_image($v["avator"]) : '';;
        }
        return (! empty($result['rows'])) ? success($result) : success(array());
    }
}