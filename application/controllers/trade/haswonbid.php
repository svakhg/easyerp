<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Haswonbid extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper('array');
        $this->load->model('sys_trademarksetting_model', 'trademark');
        $this->load->model('sys_user_model', 'user');
        $this->load->model('demand/demands','demands');
    }

	//初选中标
    public function index()
    {
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
        $this->load->view('trade/haswonbid_view',$infos);
    }

    /**
     * 获取初选中标列表
     */
    public function getlist()
    {
        $inputs = $this->input->get();
        $pagesize = isset($inputs['pagesize']) ? intval($inputs['pagesize']) : 10;
        $page = isset($inputs['page']) ? intval($inputs['page']) : 1;

        //地点组装
        $country = isset($inputs['country']) ? $inputs['country'] : '';
        $province = isset($inputs['province']) ? $inputs['province'] : '';
        $city = isset($inputs['city']) ? $inputs['city'] : '';
        $wed_location = implode(',',array($country,$province,$city));

        if(isset($inputs['shopper_alias']) && $inputs['shopper_alias'] != '')
        {
            $shopper_alias = mb_substr($inputs['shopper_alias'],0,-2);//一站式的策划师or单项的四大金刚的别名
        }
        else
        {
            if(isset($inputs['alias_code']) && $inputs['alias_code'] != '')
            {
                $shopper_alias = $inputs['alias_code'];//预算类型
            }
            else
            {
                $shopper_alias = '';
            }
        }

        //查询条件
        $keys = array(
            'status' => '1',//完成审核状态
            'pagesize' => $pagesize,
            'page' => $page,
            'counselor_uid' => isset($inputs['counselor_uid']) ? intval($inputs['counselor_uid']) : '',//新人顾问
            'channel' => isset($inputs['channel']) ? intval($inputs['channel']) : '',
            'cli_source' => isset($inputs['cli_source']) ? trim($inputs['cli_source']) : '',
            'mode' => isset($inputs['mode']) ? $inputs['mode'] : '',//找商家方式
            'remander_id' => isset($inputs['remander_id']) ? $inputs['remander_id'] : '',//交易提示id
            'type' => isset($inputs['type']) ? $inputs['type'] : '',//需求类型
            'add_from' => isset($inputs['add_from']) ? $inputs['add_from'] : '',//添加时间 开始
            'add_to' => isset($inputs['add_to']) ? $inputs['add_to'] : '',//添加时间 结束
            'wed_from' => isset($inputs['wed_from']) ? $inputs['wed_from'] : '',//查询婚期时间 开始
            'wed_to' => isset($inputs['wed_to']) ? $inputs['wed_to'] : '',//查询婚期时间 结束
            'condition' => isset($inputs['condition']) ? trim($inputs['condition']) : '',//条件查询
            'condition_text' => isset($inputs['condition_text']) ? trim($inputs['condition_text']) : '',//条件查询域
            'wed_location' => (isset($inputs['country']) && ($inputs['country'] != '')) ? $wed_location : '',//婚礼地点
            'cli_tag' => isset($inputs['cli_tag']) ? $inputs['cli_tag'] : '',//客户标签
            'shopper_alias' => $shopper_alias,
            'budget' =>  isset($inputs['budget']) ? $inputs['budget'] : '',//预算
            'shoper_name' => isset($inputs['shoper_name']) ? $inputs['shoper_name'] : '',//商家名称
            'bidding' => 'primary'
        );

        $keys_final = array();
        foreach($keys as $key => $v)
        {
            if($v != ''){
                $keys_final[$key] = $v;
            }
        }
        $result = $this->demands->DemandList($keys_final);
        if(empty($result['rows']))
        {
            return success(array('total'=>0,'rows'=>array()));
        }
        foreach($result['rows'] as &$v)
        {
            $this->lang->load('date','chinese');
            $this->load->helper('date');
            $v['compare_time'] = compare_to_now($v['time_41']);


            //找商家类型名字
            switch($v['shopper_alias']){
                case 'wedplanners':
                    $v['shopper_alias_name'] = '找策划师';
                    break;
                case 'wedmaster':
                    $v['shopper_alias_name'] = '找主持人';
                    break;
                case 'makeup':
                    $v['shopper_alias_name'] = '找化妆师';
                    break;
                case 'wedvideo':
                    $v['shopper_alias_name'] = '找婚礼摄像';
                    break;
                case 'wedphotoer':
                    $v['shopper_alias_name'] = '找婚礼摄影';
                    break;
                case 'sitelayout':
                    $v['shopper_alias_name'] = '找场地布置';
                    break;
            }
            //获取单项需求的预算金额 查询qa表里的amount 、hspz_amount(摄像：婚纱拍照)、wdy_amount(摄影：婚礼前的爱情微电影); hlgp_amount(婚礼跟拍);
            $qa_amount = $this->ew_conn->where('content_id',$v['id'])->get('demand_qa')->result_array();

            if($v['type'] == 2){//判断是否是单项
                foreach($qa_amount as $val){

                    if($val['alias']=='amount'){

                        $v['budget'] = $val['answer'];

                    }elseif($val['alias']=='hspz_amount'){

                        $v['budget'] = $val['answer'];

                    }elseif($val['alias']=='wdy_amount'){

                        $v['budget'] = $val['answer'];

                    }elseif($val['alias']=='hlgp_amount'){

                        $v['budget'] = $val['answer'];
                    }

                }
            }
        }
        return success($result);
    }

//    //获取应标商家信息
//    public function getHasbidShopers()
//    {
//        //拼接参数字符串
//        $url_str = "?";
//        //需求的id
//        $demand_id = $this->input->get("demand_id") ? $this->input->get("demand_id") : "";
////        $demand_id = 12;
//        if(!empty($demand_id))
//        {
//            $url_str .= "demand_id=$demand_id&";
//        }
//        //page
//        $page = $this->input->get("page") ? $this->input->get("page") : 1;
//        if($page > 0)
//        {
//            $url_str .= "page=$page&pagesize=10";
//        }
//        $config = $this->_data["config"];
//        $ewapi_url = $config["ew_domain"]."/erp/demand/has-bid-shopper-info".$url_str;
//        $shoper_list = $this->curl->get($ewapi_url);
////        print_r($shoper_list);exit;
//        $CI = & get_instance();
//        return $CI->output->set_content_type('application/json')->set_output($shoper_list);
//    }


}