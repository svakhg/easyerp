<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 *商家招投标
 */
class Bidexamine extends App_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('bid/Shopperdemand','bid');
        $this->load->model('bid/Shopperorder','order');
        $this->load->model('bid/Detaildemand','detail');
        $this->load->model('Sys_trademarksetting_model','trademark');
        $this->load->model('Sys_user_model','user');
        $this->load->model('baidupush/baidupush');
    }

    public function index(){
		$infos['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$infos['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
        $this->load->view('bid_view/bidexamine_view',$infos);
    }

    /*
     * 获取商家招投标需求列表
     *
     */
    public function bidContentList(){
        $inputs = $this->input->get();
        $this->load->model('bid');
        $this->bid->shopperContentList($inputs,1);
        
    }

    /*
     * 获取商家招投标订单列表
     *
     */
    public function bidOrderList(){
        $res = $this->bid->getOrderList();
        echo json_encode($res);exit;
    }

    /*
     * 发标商家需求审核
     * method:POST
     */
    public function bidContentExamine()
    {
        $input = $this->input->post();
        if(!isset($input['bid_id']) || !isset($input['examine']) || !in_array($input['examine'],array(0,1)))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        $data['bid_id'] = $input['bid_id'];
        $examine = $input['examine'];
        //验证需求是否存在
        $item = $this->bid->getOneDemand($data);
        if(empty($item[0]))
        {
            echo json_encode(array('result' => 'fail','info' => '该需求不存在'));exit;
        }
        //验证是否已分配商家
        $orderCount = $this->bid->getOrderCountByDemandId($data['bid_id']);
        if($orderCount <= 0)
        {
            echo json_encode(array('result' => 'fail','info' => '请先分配商家再审核'));exit;
        }
        if($examine == 1)//审核成功
        {
            $data['content_status'] = 2;
            if($item[0]['mode'] == 1)//招投标
            {
                $data['order_status'] = 11;
            }
            else if($item[0]['mode'] == 2)//指定商家
            {
                $data['order_status'] = 16;
            }
        }
        elseif($examine == 0)//审核不成功
        {
            $data['content_status'] = 3;
            $data['order_status'] = 1;
        }
        $res_content = $this->bid->changeBidContentStatus($data);
        $res_order = $this->bid->changeBidOrderStatus($data);
        if($res_content === true && $res_order === true)
        {
            //baiduPush begin
            $order_list = $this->order->getOrdersByContentId($data['bid_id'],"id, status, shopper_user_id");
            foreach($order_list as $val){
                $data['demand_id'] =  $item[0]['demand_id'];
                $data['id'] =  $val['id'];
                $data['status'] =  $val['status'];
                $data['shopper_user_id'] =  $val['shopper_user_id'];
                $this->baidupush->BaiduBusinessPushForErp($data);
            }
            //baiduPush end

            //给投标商家发送短信
            $phone_arr = $this->order->getTendererPhone($data['bid_id']);
            $msg = '认证策划师正在为她的客户寻找服务商，请您尽快登录易结，在订单管理中回复具体信息。';
            $sms_res = $this->sms->send($phone_arr,$msg);
            
            /*
              * 添加审核需求日志
            */
            $u_ids = $data['bid_id'];
            $u_id = explode(',', $u_ids);
            $this->bid->getShoppersLog($u_ids,0,3);

            echo json_encode(array('result' => 'succ','info' => '审核成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '审核失败'));exit;
        }
    }


    /*
     * 需求详情页
     *  method:get
     */
    public function review()
    {
        $data = array();
        $inputs = $this->input->get();
		$data['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$data['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
		$data['backurl'] = urldecode($this->input->get("backurl")) ? urldecode($this->input->get("backurl")) : "";
        if(!isset($inputs['id']) && !is_numeric(isset($inputs['id'])))
        {
            show_error('该条需求详情不存在!');
        }
        //获取该条需求的详情
        $content = $this->detail->getDetailContent($inputs['id']);
        if(empty($content)){
            show_error('该条需求详情不存在!');
        }

        //检查订单的状态，以判断需求所处的状态 如：待分配商 、待审核 or 商家投标中


        //要找的商家提供的服务类型
        if($content['shopper_alias'] == 'wedphotoer'){
            if($content['service_type'] == 'hspz'){
                $data['demand_info']['service_type'] = '婚纱照拍摄';
            }elseif($content['service_type'] == 'hlgp'){
                $data['demand_info']['service_type'] = '婚礼当天跟拍';
            }
        }elseif($content['shopper_alias'] == 'wedvideo'){
            if($content['service_type'] == 'wdy'){
                $data['demand_info']['service_type'] = '婚礼前的爱情微电影';
            }elseif($content['service_type'] == 'hlgp'){
                $data['demand_info']['service_type'] = '婚礼当天跟拍';
            }
        }

        //交易提示内容
        if(!empty($content['remander_id'])){
            $remander = $this->trademark->getInfoById($content['remander_id']);
        }
        $data['remander'] = isset($remander['name']) ? $remander['name'] : '';

        //顾问内容
        if(!empty($content['counselor_uid'])){
            $counselor = $this->user->getInfoById($content['counselor_uid']);
        }
        $data['counselor'] = isset($counselor['username']) ? $counselor['username'] : '';
           //支付方式
		$sq = "SELECT `id`,`setting_id`, `name`,`enable`,`order` FROM (`ew_erp_sys_basesetting`) WHERE `setting_id` = 176 and `order` = 22 and `enable`= 1";
	    $brr = $this->erp_conn->query($sq);
	    $list = $brr->result_array();
	    $data['crr'] = $list; 
     	
        $data['content'] = $content;

        $data['cur_status'] = $this->order->getDemandBidStatusInOrders($inputs['id']);
//print_r($data);die();
        $this->load->view('bid_view/detail_view',$data);
    }


    /*
     * 关闭需求
     * method:POST
     */
    public function closeBid()
    {
        $input = $this->input->post();
        //print_r($input);die;
        if(!isset($input['bid_ids']) || empty($input['bid_ids']) || !isset($input['close_reason']) || empty($input['close_reason']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        $res = $this->bid->closeDemand($input);
        if($res === true)
        {
            /*
              * 添加关闭需求需求日志
            */

            $dmid = $input["bid_ids"];   
            $data = $this->bid->ngsLog($dmid);
            $this->bid->demandlog($data['id'],0,'',$data['demand_id'],'关闭需求','商家招投标管理-关闭需求',0);

           
            echo json_encode(array('result' => 'succ','info' => '关闭成功'));exit;
        }
        else
        {
            echo json_encode(array('result' => 'fail','info' => '关闭失败'));exit;
        }
    }

    /*
     * 获得商家列表_分配商家
     * @author by Abel
     * method get
     *  type : 要找商家的别名代号 shoper_alia_code 如 策划师 1435
        page：分页 页码 默认是1
        pagesize：分页 显示数量 默认是10条
        mode: 商家类型：个人、公司或者工作室
        address：商家的地区 for example： 2,3
        keyword: 查询的关键字 模糊查询有 “商家昵称” “店铺名称” “商家手机”

     * @ return array 返回商家的列表
     */
    public function getShoppersInfo(){
        $input = $this->input->get();

        $type = $input['type'];//商家类型

        $params['page'] = isset($input['page']) ? $input['page'] : 1;
        $params['pagesize'] = isset($input['pagesize']) ? $input['pagesize'] : 10;
        $params['mode'] = !empty($input['mode']) ? $input['mode'] : '';

        $params['price_start'] = !empty($input['price_start']) ? $input['price_start'] : '';//服务报价
        $params['price_end'] = !empty($input['price_end']) ? $input['price_end'] : '';

        //案例数量 对应shoper_user表里的 “从事婚礼次数（1代表10场以内，2代表11-50场，3代表51-200场，4代表200场以内）”；
        $params['wed_num_start'] = !empty($input['opus_num_start']) ? $input['opus_num_start'] : '';//案例数量
        $params['wed_num_end'] = !empty($input['opus_num_end']) ? $input['opus_num_end'] : '';

        //处理地区
        $params['province'] = !empty($input['province']) ? $input['province'] : '';
        $params['city'] = !empty($input['city']) ? $input['city'] : '';
        if(! empty($params['province']) && ! empty($params['city'])){
            $params['address'] = $params['province'].','.$params['city'];
        }elseif(! empty($params['province']) && empty($params['city'])){
            $params['address'] = $params['province'];
        }
        //关键字
        $params['keyword'] = !empty($input['keywords']) ? $input['keywords'] : '';

        $info  = $this->detail->getShopperInfoByType($params,$type);
        if(! empty($info['rows']))
        {
            foreach($info['rows'] as &$v){
                switch($v['wed_num']){
                    case 1:
                        $v['wed_num_txt'] = '10场以内';
                        break;
                    case 2:
                        $v['wed_num_txt'] = '11-50场';
                        break;
                    case 3:
                        $v['wed_num_txt'] = '51-200场';
                        break;
                    case 4:
                        $v['wed_num_txt'] = '200场以内';
                        break;
                    default :
                        $v['wed_num_txt'] = '其他';
                        break;
                }
            }
        }

        return success($info);
    }

    /*
     * 获得需求对应的order订单列表
     * @author by Abel
     * @method get
     * @param
     *      demand_id 当前的需求id
            page：分页 页码 默认是1
            pagesize：分页 显示数量 默认是10条
            address：商家的地区 for example： 2,3
            keyword: 查询的关键字 模糊查询有 “商家昵称” “店铺名称” “商家手机”

     * @return array 返回需求对应的订单列表
     */
    public function getDemandOrderList()
    {
        $input = $this->input->get();
        if(!isset($input['demand_id']) || empty($input['demand_id']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }
        $params['bid_id'] = $input['demand_id'];
        $params['page'] = (isset($input['page']) && $input['page'] > 0) ? $input['page'] : 1;
        $params['pagesize'] = (!empty($input['pagesize'])&& $input['pagesize'] > 9) ? $input['pagesize'] : 10;

        $params['order_status'] = !empty($input['order_status']) ? $input['order_status'] : '';
        $params['letter_status'] = !empty($input['letter_status']) ? $input['letter_status'] : '';

        //处理地区
        $params['province'] = !empty($input['province_dlgs']) ? $input['province_dlgs'] : '';
        $params['city'] = !empty($input['city_dlgs']) ? $input['city_dlgs'] : '';
        if(! empty($params['province']) && ! empty($params['city'])){
            $params['address'] = $params['province'].','.$params['city'];
        }elseif(! empty($params['province']) && empty($params['city'])){
            $params['address'] = $params['province'];
        }

        $params['keyword'] = !empty($input['condition_text']) ? $input['condition_text'] : '';

        $info  = $this->detail->getDemandOrdersById($params);
        if(!empty($info['rows'])){
            foreach($info['rows'] as &$v){
                //招投标状态
                switch($v['status']){
                    case 1:
                        $v['status_txt'] = '初始订单待审核';
                        break;
                    case 11:
                        $v['status_txt'] = '审核成功待合作商投标';
                        break;
                    case 21:
                        $v['status_txt'] = '合作商竞标提交意向书';
                        break;
                    case 31:
                        $v['status_txt'] = '意向书审核通过待商家确定合作商';
                        break;
                    case 41:
                        $v['status_txt'] = '商家确定合作商';
                        break;
                    case 51:
                        $v['status_txt'] = '商家确定完成服务';
                        break;
                    case 61:
                        $v['status_txt'] = '商家对合作商已完成评价';
                        break;
                    case 16:
                        $v['status_txt'] = '指定商家审核成功待合作商接单';
                        break;
                    case 26:
                        $v['status_txt'] = '指定商家审核成功待合作商接单';
                        break;
                    case 36:
                        $v['status_txt'] = '指定商家审核成功待合作商接单';
                    break;
                    case 97:
                        $v['status_txt'] = '放弃订单';
                        break;
                    case 98:
                        $v['status_txt'] = '订单失败';
                        break;
                    case 99:
                        $v['status_txt'] = '需求关闭';
                        break;
                }

                //自荐信审核状态
                $v['letter_status'] = '';
                if($v['recommend_letter'] != ''){
                    switch($v['status']){
                        case 21:
                            $v['letter_status'] = '待审核';
                            break;

                        case 31:
                        case 41:
                        case 51:
                        case 61:
                            $v['letter_status'] = '审核通过';
                            break;

                        default:
                            $v['letter_status'] = '';
                            break;
                    }
                }

            }
        }
        return success($info);
    }

    /*
     * 移除需求下的指定订单
     * method post
     * param
     *  demand_id 需求id
     *  order_ids 要移除订单id的列表 1,2,3,4,5
     */
    public function removeDemandOrdersByIds()
    {
        $input = $this->input->post();
        if(!isset($input['demand_id']) || empty($input['demand_id']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }

        if(!isset($input['order_ids']) || empty($input['order_ids']))
        {
            echo json_encode(array('result' => 'fail','info' => '参数不正确'));exit;
        }

        $params['bid_id'] = $input['demand_id'];
        $params['order_ids'] = $input['order_ids'];

        //判断该条需求的是否能被移除（即该条需求是审核）
        $check_demand = Detaildemand::checkDemandExamine($params);
        if($check_demand){
            echo json_encode(array('result' => 'fail','info' => '审核过的需求不能移除商家订单'));exit;
        }
        //移除操作
        $result = $this->order->removeDemandOrder($params);

        if($result)
        {
            //添加移除商家日志
             $u_ids = $input['order_ids'];
             $id = $input['demand_id'];
             $data_log = $this->bid->getShoppersLog($u_ids,$id,4);

            echo json_encode(array('result' => 'succ','info' => '移除成功'));exit;
        }
    }


}