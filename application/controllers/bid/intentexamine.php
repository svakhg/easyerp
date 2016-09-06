<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 *商家招投标
 */
class Intentexamine extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('bid/Shopperdemand','bid');
        $this->load->model('bid/Shopperorder','order');
        $this->load->model('baidupush/baidupush');
    }

    public function index(){
        $sql_dai = 'select count(1) from ew_shopper_demand_order where wish != "" and status=21';
        $sql_yes = 'select count(1) from ew_shopper_demand_order where wish != "" and status>=31 and status<=61';
        $sql_no = 'select count(1) from ew_shopper_demand_order where wish != "" and status=98';
        $dai = $this->ew_conn->query($sql_dai)->result_array();
        $yes = $this->ew_conn->query($sql_yes)->result_array();
        $no = $this->ew_conn->query($sql_no)->result_array();
        $list['count']['dai'] = $dai[0]['count(1)'];
        $list['count']['yes'] = $yes[0]['count(1)'];
        $list['count']['no'] = $no[0]['count(1)'];
        $this->load->view('bid_view/intentexamine_view',$list);
    }

    public function getLetterList(){
        $params = $this->input->get();
        $list = $this->order->getLetterList($params);
        // print_R($list);exit;
        echo json_encode($list);exit;
    }

    /*
     * 商家意向书审核
     * method:post
     */
    public function letterExamine()
    {
        $input = $this->input->post();
        if(!isset($input['order_ids']) || !isset($input['examine']) || !in_array($input['examine'],array(0,1)))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        $data['order_ids'] = $input['order_ids'];
        if($input['examine'] == 1)
        {
            $data['order_status'] = 31;//意向书审核通过
            
             $u_ids = $input['order_ids'];
             $this->bid->getShoppersLog($u_ids,0,9);

        }
        elseif($input['examine'] == 0)
        {
            $data['order_status'] = 98;//意向书审核不通过，订单失败

             $u_ids = $input['order_ids'];
             $this->bid->getShoppersLog($u_ids,0,10);
           
           

        }
        $data['order_ids'] = $input['order_ids'];
        $ids_arr = explode(',',$data['order_ids']);
        foreach($ids_arr as $k => $v)
        {
            $params['order_status'] = $data['order_status'];
            $params['order_id'] = (int)$v;
            $res[] = $res_curr = $this->bid->changeBidOrderStatus($params);

            //baiduPush begin

            if($input['examine'] == 1){

                $order = $this->bid->getOneOrder(array('order_id' => $v));
                $demand = $this->bid->getOneDemand(array('bid_id' => $order[0]['content_id']));

                $data['demand_id'] =  $demand[0]['demand_id'];
                $data['id'] =  $order[0]['id'];
                $data['status'] =  $order[0]['status'];
                $data['shopper_user_id'] =  $order[0]['shopper_user_id'];
                $this->baidupush->BaiduBusinessPushForErp($data);

            }

            //baiduPush end

            if($res_curr === true)
            {//判断如果所有的订单都是意向书审核不通过，则将该需求改为96（自荐信审核全部不通过）
                $res_content[] = $this->bid->changeContentIfAll($params);
            }
        


        }
        if($res[0] === true)
        {
            echo json_encode(array('result' => 'succ','info' => '审核成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '审核失败'));exit;
        }
    }

    /*
     * 保存意向书
     */
    public function saveRecommendLetter(){
        $input = $this->input->post();
        if(!isset($input['id']) || empty($input['id']) || empty($input['wish']) || empty($input['recommend_letter_json']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        if(!is_array(json_decode(urldecode($input['recommend_letter_json']),true)))//确认编码能否成功
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        $data['order_id'] = $input['id'];
        $data['wish'] = $input['wish'];
        $data['recommend_letter'] = addslashes(json_encode(json_decode(urldecode($input['recommend_letter_json']))));
        $res = $this->order->updateRecommendLetter($data);
        if($res === true)
        {
          /*
           *添加商家招投标修改意向书需求日志
           */
            $o_id = $input["id"];   
            $data = $this->bid->getShoppersLog($o_id,0,5);
            

            echo json_encode(array('result' => 'succ','info' => '保存成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '保存失败'));exit;
        }
    }

}