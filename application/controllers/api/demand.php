<?php
/**
 * author: easywed
 * createTime: 15/10/14 10:59
 * description:商机api、与主站进行商机数据交互
 */
//error_reporting(0);
class Demand extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('business/business_model' , 'business');
        $this->load->model('business/business_extra_model' , 'business_extra');
        $this->load->model('business/business_shop_map_model' , 'shopmap');
        $this->load->library('ErpHash');
        $this->load->helper('ew_filter');
    }

    /**
     * 创建需求接口
     * @return mixed
     */
    public function createDemand()
    {
        $params = $this->input->post();
        $param_sign = $params['sign'];

        unset($params['sign']);

        if(!ErpHash::validate($params , $param_sign))
        {
            return failure('签名错误');
        }

        if(time() - $params['sendtime'] > 300)
        {
            return failure('抱歉，链接已失效');
        }

        if(empty($params['mobile']))
        {
            return failure('抱歉，手机号不能为空');
        }

        //获取婚礼预算列表
        list($budget , $budget_explan) = $this->business->getWedBudget();
        //获取商机来源列表
        list($source , $source_explan) = $this->business->getBusinessSource();
        //获取商机类型
        list($btype , $btype_explan) = $this->business->getBusinessType();
        //获取找商家方式
        list($findtype , $_) = $this->business->getFindShopType();
        //获取商机录入人员信息
        list($sysuser , $sysuser_explan) = $this->business->getWBusinessWriter();
        //获取商机状态
        list($bstatus , $bstatus_explan) = $this->business->getBusinessStatus();

        $btype_vals = array_values($btype);
        $budget_explan = array_flip($budget_explan);
        $wed_budget = $budget[$budget_explan[$params['budget']]];

        $params = ew_filter_quote_html($params);
        $source_key = DD($params , 'source');
        $business_info = array(
            'wed_date' => intval($params['wed_date']),
            'source' => $source['website_hq'],
            'source_note' => DD($params , 'source_note'), //来源备注
            'hotel_name' => DD($params , 'shopper_name'), //酒店名称，针对酒店商机
            'mobile' => DD($params , 'mobile') ,
            'ordertype' => in_array($params['ordertype'] , $btype_vals) ? $params['ordertype'] : $btype['wed_plan'],
            'createtime' => DD($params , 'createtime' , time()) ,
            'username' => DD($params , 'username'),
            'sys_usertype' => $sysuser['sys_usertype_online'],
            'status' => $bstatus['newadd'],
            'tradeno' => $this->business->generateTradeNo(),
            'hmsr' => DD($params , 'baidu_hmsr'),
            'source_url' => DD($params , 'source_url'),
            'activity_desc' => DD($params, 'activity_desc'),
            'is_test' => isset($params['is_test']) && $params['is_test'] == 1 ? 1 : 0
        );

        if(isset($source[$source_key]))
        {
            $business_info['source'] = $source[$source_key];
        }

        $business_extra_info = array(
            'wed_place' => DD($params , 'wed_place'),
            'location' => DD($params , 'location'),
            'budget' => EE($wed_budget , $budget['not_sure']),
            'findtype' => intval(DD($params, 'findtype', $findtype['easywed_recommand'])),
            'moredesc' => DD($params , 'moredesc')
        );
        if($business_info['source_url'] == '/mobile/weddings/planerlanding'){//落地页的借用字段来添加信息
            $business_extra_info['moredesc'] = ($business_extra_info['moredesc']. "|婚礼地区:".DD($params , 'hotel_place')."|婚宴场地:".DD($params , 'place')."|来宾人数:".DD($params , 'guests')."|婚礼风格:".DD($params , 'style'));
        }

        if($params['shopper_uid'] > 0)
        {
            $business_extra_info['findtype'] = $findtype['people_self'];
            $business_extra_info['findnote'] = $params['shopper_uid'];
        }

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
                return success(array('id' => $bid));
            }
        }

        return failure('添加失败');
    }

    /**
     * 与主站同步商机的签约状态
     * @return mixed
     */
    public function contracted()
    {
        $params = $this->input->post();
        $param_sign = $params['sign'];

        unset($params['sign']);

        if(!ErpHash::validate($params , $param_sign))
        {
            return failure('签名错误');
        }

        $params = ew_filter_quote_html($params);
        $tradeno = $params['tradeno'];
        $shopper_uid = intval($params['shopper_uid']);

        if(empty($tradeno) || $shopper_uid <= 0)
        {
            return failure('交易号为空或商家id为空');
        }

        $binfo = $this->business->findByCondition(array('tradeno' => $tradeno));
        if($binfo['id'] > 0)
        {
            //获取交易状态
            list($tstatus , $tstatus_explan) = $this->business->getTradeStatus();

            //更新商机交易状态为已成单
            $this->business->updateByCondition(array('trade_status' => $tstatus['ordered'] , 'updatetime' => time()) , array('id' => $binfo['id']));
            //其它未签约商家全部置为已丢单
            $this->shopmap->updateByCondition(array('status' => Business_shop_map_model::STATUS_LOST) , array('bid' => $binfo['id']));
            //更新商家为已签约
            $this->shopmap->updateByCondition(array('status' => Business_shop_map_model::STATUS_SIGN) , array('bid' => $binfo['id'] , 'shop_id' => $shopper_uid));
            return success('更改成功');
        }
        else
        {
            return failure('商机信息不存在');
        }
    }
}