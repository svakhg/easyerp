<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * 向erp推送分单详情下服务过程的数据
 * by zhangmiao
 */

class Workerserviceprocess extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 添加服务过程
     */
    public function saveRecord()
    {
        $data = $this->input->post();
        // $data = array(
        //     'record' => array(
        //         'id' => 2,
        //         'shopper_uid' => 44,
        //         'shop_map_id' => 1234,
        //         'communicate_time' => 1447925693,
        //         'create_time' => 1447925693,
        //         'status' => 1,
        //         'content' => 'hahahaha',
        //         'invitation_music_id' => 1,
        //         'tradeno' => '20150519174622369481',
        //         ),
        //     'addImage' => array(
        //         array(
        //             'id' => 3,
        //             'url' => "/aaa.jpg",
        //             'communicate_id' => 2,
        //             'create_time' => 1447925693,
        //             ),
        //         array(
        //             'id' => 4,
        //             'url' => "/aaa.jpg",
        //             'communicate_id' => 2,
        //             'create_time' => 1447925693,
        //             ),
        //         ),
        //     );
        //根据tradeno，shopper_uid查询shop_map_id
        $business = $this->erp_conn->from("business")->where("tradeno",$data['record']['tradeno'])->get()->result_array();
        if(!isset($business[0])){
            return failure("unknow business data");
        }
        $shop_map = $this->erp_conn->from("business_shop_map")->where(array("bid"=>$business[0]['id'],"shop_id"=>$data['record']['shopper_uid']))->get()->result_array();
        if(!isset($shop_map[0])){
            return failure("unknow shop_map data");
        }

        //根据shop_map_id删除record数据
        $this->erp_conn->delete("communicate_record",array("shop_map_id"=>$shop_map[0]['id'],"ew_id"=>$data['record']['id']));
        //根据record_id删除image数据
        $this->erp_conn->delete("communicate_image",array("communicate_id"=>$data['record']['id']));

        //组合record数据
        $record_data = $data['record'];
        unset($record_data['id']);
        $record_data['ew_id'] = $data['record']['id'];
        $record_data['shop_map_id'] = $shop_map[0]['id'];
        $res_record = $this->erp_conn->insert("communicate_record",$record_data);

        //组合image数据
        if(!empty($data['addImage'])){
            foreach ($data['addImage'] as $k => $v) {
                $image_data = $v;
                unset($image_data['id']);unset($image_data['imageurl']);
                $image_data['url'] = substr($v['imageurl'], strlen($this->config->config['img_url']));
                $image_data['ew_id'] = $v['id'];
                $this->erp_conn->insert("communicate_image",$image_data);
            }
        }
        if($res_record == true){
            return success("insert success");
        }else{
            return failure("insert failure");
        }
    }

    /*
     * 添加人员通讯录
     */
    public function addContact()
    {
        $data = $this->input->post();
        $data = $data['data'];
        // $data = array(
        //     'id' => 1,
        //     'uid' => 44,
        //     'shop_map_id' => 12,
        //     'username' => 'mengqingtao',
        //     'phone' => '12312345678',
        //     'status' => 1,
        //     'type' => 1,
        //     'is_yijie' => 1,
        //     'shopper_uid' => 1234,
        //     'create_time' => 1447925693,
        //     'tradeno' => '20150519174622369481',
        //     );
        //根据tradeno，shopper_uid查询shop_map_id
        $business = $this->erp_conn->from("business")->where("tradeno",$data['tradeno'])->get()->result_array();
        if(!isset($business[0])){
            return failure("unknow business data");
        }
        $shop_map = $this->erp_conn->from("business_shop_map")->where(array("bid"=>$business[0]['id'],"shop_id"=>$data['uid']))->get()->result_array();
        if(!isset($shop_map[0])){
            return failure("unknow shop_map data");
        }
        //组合address数据
        $address_data = $data;
        unset($address_data['id']);
        $address_data['ew_id'] = $data['id'];
        $address_data['shop_map_id'] = $shop_map[0]['id'];
        $res_address = $this->erp_conn->insert("shopper_addressbook",$address_data);
        if($res_address == true){
            return success("insert success");
        }else{
            return failure("insert failure");
        }
    }

    /*
     * 删除人员通讯录
     */
    public function delContact()
    {
        $data = $this->input->post();
        $data = $data['data'];
        // $data = array(
        //     'id' => 1
        //     );
        $res_del = $this->erp_conn->delete("shopper_addressbook",array("ew_id"=>$data['id']));
        if($res_del == true){
            return success("delete success");
        }else{
            return failure("delete failure");
        }
    }

}
