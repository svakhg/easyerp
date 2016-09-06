<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Message_members extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->lang->load('form_validation', 'chinese');
        $this->load->library('form_validation');
        $this->load->helper('array');
        $this->load->model('sys_user_model', 'user');

        $this->load->model('ew/demand_content_model','content');
        $this->load->model('ew/demand_order_model','order');
        $this->load->model('ew/shopper_demand_content_model','bid_content');
        $this->load->model('ew/shopper_demand_order_model','bid_order');

    }

    public function index()
    {
        $this->load->view('system/message_members/index');
    }


    /*
     * 根据轮询条件查询数据库是否有未处理的需求、订单
     * 返回
     * return bolean
     */
    public function getNewMessage(){
        $user_info = $this->user->getInfoById($this->session->userdata("admin_id"));
        if(! $user_info['accept_message']){
            $result = array('result' => 'succ', 'info' => false);
            $CI = & get_instance();
            return $CI->output->set_content_type('application/json')->set_output(json_encode($result));
        }

        $info = ($this->content->getWait_examined_demand() + $this->order->getWait_examined_letterOrder() + $this->bid_content->getWait_examined_bid_demand() + $this->bid_order->getWait_examined_letterOrder()) ? true : false;
        return success($info);
    }


    /*
    * 查询待处理的需求和订单
    */
    public function getPendingInfos()
    {
        //新人招投标待处理的需求总数
        $count_demand = $this->content->getWait_examined_demand();

        //自荐信待处理的总数
        $count_recomment = $this->order->getWait_examined_letterOrder();

        //商家招投标待处理的需求总数
        $count_shopper_demand = $this->bid_content->getWait_examined_bid_demand();

        //商家招投标待处理的意向书总数
        $count_shopper_letter = $this->bid_order->getWait_examined_letterOrder();

        $data = array(
            'demand' => $count_demand,
            'recomment' => $count_recomment,
            'shopper_demand' => $count_shopper_demand,
            'shopper_letter' => $count_shopper_letter,
        );

        return success($data);
    }


    /**
     * 获取接收消息通知人员带分页列表
     */
    public function get_mes_user_list()
    {
        $inputs = $this->input->get();
        $pagesize = (! empty($inputs['pagesize'])) ? intval($inputs['pagesize']) : 10;
        $page = (! empty($inputs['page'])) ? intval($inputs['page']) : 1;

        //查询条件
        $key = array(
            'name' => '',
            'code' => '',
            'department' => '',
            'accept_message' => 1,
        );
        $result = $this->user->getMessage_membersPageList($pagesize, $page, $key);
        return (! empty($result['rows'])) ? success($result) : success(array());
    }

    /*
     * 获取erp人员的列表 分页
     */
    public function get_user_list()
    {
        $inputs = $this->input->get();
        $pagesize = (! empty($inputs['pagesize'])) ? intval($inputs['pagesize']) : 10;
        $page = (! empty($inputs['page'])) ? intval($inputs['page']) : 1;

        //查询条件
        $key = array(
            'name' => '',
            'code' => '',
            'department' => '',
            'status' => 1,
        );
        $result = $this->user->getPageList($pagesize, $page, $key);
        return (! empty($result['rows'])) ? success($result) : success(array());
    }

    /*
     * 添加消息通知人员
     *
     */
    public function add_message_member()
    {
        $uids = $this->input->post('uids');
        $uid_arr = explode(',', $uids);

        foreach($uid_arr as $_v)
        {
            $user_info = $this->user->getInfoById($_v);
            if(empty($user_info))
            {
                continue;
            }
            if($user_info['accept_message'] == 1)
            {
                continue;
            }
            $this->user->edit($_v, array('accept_message' => 1));
        }

        return success('添加成功');
    }

    /*
     * 移除消息通知人员
     */
    public function remove_message_member()
    {
        $uids = $this->input->post('uids');
        $uid_arr = explode(',', $uids);

        foreach($uid_arr as $_v)
        {
            $user_info = $this->user->getInfoById($_v);
            if(empty($user_info))
            {
                continue;
            }
            if($user_info['accept_message'] == 0)
            {
                continue;
            }
            $this->user->edit($_v, array('accept_message' => 0));
        }

        return success('移除成功');
    }
}
