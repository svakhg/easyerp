<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends App_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function addUserByErp(){
		print_r($_REQUEST);exit;
	}

}
