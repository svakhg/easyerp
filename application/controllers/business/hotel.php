<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * author: easywed
 * createTime: 15/10/14 17:26
 * description:酒店商机管理
 */

class Hotel extends App_Controller
{
    const HOTEL_OPER_NAME = '酒店运营';
    public function __construct()
    {
        parent::__construct();

        $this->load->model('business/business_model' , 'business');
        $this->load->model('business/business_extra_model' , 'business_extra');
        $this->load->model('sys_user_model', 'sum');
        $this->load->model('system/roles_model', 'srm');
        $this->load->model('system/department_model', 'sdm');
        $this->load->helper('ew_filter');
    }

    public function index()
    {
        //获取商机状态
        list($bstatus , $bstatus_explan) = $this->business->getBusinessStatus();
        //获取来源
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();

        $hotel_user_info = $this->business->getHotelUsers(array($bsource['website_hotel_search'],$bsource['mobile_hotel_search'],$bsource['hotel_visit']));
        foreach($hotel_user_info as $user)
        {
            $hotel_user_ids[] = $user['sys_uid'];
        }

        $role_info = $this->srm->getInfoByName(self::HOTEL_OPER_NAME);

        $this->_data['is_satrap'] = !$this->session->userdata('satrap') ? false : true;
        $this->_data['hotel_opers'] = isset($role_info['id']) ? $this->sum->getAllByRoleIds($role_info['id']) : array();
        //$this->_data['hotel_opers'] = count($hotel_user_ids) > 0 ? $this->sum->findUsers(array('id' => $hotel_user_ids)) : array();
        list($status, $status_explan) = $this->business->getBusinessStatus();
        $this->_data['status'] = $status;
		$this->_data['status_explan'] = $status_explan;
        $this->load->view('business/hotel', $this->_data);
    }

    /**
     * 酒店运营人员录入商机入口
     * @return mixed
     */
    public function create($bid = 0)
    {
        $params = $this->input->post();
        $params = ew_filter_quote_html($params);

        if(empty($params['mobile']))
        {
            return failure('请输入客户手机');
        }

        if(empty($params['hotel_name']))
        {
            return failure('请输入合作酒店名称');
        }

        //获取商机录入人员信息
        list($sysuser , $sysuser_explan) = $this->business->getWBusinessWriter();
        //获取商机状态
        list($bstatus , $bstatus_explan) = $this->business->getBusinessStatus();
        //获取商机类型
        list($btype , $btype_explan) = $this->business->getBusinessType();
        //获取找商家方式
        list($findtype , $_) = $this->business->getFindShopType();
        //获取商机来源列表
        list($source , $source_explan) = $this->business->getBusinessSource();

        $business_info = $business_extra_info = array();
        $business_info = array(
            'username' => DD($params, 'username'),
            'mobile' => DD($params, 'mobile'),
            'hotel_name' => DD($params, 'hotel_name'),
            'wed_date' => strtotime(DD($params, 'wed_date', 0)),
            'sys_usertype' => $sysuser['sys_usertype_hotel'],
            'sys_uid' => $this->session->userdata('admin_id'),
            'ordertype' => $btype['wed_place'], //婚宴酒店
            'status' => $bstatus['newadd'],
            'tradeno' => $bid <= 0 ? $this->business->generateTradeNo() : '',
            'source' => $source['hotel_visit'],
            'source_note' => DD($params, 'hotel_name'),
            'is_test' => DD($params, 'is_test', 0)
        );

        $business_extra_info = array(
            'moredesc' => DD($params, 'note'),
            'findtype' => $findtype['easywed_recommand']
        );

        if($bid > 0)
        {
            unset($business_info['tradeno']);
            $binfo = $this->business->findByCondition(array('id' => $bid));
            if(!$binfo)
            {
                return failure('商机不存在');
            }

            $this->business->updateByCondition($business_info , array('id' => $bid));
            $this->business_extra->updateByCondition($business_extra_info , array('bid' => $bid));
            return success('编辑成功');
        }
        else
        {
            //把此手机号商机所属老商机显示状态标为不显示
            //$this->business->updateByCondition(array('show' => 0) , array('mobile' => $business_info['mobile']));
            //插入商机
            $bid = $this->business->add($this->business->prepareData($business_info));
            if($bid > 0)
            {
                $business_extra_info['bid'] = $bid;
                $resp = $this->business_extra->add($this->business_extra->prepareData($business_extra_info));
                if($resp > 0)
                {
                    return success('添加成功');
                }
            }
            return success('添加失败');
        }
    }

