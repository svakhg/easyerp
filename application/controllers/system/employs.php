<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employs extends App_Controller {

    public function __construct(){
        parent::__construct();
//        $this->output->enable_profiler();
    }

    public function index()
    {
        $this->load->view('system/employs/index');
    }
}
