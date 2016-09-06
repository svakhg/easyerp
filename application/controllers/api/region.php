<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Region extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("commons/region_model",'region');
    }

    //地区城市联级
	public function region_lst()
	{
		$parent_id = $this->input->get("parent_id") ? $this->input->get("parent_id") : "";
		
		$region = $this->region->getInfoByPid($parent_id, "id, name as text");
		
		return success($region);
	}
}
