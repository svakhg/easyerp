<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 *商家招投标 二期
 */
class Shopperbid2016 extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('commons/region_model' , 'region');
    }

    public function index()
    {
        list($status,$status_explan) = self::_getStatus();
        $status_flip = array_flip($status);
        $data['status'] = $status;
        $data['status_flip'] = $status_flip;
        $data['status_explan'] = $status_explan;
        $data['shopper_alias'] = $this->_getShopperAlias();

        $this->load->view("bid_view/shopperbid2016_view",$data);
    }

    /**
     * 获取商家招投标列表
     */
    public function getShopperBid()
    {
        $input = $this->input->get();
        if($this->session->userdata('is_test') == 0)
        {
            $input['is_test'] = 0;
        }
        $res = $this->curl->post($this->config->item('ew_domain').'/erp/bid/shopper-bid', $input);
        $res_arr = json_decode($res , true);
        if($res_arr['result'] == 'succ'){
            return success($res_arr['info']);
        }else{
            return failure("ew error: ".$res_arr['msg']);
        }
    }

    /**
     * 获取商家招投标详情
     */
    public function getShopperBidDetail()
    {
        $input = $this->input->get();
        $res = $this->curl->post($this->config->item('ew_domain').'/erp/bid/shopper-bid-detail', $input);
        $res_arr = json_decode($res , true);
        if($res_arr['result'] == 'succ'){
            $data = $res_arr['info'];
        }else{
            $data  = array();
        }

        list($order_status,$order_status_explan) = self::_getOrderStatus();
        $order_status_flip = array_flip($order_status);

        //婚礼地点
        $region_list = $this->region->getAll();
        $tp_location_text = '';
        $tp_location = explode(',' , $data['wed_place']);
        $tp_location_text .= isset($tp_location[0]) ? $region_list[$tp_location[0]].'-' : '';
        $tp_location_text .= isset($tp_location[1]) ? $region_list[$tp_location[1]] : '';
        $data["wed_place_explan"] = !empty($data["wed_place"]) ? $tp_location_text : '';

        $this->load->view("bid_view/detail2016_view",$data);
    }

    /**
     * 获取商家招投标中的中标商家信息
     */
    public function getBidShopperList()
    {
        $input = $this->input->get();
        $res = $this->curl->post($this->config->item('ew_domain').'/erp/bid/bid-shopper-list', $input);
        $res_arr = json_decode($res , true);
        if($res_arr['result'] == 'succ'){
            return success($res_arr['info']);
        }else{
            return failure("ew error: ".$res_arr['msg']);
        }
    }

    /*
     * 获取状态配置
     */
    private static function _getStatus()
    {
        $status = array(
            'published' => 1,//发布需求
            'part_response' => 5,//部分响应
            'all_response' => 10,//响应完成
            'close' => 90,//策划师关闭需求
            'timeout' => 91//指定时间内没有响应
            );

        $status_explan = array(
            'published' => '待响应',
            'part_response' => '部分响应',
            'all_response' => '响应完成',
            'close' => '商家关闭',
            'timeout' => '超时关闭',
            );
        return array($status,$status_explan);
    }

    /*
     * 获取订单状态配置
     */
    private static function _getOrderStatus()
    {
        $order_status = array(
            'published' => 1,//发起响应
            'close' => 90,//订单关闭
            );

        $order_status_explan = array(
            'published' => '发起响应',
            'close' => '订单关闭',
            );
        return array($order_status,$order_status_explan);
    }

    private static function _getShopperAlias()
    {
        $shopper_alias = array(
            'wedmaster' => '主持人',
            'wedvideo' => '摄像师',
            'sitelayout' => '场地布置',
            'makeup' => '化妆师',
            'wedphotoer' => '摄影师',
            );
        return $shopper_alias;
    }

}
