<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
  # 优惠信息
  # add by zhangmiao
  # 
 */
class Preferential extends App_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('demand/demand_trade_discount','discount');//交易优惠model
    }


    /*
     * 获取当前需求的优惠列表
     */
    public function getCheapList()
    {
        $input = $this->input->get();
        if(!isset($input['sid']) || !isset($input['demand_id'])){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        $result = $this->discount->getDiscountList($input);
        return success($result);
    }

    /*
     * 添加优惠信息
     */
    public function addCheap()
    {
        $input = $this->input->post();
        if(!isset($input['sid'])){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        $data = $input;
        unset($data['sid']);
        $data['contract_id'] = $input['sid'];
        $result = $this->discount->addOne($data);
        if($result == true)
        {
            echo json_encode(array('result' => 'succ','info' => '添加成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '添加失败'));exit;
        }
    }

    /*
     * 修改优惠信息
     */
    public function editCheap()
    {
        $input = $this->input->post();
        if(!isset($input['id'])){
            echo json_encode(array('result' => 'fail','info' => '缺少必要数据'));exit;
        }
        $data = $input;
        unset($data['sid']);
        $data['contract_id'] = $input['sid'];
        $result = $this->discount->editOne($data);
        if($result == true)
        {
            echo json_encode(array('result' => 'succ','info' => '添加成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '添加失败'));exit;
        }
    }

}

?>