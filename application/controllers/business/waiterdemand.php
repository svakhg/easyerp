<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * author: easywed
 * createTime: 15/10/14 17:14
 * description:客服需求录入控制器
 */

class Waiterdemand extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('business/business_model' , 'business');
        $this->load->model('business/business_extra_model' , 'business_extra');
        $this->load->helper('ew_filter');
    }

    public function index()
    {
        list($bsource , $bsource_explan) = $this->business->getBusinessSource();
        list($customer , $customer_explan) = $this->business->getCustomerIdentify();

        //商机来源
        $this->_data['bsource'] = array(
            $bsource['callcenter'] => $bsource_explan['callcenter'],
            $bsource['live800'] => $bsource_explan['live800'],
            $bsource['weibo'] => $bsource_explan['weibo'],
            $bsource['youzan'] => $bsource_explan['youzan'],
            $bsource['53kf'] => $bsource_explan['53kf'],
            $bsource['other'] => $bsource_explan['other'],
        );

        //客户身份
        $this->_data['customer'] = $customer;
        $this->_data['customer_explan'] = $customer_explan;

        $this->load->view('business/waiteradd', $this->_data);
    }

    /**
     * 客服人员录入商机入口
     * @return mixed
     */
    public function create()
    {
        $params = $this->input->post();
        $params = ew_filter_quote_html($params);

        if(empty($params['source']))
        {
            return failure('请输入商机来源');
        }

        $valid_status = false;
        $valid_fields = array('mobile' , 'tel' , 'weixin' , 'qq');

        foreach($valid_fields as $fields)
        {
            if(isset($params[$fields]) && !empty($params[$fields]))
            {
                $valid_status = true;break;
            }
        }
        if(!$valid_status)
        {
            return failure('手机号、电话、微信、QQ至少填写一项');
        }

        //获取新人身份信息
        list($customer_identify , $status_explan) = $this->business->getCustomerIdentify();
        $customer_vals = array_values($customer_identify);
        //获取商机录入人员信息
        list($sysuser , $sysuser_explan) = $this->business->getWBusinessWriter();
        //获取商机状态
        list($bstatus , $bstatus_explan) = $this->business->getBusinessStatus();
        //获取商机类型
        list($btype , $btype_explan) = $this->business->getBusinessType();
        //获取找商家方式
        list($findtype , $_) = $this->business->getFindShopType();

        $business_info = $business_extra_info = array();
        $business_info = array(
            'source' => intval($params['source']),
            'source_note' => DD($params, 'source_note'),
            'username' => DD($params, 'username'),
            'userpart' => in_array(intval($params['identify']), $customer_vals) ? intval($params['identify']) : $customer_identify['bridegroom'],
            'mobile' => DD($params, 'mobile'),
            'weixin' => DD($params, 'weixin'),
            'tel' => DD($params, 'tel'),
            'qq' => DD($params, 'qq'),
            'sys_usertype' => $sysuser['sys_usertype_service'],
            'sys_uid' => $this->session->userdata('admin_id'),
            'status' => $bstatus['newadd'],
            'ordertype' => $btype['wed_plan'],
            'tradeno' => $this->business->generateTradeNo(),
            'is_test' => DD($params, 'is_test', 0)
        );

        $business_extra_info = array(
            'moredesc' => DD($params, 'note'),
            'findtype' => $findtype['easywed_recommand']
        );

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
