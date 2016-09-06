<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * author: easywed
 * createTime: 15/10/14 10:03
 * description:商机主表model.
 */

class Business_model extends MY_Model
{
    protected $_table = 'business';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 同步商机信息到主站
     * @param $bid 商机id
     * @param array $shoper_ids 商家id
     * @param array $sync_info 商机信息
     * @return int 1:成功，否则失败
     */
    public function syncToMaster($bid , array $shoper_ids , $sync_info = array())
    {
        $post_params = array();
        if($bid <= 0 || count($shoper_ids) <= 0)
        {
            return array('code' => -1, 'code_msg' => '商机或商家id为空');
        }
        $this->erp_conn->from($this->_table);
        $this->erp_conn->join('business_extra' , $this->_table . '.id = business_extra.bid');
        $this->erp_conn->where('bid' , $bid);
        $binfo = $this->erp_conn->get()->row_array();

        if(!$binfo)
        {
            return array('code' => -2, 'code_msg' => '商机不存在');
        }

        //'wed_date' => '婚礼日期'，'wed_place' => '婚礼场地'
        $required = array('source' => '商机来源' , 'ordertype' => '商机类型' , 'usertype' =>'客户类型' ,
            'username' => '客户名称' , 'mobile' => '客户手机' , 'location' => '婚礼地点' ,
            'budget' => '婚礼预算' , 'findtype' => '找商家方式');
        foreach($binfo as $key => $bval)
        {
            if(isset($sync_info[$key]))
            {
                $binfo[$key] = $sync_info[$key];
            }
        }

        foreach($required as $rkey => $rval)
        {
            if(!isset($binfo[$rkey]) || !$binfo[$rkey])
            {
                return array('code' => -3, 'code_msg' => $rval . ':不能为空');break;
            }
        }

        if(empty($binfo['wed_date']) && empty($binfo['weddate_note']))
        {
            return array('code' => -3, 'code_msg' => '婚礼日期或备注:不能为空');
        }

        if(empty($binfo['wed_place']) && empty($binfo['wed_place_area']))
        {
            return array('code' => -3, 'code_msg' => '婚礼场地或场地说明:不能为空');
        }

        //获取商机来源
        list($source , $source_explan) = $this->getBusinessSource();
        //获取商机类型
        list($btype , $btype_explan) = $this->getBusinessType();
        //获取婚礼预算
        list($budget , $budget_explan) = $this->getWedBudget();
        //获取婚宴类型
        list($wedtype , $wedtype_explan) = $this->getWedType();

        $source_map = array_flip($source);
        $btype_map = array_flip($btype);
        $budget_map = array_flip($budget);
        $wedtype_map = array_flip($wedtype);

        $post_params['source'] = $source_explan[$source_map[$binfo['source']]];
        $post_params['ordertype_alias'] = $btype_map[$binfo['ordertype']];
        $post_params['usertype'] = $binfo['usertype'];
        $post_params['nickname'] = $binfo['username'];
        $post_params['mobile'] = $binfo['mobile'];
        $post_params['wed_date_sure'] = empty($binfo['wed_date']) ? 0 : 1;
        $post_params['wed_date'] = empty($binfo['wed_date']) ? $binfo['weddate_note'] : date('Y-m-d' , $binfo['wed_date']);
        $post_params['wed_location'] = $binfo['location'];
        $post_params['wed_place'] = $binfo['wed_place'] ? $binfo['wed_place'] : $binfo['wed_place_area'];
        $post_params['budget'] = $budget_explan[$budget_map[$binfo['budget']]];
        $post_params['findtype'] = $binfo['findtype'];
        $post_params['shopper_uid_str'] = implode(',' , $shoper_ids);
        $post_params['tradeno'] = $binfo['tradeno'];
        $post_params['wed_type'] = $wedtype_explan[$wedtype_map[$binfo['wed_type']]];
        $post_params['reason'] = $binfo['status_note'];
        $post_params['wish_contract'] = $binfo['wish_contact'];
        $post_params['budget_note'] = $binfo['budget_note'];

        //新人顾问和运营
        $consultant = $this->erp_conn->where("id",$binfo['follower_uid'])->get("erp_sys_user")->result_array();
        $operation = $this->erp_conn->where("id",$binfo['operate_uid'])->get("erp_sys_user")->result_array();
        $post_params['consultant_name'] = isset($consultant[0]) ? $consultant[0]['username'] : "" ;
        $post_params['consultant_phone'] = isset($consultant[0]) ? $consultant[0]['mobile'] : "" ;
        $post_params['consultant_id'] = isset($consultant[0]) ? $consultant[0]['id'] : "" ;
        $post_params['operation_name'] = isset($operation[0]) ? $operation[0]['username'] : "" ;
        $post_params['operation_phone'] = isset($operation[0]) ? $operation[0]['mobile'] : "" ;
        $post_params['operation_id'] = isset($operation[0]) ? $operation[0]['id'] : "" ;

        $other_fields = array('guest_from' , 'guest_to' , 'desk_from' , 'desk_to' , 'price_from' ,
            'price_to' , 'tel' , 'weixin' , 'qq' , 'moredesc');
        foreach($other_fields as $oval)
        {
            $post_params[$oval] = $binfo[$oval];
        }
        $post_params['is_test'] = isset($binfo['is_test']) && $binfo['is_test'] == 1 ? 1 : 0;
        $re = $this->curl->post($this->config->item('ew_domain').'/erp/business/push', $post_params);
        $resp = json_decode($re , true);
        if($resp['result'] == 'succ')
        {
            $ew_uid = isset($resp['uid']) ? intval($resp['uid']) : 0;
            if($ew_uid > 0)
            {
                //更新商机的uid
                $this->updateByCondition(array('uid' => $ew_uid) , array('id' => $bid));
            }
            return array('code' => 1, 'code_msg' => '添加成功');
        }
        else
        {
            return array('code' => -4, 'code_msg' => $resp['msg']);
        }
    }