    /**
     * 获取酒店商机列表
     * @return mixed
     */
    public function search()
    {
        //获取商机录入人员信息
        list($sysuser , $sysuser_explan) = $this->business->getWBusinessWriter();
        //获取商机状态
        list($bstatus , $bstatus_explan) = $this->business->getBusinessStatus();
        //获取来源
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        $bstatus_info = array_flip($bstatus);

        $params = $this->input->get();
        $params = ew_filter_quote_html($params);

        $conditions = array();

        if(!$this->session->userdata('satrap'))
        {
            //$conditions['sys_usertype'] = $sysuser['sys_usertype_hotel'];
            $conditions['sys_uid'] = $this->session->userdata('admin_id');
        }
        else
        {
            $is_satrap = true;
            $conditions['source'] = $bsource['hotel_visit'];
        }

        if($params['status_1'] == 100 && $params['status_2'] == 0)
        {
            $conditions['status'] = array($bstatus['follow_noanswer'] , $bstatus['follow_next']);
        }
        elseif($params['status_1'] == 101 && $params['status_2'] == 0)
        {
            $conditions['status'] = array($bstatus['garbage_invalid_info'] , $bstatus['garbage_three_times'] , $bstatus['garbage_repeat'] , $bstatus['follow_noanswer'] , $bstatus['garbage_other']);
        }
        elseif($params['status_2'] != 0)
        {
            $conditions['status'] = $params['status_2'];
        }
        elseif($params['status_1'] != 0)
        {
            $conditions['status'] = $params['status_1'];
        }

        if($hotel_name = DD($params, 'hotel_name'))
        {
            $conditions['hotel_name'] = $hotel_name;
        }

        if($sys_username = DD($params, 'sys_username'))
        {
            $conditions['sys_uid'] = intval($sys_username);
        }

        $perpages = intval($params['pagesize']);
        $page = intval($params['page']);
        $page = $page > 0 ? $page : 1;

        //获取记录数
        $result_nums = $this->business->counts($conditions);
        $result = $this->business->findAllBusiness($conditions, array('start'=> ($page - 1) * $perpages ,'nums'=> $perpages), array('id' => 'desc'));
        $sys_uids = array();
        foreach($result as &$business)
        {
            $business['createtime'] = date('Y-m-d H:i:s',$business['createtime']);
            $business['wed_date'] = !empty($business['wed_date']) ? date('Y-m-d' , $business['wed_date']) : '';
            $business['bstatus'] = isset($bstatus_info[$business['status']]) ? $bstatus_explan[$bstatus_info[$business['status']]] : $bstatus_explan['newadd'];
            $business['note'] = '';
            $business['hotel_name'] = empty($business['hotel_name']) ? $business['source_note'] : $business['hotel_name'];
            if($business['sys_uid'] > 0)
            {
                $sys_uids[] = $business['sys_uid'];
            }
        }

        $bids = array_keys($result);
        if(count($bids) > 0)
        {
            $business_extra = $this->business_extra->findAll(array('bid' => $bids));
            foreach($business_extra as $val)
            {
                $result[$val['bid']]['note'] = $val['moredesc'];
            }
        }

        //获取酒店运营信息
        if(count($sys_uids) > 0)
        {
            $sys_users = $this->user->findUsers(array('id' => $sys_uids));
            foreach($result as &$business)
            {
                $business['sys_username'] = isset($sys_users[$business['sys_uid']]) ? $sys_users[$business['sys_uid']]['username'] : '';
            }
        }
        $info = array(
            'total' => $result_nums,
            'rows' => array()
        );
        foreach($result as $val)
        {
            $info['rows'][] = $val;
        }
        return success($info);
    }

    /**
     * 酒店管理编辑
     */
    public function update()
    {
        $params = $this->input->post();
        $bid = intval($params['bid']);
        $this->create($bid);
    }
}

