<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * author: easywed
 * createTime: 15/10/16 15:53
 * description:分单管理_分单列表_分单数据检索
 */

class Partorder extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('business/business_model' , 'business');
        $this->load->model('business/business_extra_model' , 'business_extra');
        $this->load->model("business/common_model" , 'buscommon');
        $this->load->model('business/business_shop_map_model' , 'shopmap');
        $this->load->model('sys_user_model', 'sum');
        $this->load->model('system/roles_model', 'srm');
        $this->load->model('system/department_model', 'sdm');

        $this->load->helper('ew_filter');
        $this->load->helper('excel_tools');
    }

    public function index()
    {
        //获取交易状态
        list($tstatus , $tstatus_explan) = $this->business->getTradeStatus();
        $this->_data['trade_status'] = $tstatus;
        $this->_data['trade_status_explan'] = $tstatus_explan;

        //获取建单来源
        list($source , $source_explan) = $this->business->getBusinessSource();
        $this->_data['source'] = $source;
        $this->_data['source_explan'] = $source_explan;

        //获取新人顾问
        $role_info = $this->sdm->getWedAdviser();
        $adviser_list = $this->sum->findAll(array('department' => $role_info['id']), array(), array());
        $this->_data['adviser_list'] = $adviser_list;

        // 运营人员
        $operater_department = $this->sdm->getOperater();
        $operater = $this->sum->findAll(array('department' => $operater_department['id'] , 'num_code <' => '99999000'), array(), array());
        $this->_data['operater'] = $operater;

        //获取客户类型
        $this->_data['usertype'] = $this->business->getUserType();

        $this->load->view('business/partorderlist' , $this->_data);
    }

    /**
     * 分单检索列表
     * @return mixed
     */
    public function search($export = false)
    {
        $params = $this->input->get();
        $params = ew_filter_quote_html($params);

        //获取商机状态
        list($bstatus , $bstatus_explan) = $this->business->getBusinessStatus();

		if($this->session->userdata('is_test') == 0)
		{
			$conditions['is_test'] = 0;
		}
        $conditions['status'] = $bstatus['parted'];

        if($params['trade_status'])
        {
            $conditions['trade_status'] = intval($params['trade_status']);
        }

        if($params['usertype'])
        {
            $conditions['usertype'] = $params['usertype'];
        }

        //跟进策划师
        if($params['planer'])
        {
            $shoper_id = 0; $bids = array(0);
            $shoper = $this->buscommon->shoperInfo(array('keyword' => $params['planer']));
            if(count($shoper) > 0)
            {
                $shoper_id = $shoper['rows'][0]['uid'];
            }
            if($shoper_id > 0)
            {
                $shop_map_info = $this->shopmap->getInfoByShopId($shoper_id);
                foreach($shop_map_info as $map)
                {
                    $bids[] = $map['bid'];
                }
            }
            $conditions['bid'] = $bids;
        }

        if($params['ordertime_from'])
        {
            $conditions['ordertime >='] = strtotime($params['ordertime_from']);
        }

        if($params['ordertime_to'])
        {
            $conditions['ordertime <='] = strtotime($params['ordertime_to']);
        }

        if($params['weddate_from'])
        {
            $conditions['wed_date >='] = strtotime($params['weddate_from']);
        }

        if($params['weddate_to'])
        {
            $conditions['wed_date <='] = strtotime($params['weddate_to']);
        }

        $allow_cond = array('mobile' , 'tradeno' , 'username' , 'wed_place' , 'bid');
        if(in_array($params['cond_type'] , $allow_cond) && $params['cond_value'])
        {
            if($params['cond_type'] == 'bid')
            {
                $conditions['bid'] = $this->business->formatBid($params['cond_value'] , 0 , true);
            }
            else
            {
                $conditions[$params['cond_type'] . ' like'] = '%'.$params['cond_value'].'%';
            }
        }

        if($params['source'])
        {
            $conditions['source'] = $params['source'];
        }

        if($params['adviser'])
        {
            $conditions['follower_uid'] = $params['adviser'];
        }
        if($params['operate_uid'])
        {
            $conditions['operate_uid'] = $params['operate_uid'];
        }

        //属于策划师运营部 并且 不是主管 只能看到自己负责的商机
        $user_info = $this->sum->getInfoById($this->session->userdata('admin_id'));
        if($user_info['satrap'] == 0)
        {
            $conditions['follower_uid'] = $this->session->userdata('admin_id');
        }
        if($user_info['department'] == 2 && $user_info['satrap'] == 0)
        {
            unset($conditions['follower_uid']);
            unset($conditions['operate_uid']);
            $conditions['where_str'] = '(`follower_uid` = '.$this->session->userdata('admin_id').' OR `operate_uid` = '.$this->session->userdata('admin_id').')';
        }

        $perpages = intval(DD($params, 'pagesize', 10));
        $page = intval(DD($params, 'page', 1));
        $page = $page > 0 ? $page : 1;
        $perpages = $perpages < 20 ? 20 : $perpages;

        $result_nums = $this->business->findBusinessJoinExtra($conditions , array() , true);
        //导出数据
        if($export)
        {
            $result = $this->business->findBusinessJoinExtra($conditions, array(), false);
            $formatData = $this->_makeData($result);
            $exporter = new ExportDataExcel('browser', '分单表_' . date('Y-m-d') . '.xls');
            $exporter->initialize();
            $exporter->addRow(array('交易编号','交易状态','新人顾问','运营','交易类型','客户类型','客户姓名','客户手机','婚礼日期','婚礼地点','预算','分单月份','分单时间','签约商家','跟进策划师','建单来源','来源说明','找商家方式','商机编号'));
            foreach($formatData as $val)
            {
                $tmpdata = array(
                    $val['tradeno'],$val['trade_status'],$val['follow_username'],$val['operate_name'],
                    $val['wed_type'],$val['usertype'],$val['username'],
                    $val['mobile'],$val['wed_date'],$val['wed_place'],
                    $val['budget'],$val['order_month'],$val['ordertime'],
                    $val['shoper'],$val['planer'],$val['source'],
                    $val['source_note'],$val['findtype'],$val['bidstr']
                );
                $exporter->addRow($tmpdata);
            }
            $exporter->finalize();
            exit();
        }
        else {
            $result = $this->business->findBusinessJoinExtra($conditions , array('start'=> ( $page -1 ) * $perpages ,'nums'=> $perpages) , false);
            $formatData = $this->_makeData($result);
            $info = array(
                'total' => $result_nums,
                'rows' => $formatData
            );
            return success($info);
        }
    }

    /**
     * 导出分单列表
     */
    public function exportcsv()
    {
        $this->search(true);
    }

    /**
     * 商机列表数据格式化
     * @param array $data
     * @return array
     */
    private function _makeData($data = array())
    {
        //获取交易状态
        list($tstatus , $tstatus_explan) = $this->business->getTradeStatus();
        //获取商机类型
        list($btype , $btype_explan) = $this->business->getBusinessType();
        //获取婚礼预算
        list($wbudget , $wbudget_explan) = $this->business->getWedBudget();
        //获取来源
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        //找商家方式
        list($findtype , $findtype_explan) = $this->business->getFindShopType();
        $tstatus_map = array_flip($tstatus);
        $btype_map = array_flip($btype);
        $wbudget_map = array_flip($wbudget);
        $bsource_map = array_flip($bsource);
        $findtype_map = array_flip($findtype);

        $sys_uids = $bids = array();
        foreach($data as $key => &$order)
        {
            $sys_uids[] = $order['follower_uid'];
            $order['trade_status'] = isset($tstatus_map[$order['trade_status']]) ? $tstatus_explan[$tstatus_map[$order['trade_status']]] : ''; //交易状态
            $order['wed_type'] = isset($btype_map[$order['ordertype']]) ? $btype_explan[$btype_map[$order['ordertype']]] : ''; //交易类型
            $order['wed_date'] = $order['wed_date'] ? date('Y-m-d' , $order['wed_date']) : ''; //婚礼时间
            $order['budget'] = (isset($order['budget']) && isset($wbudget_map[$order['budget']])) ? $wbudget_explan[$wbudget_map[$order['budget']]] : $wbudget['not_sure']; //婚礼预算
            $order['order_month'] = date('Y-m' , $order['ordertime']); //分单月份
            $order['ordertime'] = date('Y-m-d H:i:s' , $order['ordertime']); //分单时间
            $order['shoper'] = ''; //签约商家
            $order['planer'] = ''; //跟进策划师
            $order['source'] = isset($bsource_map[$order['source']]) ? $bsource_explan[$bsource_map[$order['source']]] : "" ; //建单来源
            $order['findtype'] = isset($findtype_map[$order['findtype']]) ? $findtype_explan[$findtype_map[$order['findtype']]] : "" ; //找商家方式
            $order['busoper'] = '-'; //商家运营
            $operator_info = $this->sum->getInfoById($order['operate_uid']);
            $order['operate_name'] = isset($operator_info['username']) ? $operator_info['username'] : "" ; //运营
            $order['bidstr'] = $this->business->formatBid($order['bid'] , $order['createtime']);
            $order['wed_place'] = ($order['ordertype'] == $btype['plan_place'] || $order['ordertype'] == $btype['wed_place']) ? $order['wed_place_area'] : $order['wed_place'];
            $bids[] = $order['bid'];
        }

        if(count($bids) > 0)
        {
            $shoper_query = $this->shopmap->getShoperidsBybid($bids);
            $shoper_map = $shoper_ids = $shoper_info = array();
            foreach($shoper_query as $sh)
            {
                //如果此商家已经签约，则单独记录一个数据
                if($sh['status'] == Business_shop_map_model::STATUS_SIGN)
                {
                    $shoper_map[$sh['bid'] . '_sign'] = $sh['shop_id'];
                }
                //一个商机会有多个商家
                $shoper_map[$sh['bid']][] = $sh['shop_id'];
                $shoper_ids[] = $sh['shop_id'];
            }

            $shoper_con = $this->buscommon->shoperInfo(array('uids' => implode(',',$shoper_ids)));
            foreach($shoper_con['rows'] as $shop)
            {
                $shoper_info[$shop['uid']] = $shop;
            }
        }

        if(count($sys_uids) > 0)
        {
            $sys_users = $this->user->findUsers(array('id' => $sys_uids));
        }

        foreach($data as $key => &$order)
        {
            $order['follow_username'] = isset($sys_users[$order['follower_uid']]) ? $sys_users[$order['follower_uid']]['username'] : '-';
            //判断签约商家
            if(isset($shoper_map[$order['bid'] . '_sign']))
            {
                $order['shoper'] = isset($shoper_info[$shoper_map[$order['bid'] . '_sign']]) ? $shoper_info[$shoper_map[$order['bid'] . '_sign']]['nickname'] : '';//studio_name
            }
            if(isset($shoper_map[$order['bid']]))
            {
                foreach($shoper_map[$order['bid']] as $s)
                {
                    $order['planer'] .= isset($shoper_info[$s]) ? $shoper_info[$s]['nickname'] . ',' : '';
                }
                $order['planer'] = strlen($order['planer']) > 0 ? substr($order['planer'], 0 ,strlen($order['planer']) - 1) : '';
            }
        }

        return $data;
    }

    /*
     * 服务过程（分单详情中分配商家列表入口）
     */
    public function serviceProcess()
    {
        $data = $this->input->get();
        $shop_map_data = $this->erp_conn->from("business_shop_map")->where("id",$data['shop_map_id'])->get()->result_array();
        $shoper = $this->buscommon->shoperInfo(array('uids' => $shop_map_data[0]['shop_id']));
        $view_data = array();
        $view_data['shop_map_info'] = $shop_map_data[0];
        $view_data['shopper_info'] = isset($shoper['rows'][0]) ? $shoper['rows'][0] : array();
        // print_R($view_data);
        $this->load->view("business/serviceprocess",$view_data);
    }

    /*
     * 获取沟通记录数据
     * shop_map_id
     * page
     * pagesize
     */
    public function getCommunitcateRecord()
    {
        $data = $this->input->get();

        //data
        $this->erp_conn->from("communicate_record")->where(array("shop_map_id"=>$data['shop_map_id'],"status"=>1));
        if(isset($data['page']) && isset($data['pagesize'])){
            $this->erp_conn->limit($data['pagesize'], ($data['page']-1)*$data['pagesize']);
        }
        $record_data = $this->erp_conn->get()->result_array();
        foreach($record_data as $k => $v){
            $record_data[$k]['create_time'] = date("Y-m-d H:i:s",$v['create_time']);
            $record_data[$k]['communicate_time'] = date("Y-m-d",strtotime($v['communicate_time']));
            $record_data[$k]['image_data'] = $this->erp_conn->from("communicate_image")->where("communicate_id",$v["ew_id"])->get()->result_array();
            if(!empty($record_data[$k]['image_data'])){
                foreach($record_data[$k]['image_data'] as $k_img => $v_img){
                    $record_data[$k]['image_data'][$k_img]['url'] = $this->config->config['img_url'].$v_img['url']."@500w.jpg";
                }
            }
            $record_data[$k]['music_data'] = $this->erp_conn->from("invitation_music")->where("musicId",$v["invitation_music_id"])->get()->result_array();
            if(isset($record_data[$k]['music_data'][0])){
                $record_data[$k]['music_name'] = $record_data[$k]['music_data'][0]['musicName'];
            }else{
                $record_data[$k]['music_name'] = "";
            }
        }

        //num
        $record_num = $this->erp_conn->from("communicate_record")
                                ->where(array("shop_map_id"=>$data['shop_map_id'],"status"=>1))
                                ->count_all_results();
        $record = array(
            'total' => $record_num,
            'rows' => $record_data,
            );
        return success($record);
    }


    /*
     * 获取人员通讯录数据
     * shop_map_id
     * page
     * pagesize
     */
    public function getShopperContact()
    {
        $data = $this->input->get();
        $this->erp_conn->from("shopper_addressbook")->where("shop_map_id",$data['shop_map_id']);
        if(isset($data['page']) && isset($data['pagesize'])){
            $this->erp_conn->limit($data['pagesize'], ($data['page']-1)*$data['pagesize']);
        }
        $contact_data = $this->erp_conn->get()->result_array();
        foreach($contact_data as $k => $v){
            $contact_data[$k]['create_time'] = date("Y-m-d H:i:s",$v['create_time']);
            $contact_data[$k]['type_text'] = $this->shopperType($v['type']);
            $contact_data[$k]['is_yijie_text'] = $v['is_yijie'] == 1 ? "是" : "否";
        }
        $contact_num = $this->erp_conn->from("shopper_addressbook")->where("shop_map_id",$data['shop_map_id'])->count_all_results();
        $contact = array(
            'total' => $contact_num,
            'rows' => $contact_data,
            );
        return success($contact);
    }

    /*
     * shopper_address字段配置
     */
    private function shopperType($type)
    {
        switch ($type) {
            case '1':
                $type_text = "主持人";
                break;
            case '2':
                $type_text = "化妆师";
                break;
            case '3':
                $type_text = "摄影师";
                break;
            case '4':
                $type_text = "摄像师";
                break;
            case '5':
                $type_text = "场地布置";
                break;
            case '21':
                $type_text = "督导";
                break;
            default:
                $type_text = "";
                break;
        }
        return $type_text;
    }
}
