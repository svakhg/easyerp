<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends Base_Controller {


	public function __construct(){
        parent::__construct();
        $this->load->model('sys_log_model','log');
        $this->load->model('sys_func_model', 'func');
        $this->load->model("account/login_model",'log_in');
    }

	public function index()
	{
		//判断如果已经登录，跳回根目录
		if($this->session->userdata('admin'))
		{
            redirect('home/index');
			exit();
		}
		$this->_data['config'] = $this->config->config;
        $this->load->view('account/login_view.php',$this->_data);
	}
	
	public function sign_in()
	{
		$username = $this->input->post("username");
		$password = $this->input->post("password");
		$code = $this->input->post("code");
		
		if(empty($username) || empty($password))
		{
            redirect('account/login');
		}
		
		//验证验证码
		if(ENVIRONMENT == 'production')
		{
			if(!$this->sms->validate($username, $code))
			{
				$this->session->set_userdata('log_status', 'validate');
				redirect('account/login');
			}
		}
		

		$row = $this->log_in->signin($username, $password);
		if(!empty($row))
		{
            $this->session->set_userdata('admin', $row["username"]);
			$this->session->set_userdata('admin_id', $row["id"]);
            $this->session->set_userdata('admin_mobile', $row['mobile']);
            $this->session->set_userdata('log_status', 'login');
            $this->session->set_userdata('satrap', $row['satrap']);
			$this->session->set_userdata('is_test', $row['is_test']);

            //添加操作日志
            $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::LOGIN_TYPE, "登录");

            header('Location: /home/index');
			exit();
		}else
		{
            $this->session->set_userdata('log_status', 'logout');
            redirect('account/login');
		}
	}

    //退出登录
    public function logout(){
        //添加操作日志
        $this->log->addlog($this->session->userdata('admin_id'), Sys_log_model::LOGIN_TYPE, "退出");

        //删除session
        $this->session->unset_userdata('admin');
        $this->session->unset_userdata('admin_id');
        $this->session->unset_userdata('log_status');
        $this->session->unset_userdata('satrap');
        //清除session
        $this->session->sess_destroy();
        redirect('account/login');
    }
	
	//发送验证码
	public function send()
	{
		$username = $this->input->get("username");
		if(!$username)
		{
			return failure("请填写手机号");
		}
		$send = $this->sms->sendCode($username);
		if($send["code"] == 1)
		{
			return success("发送成功");
		}else
		{
			return failure("发送失败");
		}
	}
}
