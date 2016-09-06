<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends App_Controller {

	public function __construct(){
		parent::__construct();

	}

	public function index()
	{
//------------调用开始--------------------------------------------------
        //调用方法 domain?param=begin&t=51
        //51的位置是当前系统的时间
        $data = $this->input->get();
        $param = isset($data['param']) ? $data['param'] : '';
        $time = isset($data['t']) ? $data['t'] : '';//当前时间的分钟

        if($param == 'begin' && $time == date('i')){
            echo $this->__runData();
        }
//------------调用结束--------------------------------------------------
        $this->load->view('home_view/home_index_view');
	}

    /*
     * 跑local主站数据库 从demand_qa表里的shopper_alias,service_type 更新到demand_content表的shopper_alias,service_type字段中
     */
    private function __runData()
    {
        $qa_ids = $demands = array();

        $data = $this->getData();

        foreach($data['single_demand_qa'] as $_v)
        {
            $qa_ids[] = $_v['content_id'];
            $demands[$_v['content_id']] = $_v;
        }

        $ids_arr = array();
        foreach($data['demands_ids'] as $v)
        {
            if(in_array($v['id'], $qa_ids))
            {
                $this->__update_content($demands[$v['id']]);
                $ids_arr[] = $v['id'];
            }
        }

        $id_count = count($ids_arr);
        $effected_str = implode(',', $ids_arr);

        if($id_count)
        {
            return "Done,".$id_count." effected where id in : $effected_str !";
        }
        else
        {
            return "None needed update !";
        }

    }

    private function __update_content($data)
    {
        $params = array(
            'shopper_alias' => $data['shopper_alias'],
            'service_type' => $data['service_type'],
        );
        $this->ew_conn->update('demand_content',$params,array('id' => $data['content_id']));
    }

    public function getData()
    {
        $data = array();

        $data['demands_ids'] = $this->ew_conn->select('id')->where("shopper_alias",'')->get('demand_content')->result_array();
        $data['single_demand_qa'] = $this->ew_conn->select('content_id, shopper_alias, service_type')->distinct('content_id')->get('demand_qa')->result_array();

        return $data;
    }

}
