<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sys_user extends Base_Controller {

    public function __construct(){
        parent::__construct();
		$this->load->model('sys_user_model', 'users');
		
    }

    //ew主站查询运营信息
	public function getOperate()
	{
		$data = $this->input->post();
		if(empty($data))
		{
			return failure("参数错误");
		}
		$result = $this->users->getInfoById($data['uid']);
		return (! empty($result)) ? success($result) : success(array());
	}
	
}
