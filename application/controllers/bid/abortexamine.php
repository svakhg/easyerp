<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 *商家招投标
 */
class Abortexamine extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('bid/Shopperdemand','bid');
        $this->load->model('bid/Shopperorder','order');
    }

    public function index(){
        $sql_dai = 'select count(1) from ew_shopper_demand_order where abort_reason!="" and reason_status=1';
        $sql_yes = 'select count(1) from ew_shopper_demand_order where abort_reason!="" and reason_status=2';
        $sql_no = 'select count(1) from ew_shopper_demand_order where abort_reason!="" and reason_status=3';
        $dai = $this->ew_conn->query($sql_dai)->result_array();
        $yes = $this->ew_conn->query($sql_yes)->result_array();
        $no = $this->ew_conn->query($sql_no)->result_array();
        $list['count']['dai'] = $dai[0]['count(1)'];
        $list['count']['yes'] = $yes[0]['count(1)'];
        $list['count']['no'] = $no[0]['count(1)'];
        $this->load->view('bid_view/abortexamine_view',$list);
    }

    public function getAbortList(){
        $params = $this->input->get();
        $list = $this->order->getLetterList($params);
        echo json_encode($list);exit;
    }

    /*
     * 商家弃单原因审核
     * method:post
     */
    public function abortReasonExamine()
    {
        $input = $this->input->post();
        if(!isset($input['order_ids']) || !isset($input['examine']) || !in_array($input['examine'],array(0,1)))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        if($input['examine'] == 1)
        {
            $data['reason_status'] = 2;

           ///添加弃单原因审核通过日志
            $u_ids = $input['order_ids'];
            $this->bid->getShoppersLog($u_ids,0,7);

        }
        elseif($input['examine'] == 0)
        {
            $data['reason_status'] = 3;

            //添加弃单原因审核不通过日志
            $u_ids = $input['order_ids'];
            $this->bid->getShoppersLog($u_ids,0,2);
           
        }
        $data['order_ids'] = $input['order_ids'];
        $res = $this->bid->changeBidReasonStatus($data);
        if($res === true)
        { 
            echo json_encode(array('result' => 'succ','info' => '审核成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '审核失败'));exit;
        }
    }

    /*
     * 保存弃单原因
     */
    public function saveAbortReason(){
        $input = $this->input->post();
        if(!isset($input['id']) || empty($input['id']) || empty($input['abort_reason']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        $data['order_id'] = $input['id'];
        $data['abort_reason'] = $input['abort_reason'];
        $res = $this->order->updateAbortReason($data);
        if($res === true)
        {
            /*
              * 添加修改弃单原因需求日志
            */

             $did = $input['id']; 
             $this->bid->getShoppersLog($did,0,6); 
             

            echo json_encode(array('result' => 'succ','info' => '保存成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '保存失败'));exit;
        }
    }

}
