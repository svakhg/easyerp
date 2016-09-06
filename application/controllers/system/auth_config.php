<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_config extends App_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('system/auth_config/index');
    }

}