    /**
     * 同步丢单状态至主站
     * @param $tradeno 交易编号
     * @param $reason
     * @return bool
     */
    public function syncLostOrder($tradeno , $reason)
    {
        if(!$tradeno)return false;

        $post_params = array(
            'tradeno' => $tradeno,
            'reason' => $reason
        );
        $ret = $this->curl->post($this->config->item('ew_domain').'/erp/business/missing', $post_params);

        $resp = json_decode($ret , true);
        if($resp['result'] == 'succ')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 同步商机无效状态至主站
     * @param $tradeno
     * @param $reason
     * @return bool
     */
    public function syncMissOrder($tradeno , $reason)
    {
        if(!$tradeno)return false;

        $post_params = array(
            'tradeno' => $tradeno,
            'reason' => $reason
        );
        $ret = $this->curl->post($this->config->item('ew_domain').'/erp/business/invalid', $post_params);

        $resp = json_decode($ret , true);
        if($resp['result'] == 'succ')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 预处理数据
     * @param array $insert_data
     * @return array
     */
    public function prepareData($insert_data = array())
    {
        $prepare_data = array('uid' => 0, 'username' => '', 'userpart' => 0, 'usertype' => '',
            'source' => 0, 'source_note' => '', 'mobile' => '', 'tel' => '', 'weixin' => '', 'qq' => '',
            'other_contact' => '', 'hotel_name' => '', 'sys_usertype' => 0, 'sys_uid' => 0, 'follower_uid' => 0,
            'createtime' => time(), 'ordertime' => 0, 'ordertype' => 0, 'status' => 0, 'status_note' => '',
            'wed_date' => 0, 'tradeno' => '0' , 'hmsr' => '' , 'source_url' => '', 'is_test' => 0
        );

        if(count($insert_data) > 0)
        {
            $prepare_data = array_merge($prepare_data , $insert_data);
        }
        return $prepare_data;
    }

    /**
     * 获取商机列表
     * @param $condition 查询条件
     * @param $limit 分页
     * @param $order 排序
     * @param string $sel_fields 查询字段
     * @return array
     */
    public function findAllBusiness($condition, $limit ,$order = array(), $sel_fields = '*')
    {
        $query_data = $this->findAll($condition, $limit ,$order, $sel_fields = '*');
        $result = array();

        foreach($query_data as $key => $val)
        {
            $result[$val['id']] = $val;
        }
        return $result;
    }

    /**
     * 获取商机与商机获取列表
     * @param $condition
     * @param $limit
     * @param bool $get_nums
     * @param string $sel_fields
     * @return mixed
     */
    public function findBusinessJoinExtra($condition, $limit, $get_nums = false, $sel_fields = '*')
    {
        if($get_nums)
        {
            $this->erp_conn->from($this->_table);
            $this->erp_conn->join('business_extra' , $this->_table . '.id = business_extra.bid');
            foreach($condition as $k => $v)
            {
                if($k == "where_str")
                {
                    $this->erp_conn->where($v);
                }
                else if(is_array($v))
                {
                    $this->erp_conn->where_in($k , $v);
                }
                else
                {
                    $this->erp_conn->where($k , $v);
                }

            }
            return $this->erp_conn->count_all_results();
        }
        else
        {
            $this->erp_conn->select($sel_fields);
            $this->erp_conn->from($this->_table);
            $this->erp_conn->join('business_extra' , $this->_table . '.id = business_extra.bid');
            foreach($condition as $k => $v)
            {
                if($k == "where_str")
                {
                    $this->erp_conn->where($v);
                }
                else if(is_array($v))
                {
                    $this->erp_conn->where_in($k , $v);
                }
                else
                {
                    $this->erp_conn->where($k , $v);
                }

            }

            $this->erp_conn->order_by($this->_table . '.id' , 'desc');

            if(isset($limit['nums']) && $limit['nums'] > 0)
            {
                $this->erp_conn->limit($limit['nums'] , $limit['start']);
            }

            return $this->erp_conn->get()->result_array();
        }
    }

	/**
     * 获取单个商机
     * @param $condition 查询条件
     * @param string $sel_fields 查询字段
     * @return array
     */
    public function findRow($condition, $sel_fields = '*')
    {
        $result = $this->findByCondition($condition, $sel_fields = '*');
        return $result;
    }

    /**
     * 获取商机状态
     * @return array
     */
    public function getBusinessStatus()
    {
       /* $business_status = array(
            'newadd' => 1, //新增
            'follow_noanswer' => 2, //跟进中-无法接听
            'follow_next' => 3, //跟进中-约定下次跟进
            'garbage_invalid_info' => 4, //废商机-无效信息
            'garbage_three_times' => 5, //废商机-3次以上无法接听
            'parted' => 6, //已分单
        );

        $status_explan = array(
            'newadd' => '新增',
            'follow_noanswer' => '无法接听',
            'follow_next' => '约定下次跟进',
            'garbage_invalid_info' => '无效信息',
            'garbage_three_times' => '3次以上无法接听',
            'parted' => '已分单',
        );
*/

        /*
         * 新更新的商机状态值
         */
         $business_status = array(
             'newadd' => 1, //新增
             'follow_next' => 3, //跟进中
             'parted' => 6, //已分单4进2
             'build' => 7, //已建单
             'allocate' => 8, //已分配顾问
             'follow_noanswer' => 2, //无效信息-拒接或关机
             'garbage_invalid_info' => 4, //无效信息-电话错误
             'garbage_three_times' => 5, //无效信息-无需求
             'garbage_other' => 9, //无效信息-其他
             'garbage_repeat' => 10, //无效信息-重复提交
             '3days_ago' => 15, //三天以上跟进中-系统设置
             'parted_n_4' => 20, //已分单n进4
         );

         $status_explan = array(
             'newadd' => '新增',
             'follow_noanswer' => '拒接或关机',
             'follow_next' => '跟进中',
             'garbage_invalid_info' => '电话错误',
             'garbage_three_times' => '无需求',
             'parted' => '已分单4进2',
             'build' => '已建单',
             'allocate' => '分配顾问',
             'garbage_other' => '无效-其他',
             'garbage_repeat' => '无效-重复',
             '3days_ago' => '三天以上跟进中-系统设置',
             'parted_n_4' => '已分单n进4',
         );


        return array($business_status , $status_explan);
    }

    /**
     * 获取商机来源
     * @return array
     */
    public function getBusinessSource()
    {
        $business_source = array(
            'website_hq' => 1, //婚庆需求(网站端)
            'mobile_hq' => 2, //婚庆需求(手机端)
            'wxaccount_hq' => 4, //婚庆需求(微信公众号)
            'website_hy' => 21, //婚宴需求(网站端)
            'mobile_hy' => 34, // 婚宴需求(手机端)
            'website_hq_page' => 8, //婚庆落地页(网站端)
            'mobile_hq_page' => 9, //婚庆落地页(手机端)
            'website_hy_page' => 6, //婚宴落地页(网站端)
            'mobile_hy_page' => 7, //婚宴落地页(手机端)
            'website_hotel_search' => 10, //酒店查询(网站端)
            'mobile_hotel_search' => 22, //酒店查询(手机端)
            'website_register' => 11, //新人注册(网站端)
            'mobile_register' => 3, //新人注册(手机端)
            'hotel_visit' => 5, //酒店陌生拜访
            'mike' => 12, //麦客
            'callcenter' => 13, //呼叫中心
            'internal_rec' => 14, //内部推荐
            'live800' => 15, //Live800
            'weibo' => 16, //微博
            'youzan' => 17, //有赞微商城
            'channel_spread' => 18, //渠道推广
            'other' => 19, //其它
            'pricelanding' => 23, //估算报价落地页(手机端)
            'planerlanding' => 24, //'预约策划师落地页（手机端）'
            'photolanding' => 25,//结婚照活动落地页（网站端）
            'companylanding' => 26,//企业活动落地页（手机端）
            'dresslanding' => 27,//婚纱落地页（网站端）
            'sanyalanding' => 28,//三亚婚礼落地页（网站端）
            'drephotolanding' => 29,//婚纱摄影落地页（网站端）
            'xnshalonglanding' =>31, //新娘沙龙落地页（手机端）
            '53kf' => 30,//53客服
            'hunbohui' => 32,//婚博会
			'marphotolanding' => 33,//结婚照活动落地页（手机端）
            'mobile_single_demand' => 35, // 单项需求(手机端)
            'eventplan_weixin' => 36, //活动策划(微信)
            'eventplan_weibo' => 37, //活动策划(微博)
            'eventplan_baidu' => 38, //活动策划(百度)
            'qudaobao' => 39, //渠道宝
			'sayalanding'=>40,//三亚婚礼落地页（手机端）
            'weddingofferlanding'=>41,   //智能婚礼报价落地页(网站端)
            'm_hotel_search' => 42, //酒店查询（M端）
            'm_weddingofferlanding' => 43, //智能婚礼报价（M端）
            'store_booking' => 44, // 策划师店铺预定
            'reserve' => 45, // 预约到店
            'm_bjplannerlanding' => 46, //北京策划落地页（M端）
        );

        $status_explan = array(
            'website_hq' => '婚庆需求(网站端)',
            'mobile_hq' => '婚庆需求(手机端)',
            'wxaccount_hq' => '婚庆需求(微信公众号)',
            'website_hy' => '婚宴需求(网站端)',
            'mobile_hy' => '婚宴需求(手机端)',
            'website_hq_page' => '婚庆落地页(网站端)',
            'mobile_hq_page' => '婚庆落地页(手机端)',
            'website_hy_page' => '婚宴落地页(网站端)',
            'mobile_hy_page' => '婚宴落地页(手机端)',
            'website_hotel_search' => '酒店查询(网站端)',
            'mobile_hotel_search' => '酒店查询(手机端)',
            'website_register' => '新人注册(网站端)',
            'mobile_register' => '新人注册(手机端)',
            'hotel_visit' => '酒店陌生拜访',
            'mike' => '麦客',
            'callcenter' => '呼叫中心',
            'internal_rec' => '内部推荐',
            'live800' => 'Live800',
            'weibo' => '微博',
            'youzan' => '有赞微商城',
            'channel_spread' => '渠道推广',
            'other' => '其它',
            'pricelanding' => '估算报价落地页(手机端)',
            'planerlanding' => '预约策划师落地页（手机端）',
            'photolanding' => '结婚照活动落地页（网站端）',
            'companylanding' => '企业活动落地页（手机端）',
            'dresslanding' => '婚纱落地页（网站端）',
            'sanyalanding' => '三亚婚礼落地页（网站端）',
            'drephotolanding' => '婚纱摄影落地页（网站端）',
            'xnshalonglanding' => '新娘沙龙落地页（手机端）',
            '53kf' => '53客服',
            'hunbohui' => '婚博会',
			'marphotolanding'=> '结婚照活动落地页（手机端）',
            'mobile_single_demand' => '单项需求(手机端)',
            'eventplan_weixin' => '活动策划(微信)',
            'eventplan_weibo' => '活动策划(微博)',
            'eventplan_baidu' => '活动策划(百度)',
            'qudaobao' => '渠道宝',
			'sayalanding'=>'三亚婚礼落地页（手机端）',
            'weddingofferlanding'=>'智能婚礼报价落地页(网站端)',
            'm_hotel_search' => '酒店查询（M端）',
            'm_weddingofferlanding' => '智能婚礼报价（M端）',
            'store_booking' => '店铺预定',
            'reserve' => '预约到店',
            'm_bjplannerlanding' => '北京策划落地页（M端）',
        );

        return array($business_source , $status_explan);
    }

    /**
     * 获取客户身份
     * @return array
     */
    public function getCustomerIdentify()
    {
        $customer_identify = array(
            'bridegroom' => 1, //新郎
            'bride' => 2, //新娘
            'bridegroom_family' => 3, //新郎家人
            'bride_family' => 4, //新娘家人
            'bridegroom_friend' => 5, //新郎朋友
            'bride_friend' => 6, //新娘朋友
            'other' => 7 //其它
        );

        $status_explan = array(
            'bridegroom' => '新郎',
            'bride' => '新娘',
            'bridegroom_family' => '新郎家人',
            'bride_family' => '新娘家人',
            'bridegroom_friend' => '新郎朋友',
            'bride_friend' => '新娘朋友',
            'other' => '其它'
        );

        return array($customer_identify , $status_explan);
    }

    /**
     * 客户类型
     */
    public function getUserType()
    {
        //return array('A+', 'A', 'B+', 'B', 'C', 'D', 'E');
        return array('C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C5和C6','C7', 'C8');
    }

    /**
     * 获取婚礼预算
     * @return array
     */
    public function getWedBudget()
    {
        $wed_budget = array(
            'lg_2' => 1, //2万以内
            '2_4' => 2, //2-4万
            '4_7' => 3, //4-7万
            '7_10' => 4, //7-10万
            'gt_10' => 5, //10万以上
            'not_sure' => 6, //不确定
        );

        $status_explan = array(
            'lg_2' => '2万以下',
            '2_4' => '2-4万',
            '4_7' => '4-7万',
            '7_10' => '7-10万',
            'gt_10' => '10万以上',
            'not_sure' => '不确定'
        );

        return array($wed_budget , $status_explan);
    }

    /**
     * 获取商机类型
     * @return array
     */
    public function getBusinessType()
    {
        $business_type = array(
            'wed_plan' => 1, //婚礼策划
            'wed_place' => 2, //婚宴场地
            'plan_place' => 3, //场地+策划
        );

        $status_explan = array(
            'wed_plan' => '婚礼策划',
            'wed_place' => '婚宴酒店',
            'plan_place' => '婚礼策划+婚宴酒店'
        );

        return array($business_type , $status_explan);
    }

    /**
     * 获取找商家方式
     * @return array
     */
    public function getFindShopType()
    {
        $find_shop_type = array(
            'easywed_recommand' => 1, //易结推荐
            'people_self' => 2, //新人自选
        );

        $status_explan = array(
            'easywed_recommand' => '易结推荐',
            'people_self' => '新人自选'
        );

        return array($find_shop_type , $status_explan);
    }

    /**
     * 获取商机录入人员信息
     * @return array
     */
    public function getWBusinessWriter()
    {
        $writer = array(
            'sys_usertype_online' => 0, // 线上提交
            'sys_usertype_service' => 1, // 客服
            'sys_usertype_hotel' => 2, // 酒店运营
            'sys_usertype_adviser' => 3 // 婚礼顾问
        );

        $writer_explan = array(
            'sys_usertype_online' => '线上',
            'sys_usertype_service' => '客服',
            'sys_usertype_hotel' => '酒店运营',
            'sys_usertype_adviser' => '婚礼顾问'
        );
        return array($writer, $writer_explan);
    }

    /**
     * 婚宴类型
     * @return array
     */
    public function getWedType()
    {
        $wedtype = array(
            'type_no' => 0, // 未确定
            'type_noon' => 1, // 午宴
            'type_night' => 2 // 晚宴
        );
        $wedtype_explan = array(
            'type_no' => '未确定',
            'type_noon' => '午宴',
            'type_night' => '晚宴'
        );
        return array($wedtype, $wedtype_explan);
    }

    /**
     * 交易状态
     * @return array
     */
    public function getTradeStatus()
    {
        $tradestatus = array(
            'faced' => 1, //已见面
            'no_faced' => 2, //未见面
            'ordered' => 3, //已成单
            'discard' => 4, //已丢单
            'invalid' => 5, //无效订单
        );

        $tradestatus_explan = array(
            'faced' => '已见面',
            'no_faced' => '未见面',
            'ordered' => '已成单',
            'discard' => '已丢单',
            'invalid' => '无效订单'
        );
        return array($tradestatus , $tradestatus_explan);
    }

    /**
     * 获取商机列表
     */
    public function conditions(array $cond)
    {
        // 常规where处理
        if(!empty($cond['autowhere']) && is_array($cond['autowhere']))
        {
            foreach ($cond['autowhere'] as $field => $val)
            {
                if($field == 'where_str')
                {
                    $this->erp_conn->where($val);
                }
                else if(is_array($val))
                {
                    $this->erp_conn->where_in($field, $val);
                }
                else
                {
                    $this->erp_conn->where($field, $val);
                }
            }
        }

        // 手机号数量
        if(!empty($cond['mobile_count']) && is_array($cond['mobile_count']))
        {
            $this->erp_conn->where_in('mobile', $cond['mobile_count'])->group_by('mobile');
        }       

        // 提交日期处理
        if(!empty($cond['creat_time_start']) || !empty($cond['creat_time_end']))
        {
            $start = !empty($cond['creat_time_start']) ? strtotime($cond['creat_time_start']) : strtotime(date('Ymd'));
            $end = !empty($cond['creat_time_end']) ? strtotime($cond['creat_time_end']) : strtotime(date('Ymd') . '235959');
            if($start > $end)
            {
                $this->erp_conn->where("createtime BETWEEN {$end} AND {$start}");
            }
            else
            {
                $this->erp_conn->where("createtime BETWEEN {$start} AND {$end}");
            }
        }

        // 婚礼日期日期处理
        if(!empty($cond['wed_time_start']) || !empty($cond['wed_time_end']))
        {
            $start = !empty($cond['wed_time_start']) ? strtotime($cond['wed_time_start']) : strtotime(date('Ymd'));
            $end = !empty($cond['wed_time_end']) ? strtotime($cond['wed_time_end'] . ' 23:59:59') : strtotime(date('Ymd') . '235959');
            if($start > $end)
            {
                $this->erp_conn->where("wed_date BETWEEN {$end} AND {$start}");
            }
            else
            {
                $this->erp_conn->where("wed_date BETWEEN {$start} AND {$end}");
            }
        }

        // 分配顾问日期处理
        if(!empty($cond['adviser_time_start']) || !empty($cond['adviser_time_end']))
        {
            $start = !empty($cond['adviser_time_start']) ? strtotime($cond['adviser_time_start']) : strtotime(date('Ymd'));
            $end = !empty($cond['adviser_time_end']) ? strtotime($cond['adviser_time_end'] . ' 23:59:59') : strtotime(date('Ymd') . '235959');
            if($start > $end)
            {
                $this->erp_conn->where("advisertime BETWEEN {$end} AND {$start}");
            }
            else
            {
                $this->erp_conn->where("advisertime BETWEEN {$start} AND {$end}");
            }
        }
        // 分配运营日期处理
        if(!empty($cond['operate_time_start']) || !empty($cond['operate_time_end']))
        {
            $start = !empty($cond['operate_time_start']) ? strtotime($cond['operate_time_start']) : strtotime(date('Ymd'));
            $end = !empty($cond['operate_time_end']) ? strtotime($cond['operate_time_end'] . ' 23:59:59') : strtotime(date('Ymd') . '235959');
            if($start > $end)
            {
                $this->erp_conn->where("operatetime BETWEEN {$end} AND {$start}");
            }
            else
            {
                $this->erp_conn->where("operatetime BETWEEN {$start} AND {$end}");

            }
        }
    }

    /**
     * 生成交易号
     * @return string
     */
    public function generateTradeNo()
    {
        $insert_id = $this->add($this->prepareData(array('tradeno' => time())));
        if($insert_id > 0)
        {
            $this->erp_conn->where('id' , $insert_id)->delete($this->_table);
        }

        $number = date('ym').str_pad($insert_id , 7 , 0 , STR_PAD_LEFT);
        return $number;
    }

    /**
     * 获取提交数量
     */
    public function getMobileNum(array $mobile)
    {

    }

    /**
     * 转换商机id为年+月+7位数字(包含$bid)
     * @param $bid 商机id
     * @param bool $parse 为true时就反转回正常的$bid
     * @return string
     */
    public function formatBid($bid , $createtime = 0 , $parse = false)
    {
        if(!$parse)
        {
            return date('ym' , $createtime) . str_pad($bid , 7 , 0 , STR_PAD_LEFT);
        }
        else
        {
            return ltrim(substr($bid , 4 , 11) , '0');
        }
    }

    /**
     * 获取酒店录入信息的用户
     * @param array $source
     * @return array
     */
    public function getHotelUsers($source = array())
    {
        if(count($source) <= 0)return array();
        return $this->erp_conn->query('SELECT count(*),sys_uid FROM '. $this->erp_conn->dbprefix($this->_table) .' where source in('. implode(',' , $source) .') group by sys_uid')->result_array();
    }

}
