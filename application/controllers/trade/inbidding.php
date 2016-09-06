<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inbidding extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper('array');
        $this->load->model('sys_trademarksetting_model', 'trademark');
        $this->load->model('sys_user_model', 'user');
        $this->load->model('ew/demand_order_model','order');
        $this->load->model('ew/demand_content_model','content');
        $this->load->model('baidupush/baidupush');
        $this->load->model('demand/demands','demands');
    }

	//商家投标中
    public function index()
    {
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
        $this->load->view('trade/inbidding_view',$infos);
    }

    /**
     * 获取商家投标中列表
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
            'bidding' => 'bidding'
        );

        $keys_final = array();
        foreach($keys as $key => $v)
        {
            if($v != ''){
                $keys_final[$key] = $v;
            }
        }
        $result = $this->demands->DemandList($keys_final);

//        print_r($result);die();
        if(empty($result['rows']))
        {
            return success(array('total'=>0,'rows'=>array()));
        }
        foreach($result['rows'] as &$v)
        {
            $this->lang->load('date','chinese');
            $this->load->helper('date');
            $v['compare_time'] = compare_to_now($v['time_11']);

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

    /*
     * 单个审核自荐信功能
     * 
     */
    public function exam_recommend(){
        $data['order_id'] = $this->input->post('id');
        //审核通过状态置为31，不通过置为99
        if($this->input->post('examine') == 1){
            $data['status'] = 31;

             //添加需求日志
            $did = $data['order_id'];
            $brr = $this->ew_conn->where('ew_demand_order.id',$did)->select('ew_demand_order.content_id,ew_demand_content.demand_id,ew_demand_order.order_id')->from('ew_demand_content')->join('ew_demand_order','ew_demand_content.id=ew_demand_order.content_id')->get();
            $data_log = $brr->row_array();
            $this->load->model('demand_order_log_model'); 
            $this->demand_order_log_model->demandlog($data_log['content_id'],$did,$data_log['order_id'],$data_log['demand_id'],'单个审核自荐信','商家投标中-自荐信审核通过');


        }elseif($this->input->post('examine') == 0){
            $data['status'] = 99;
            $data['order_step_end'] = 21;

             //添加需求日志
            $did = $data['order_id'];
            $brr = $this->ew_conn->where('ew_demand_order.id',$did)->select('ew_demand_order.content_id,ew_demand_content.demand_id,ew_demand_order.order_id')->from('ew_demand_content')->join('ew_demand_order','ew_demand_content.id=ew_demand_order.content_id')->get();
            $data_log = $brr->row_array();
            $this->load->model('demand_order_log_model'); 
            $this->demand_order_log_model->demandlog($data_log['content_id'],$did,$data_log['order_id'],$data_log['demand_id'],'单个审核自荐信','商家投标中-自荐信审核不通过');
        }
        if(empty($data['order_id']) || !isset($data['status'])){
            echo json_encode(array('result' => 'fail','info' => '缺少参数'));exit;
        }
        $ew_api = $this->config->item('ew_domain').'erp/demand/change-order-status';
        $result = $this->curl->post($ew_api,$data);

        // 百度推送 begin ----------------------------------------------
        $order_info = $this->order->getOrderById($data['order_id']);
        $content_info = $this->content->getContentById($order_info['content_id'], 'demand_id');

        $data['demand_id'] =  $content_info['demand_id'];
        $data['id'] =  $order_info['id'];
        $data['status'] =  $order_info['status'];
        $data['shopper_user_id'] =  $order_info['shopper_user_id'];
        $data['shopper_alias'] =  $order_info['shopper_alias'];

        $this->baidupush->BaiduPushForErp($data);

        // 百度推送 end -----------------------------------------------

        echo json_encode(array('result' => 'succ','info' => '审核完成'));exit;
    }

    //erp初选商家获取该条需求对应的的所有商家信息列表
    public function getShopersByDemandId()
    {
        //拼接参数字符串
        $url_str = "?";
        //需求id
        $id = $this->input->get("id") ? $this->input->get("id") : 0;
        if(!empty($id))
        {
            $url_str .= "id=$id&";
        }
        //所在地区
        $province = $this->input->get("province") ? $this->input->get("province") : 0;
        $city = $this->input->get("city") ? $this->input->get("city") : 0;
        if(!empty($province) && !empty($city))
        {
            $url_str .= "address=".$province.",".$city."&";
        }
        elseif(!empty($province))
        {
            $url_str .= "address=".$province.","."&";
        }
        elseif(!empty($city))
        {
            $url_str .= "address=,".$city."&";
        }

        //商家类型
        $mode = $this->input->get("shoper_mode") ? $this->input->get("shoper_mode") : 0;
        if(!empty($mode))
        {
            $url_str .= "mode=$mode&";
        }

        //投标状态
        $status = $this->input->get("has_status") ? $this->input->get("has_status") : '';
        if(!empty($status))
        {
            $url_str .= "status=$status&";
        }

        //关键字 商家昵称、手机号码、工作室名称
        $keywords = $this->input->get("keywords") ? $this->input->get("keywords") : '';
        if(!empty($keywords))
        {
            $url_str .= "keyword=$keywords&";
        }

        //page
        $page = $this->input->get("page") ? $this->input->get("page") : 1;
        $pagesize = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
        if($page > 0)
        {
            $url_str .= "page=$page&pagesize=$pagesize";
        }

        $config = $this->_data["config"];
        $ewapi_url = $config["ew_domain"]."erp/demand/demand-order-shoppers".$url_str;
//      var_dump($ewapi_url);die;
        $shoper_list = $this->curl->get($ewapi_url);
        $shoper_list = json_decode($shoper_list,true);

        if(! empty($shoper_list['rows'])){
            foreach($shoper_list['rows'] as &$v){
            //显示订单状态
                switch($v['status']){
                    case 21:
                        $v['status_txt']="已投标，待审核";
                    break;
                    case 31:
                        $v['status_txt']="已投标，待初选";
                        break;
                    case 41:
                        $v['status_txt']="初选中标，待出方案";
                        break;
                    case 46:
                        $v['status_txt']="已出方案，待确认";
                        break;
                    case 51:
                        $v['status_txt']="已中标";
                        break;
                    case 99:
                        $v['status_txt']="未中标";
                        break;
                    default :
                        $v['status_txt']="待投标";
                        break;
                }
            }
        }

        return success($shoper_list);
    }

    /**
     * 初选、确认商家操作
     * ids 是 demand_ids
     * shoper_ids 是 选中的
     */
    public function confirmShopers()
    {
        $inputs= $this->input->post();

        if(empty($inputs['ids'])){
            return failure('参数错误！');
        }
        $demand_id = $inputs['ids'];

        if(empty($inputs['shoper_ids'])){
            return failure('请选择商家！');
        }
        $shoper_ids = $inputs['shoper_ids'];

        //判断需求所处的节点
        //获取指定需求下的正常订单最大状态的订单
        $max_order = $this->ew_conn->select('id, content_id, status')->where('content_id',$demand_id)->where('status <>', 99)->order_by('status','desc')->get('demand_order')->row_array();

        $primary_status_arr = array(11, 21, 31);//初选状态
        $confirm_status_arr = array(41, 46);//初选状态

        $params = array(
            'demand_id'  => $demand_id,
            'shoper_ids' => $shoper_ids
        );

        if(in_array($max_order['status'], $primary_status_arr))
        {
            //进入初选操作
            $ret =  $this->__Primaries($params);
        }
        elseif(in_array($max_order['status'], $confirm_status_arr))
        {
            //进入确认操作
            $ret = $this->__Confirm($params);
        }


        if($ret['result'] == 'succ'){

            return success($ret['info']);

        }elseif($ret['result'] == 'fail'){

            return failure($ret['info']);
        }
    }



    //初选商家操作
    private function __Primaries($arr)
    {
        $cur_time = date('Y-m-d H:i:s');

        //改变用户对应订单确认41状态
        $params = array('status' => 41,'time_41' => $cur_time);
        $this->order->changeOrderStatusInWhere($arr['demand_id'], $params, "id in (".$arr['shoper_ids'].")");

        //要关闭的订单操作99 小于31的为0; 等于31的为 order_step_end = 1
        $this->order->changeOrderStatusInWhere($arr['demand_id'], array('status' => 99,'time_99' => $cur_time), "status < 31");
        $this->order->changeOrderStatusInWhere($arr['demand_id'], array('status' => 99,'order_step_end' => 1,'time_99' => $cur_time), "status = 31");

        //百度推送 begin---------------------------------------------------------------------
        $this->__baiduPushForPrimary_Confirm($arr['demand_id'],41);
        //百度推送 end------------------------------------------------------------------------

        return array('result' => 'succ', 'info' => '初选成功！');
    }

    //确认商家操作
    private function __Confirm($arr)
    {
        $cur_time = date('Y-m-d H:i:s');

        //改变用户对应订单确认51状态
        $shoper_ids = explode(',', $arr['shoper_ids']);

        if(count($shoper_ids) > 1){

            return array('result' => 'fail', 'info' => '确认商家不能选多个！');
        }

        //查询将要被确认的订单time_46的时间是否为空，为空则需要补全为当前确认时间
        $order_time_46 = $this->order->getOrderById($shoper_ids[0], 'time_46');

        if($order_time_46['time_46']=='0000-00-00 00:00:00')
        {
            $params = array('status' => 51,'order_step_end'=> 0,'time_46' =>$cur_time,'time_51' =>$cur_time );
        }
        else
        {
            $params = array('status' => 51,'order_step_end'=> 0,'time_51' => $cur_time);
        }
        //确认订单
        $this->order->changeOrderStatusInWhere($arr['demand_id'], $params, "id = ".$shoper_ids[0]);

        //要关闭的订单操作99  41、46的order_step_end = 2
        $this->order->changeOrderStatusInWhere($arr['demand_id'], array('status' => 99,'order_step_end' => 2,'time_99' => $cur_time), "(status = 41 or status = 46)");

        //更改需求状态为完成状态 80
        $this->content->changeContentStatus($arr['demand_id'], 80);

        //百度推送 begin---------------------------------------------------------------------
        $this->__baiduPushForPrimary_Confirm($arr['demand_id'],51);
        //百度推送 end------------------------------------------------------------------------

        return array('result' => 'succ', 'info' => '确认成功！');

    }

    /**
     * 百度推送
     * @param $demand_id
     * @param $status
     */
    private function __baiduPushForPrimary_Confirm($demand_id, $status)
    {
        $order_list = $this->ew_conn->from('demand_order as orders')
            ->join('demand_content as content','content.id = orders.content_id')
            ->select('content.demand_id,orders.id,orders.status,orders.shopper_user_id,orders.shopper_alias')
            ->where('orders.content_id',$demand_id)->where('orders.status', $status)->get()->result_array();
        foreach($order_list as $val){
            $data['demand_id'] =  $val['demand_id'];
            $data['id'] =  $val['id'];
            $data['status'] =  $val['status'];
            $data['shopper_user_id'] =  $val['shopper_user_id'];
            $data['shopper_alias'] =  $val['shopper_alias'];
            $this->baidupush->BaiduPushForErp($data);
        }
    }

}
