<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Updatepwd extends App_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('sys_user_model', 'user');
    }

	public function index()
	{
        $this->load->view('account/editpwd.php');
	}

    //处理修改密码
    public function post_updatepwd()
    {
        $id = $this->session->userdata('admin_id');
        $oldpwd = $this->input->post('oldpwd');
        $newpwd = $this->input->post('newpwd');
        $comparepwd = $this->input->post('comparepwd');
        if(! is_numeric($id) || $id <= 0)
        {
            return failure('参数错误');
        }
        $info = $this->user->getInfoById($id);
        if($info['password'] != md5(trim($oldpwd)))
        {
            return failure('原密码不正确');
        }

        if(trim($newpwd) != trim($comparepwd))
        {
            return failure('两次密码不一致');
        }

        $attr = array(
            'password' => md5($newpwd),
        );
        $result = $this->user->edit($id, $attr);
        return $result ? success("修改成功！") : failure("修改失败");
    }
}
