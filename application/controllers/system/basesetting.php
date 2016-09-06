<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Basesetting extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('sys_basesetting_model','basesetting');
    }

    public function index()
    {
        $id = $this->_data['cur_info']['id'];
        $infos = $this->func->getInfosByPid2($id);
        $data['infos'] = $infos;
        $this->load->view('system/basesetting/index', $data);
    }
}
