<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * author: easywed
 * createTime: 15/10/14 10:03
 * description:商机主表model.
 */

class Sem_model extends MY_Model
{
    protected $_table = 'business';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 页面映射
     */
    public function getPageName($page_path)
    {
        $page = array(
            '/hotel/landing' => 'PC婚宴落地页V1',
            '/mobile/hotel/landing' => 'H5婚宴落地页V1',
            '/mobile/weddings/landing' => 'H5婚庆落地页V1',
            '/weddings/landing' => 'PC婚庆落地页V1',
            '/mobile/weddings/newlanding' =>  'H5婚庆落地页V2',
            '/weddings/newlanding' => 'PC婚庆落地页V1',
            '/hotel/landing-two' => 'PC婚宴落地页V2'
        );
        return isset($page[$page_path]) ? $page[$page_path] : '';
    }

    /**
     * 获取商机状态
     * @return array
     */
    public function getBusinessStatus()
    {
        $business_status = array(
            'newadd' => 1, //新增
            'follow_noanswer' => 2, //跟进中-无法接听
            'follow_next' => 3, //跟进中-约定下次跟进
            'garbage_invalid_info' => 4, //废商机-无效信息
            'garbage_three_times' => 5, //废商机-3次以上无法接听
            'parted' => 6 //已分单
        );

        $status_explan = array(
            'newadd' => '新增',
            'follow_noanswer' => '无法接听',
            'follow_next' => '约定下次跟进',
            'garbage_invalid_info' => '无效信息',
            'garbage_three_times' => '3次以上无法接听',
            'parted' => '已分单'
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
            'web_hq_page' => 8, //婚庆落地页(网站端)
            'mobile_hq_page' => 9, //婚庆落地页(手机端)
            'web_hy_page' => 6, //婚宴落地页(网站端)
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
        );

        $status_explan = array(
            'website_hq' => '婚庆需求(网站端)',
            'mobile_hq' => '婚庆需求(手机端)',
            'wxaccount_hq' => '婚庆需求(微信公众号)',
            'website_hy' => '婚宴需求(网站端)',
            'web_hq_page' => '婚庆落地页(网站端)',
            'mobile_hq_page' => '婚庆落地页(手机端)',
            'web_hy_page' => '婚宴落地页(网站端)',
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
        );

        return array($business_source , $status_explan);
    }


    /**
     * 获取渠道
     */
    public function getChannel()
    {
        return array(
            'baidu' => '百度', 'sogou' => '搜狗', '360' => 360 
        );
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
                if(is_array($val))
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
            $start = !empty($params['wed_time_start']) ? strtotime($cond['wed_time_start']) : strtotime(date('Ymd'));
            $end = !empty($cond['wed_time_end']) ? strtotime($cond['wed_time_end']) : strtotime(date('Ymd') . '235959');
            if($start > $end)
            {
                $this->erp_conn->where("wed_date BETWEEN {$end} AND {$start}");
            }
            else
            {
                $this->erp_conn->where("wed_date BETWEEN {$start} AND {$end}");
            }
        }

        // 提交日期
        if(!empty($cond['add_date']))
        {
            $start = strtotime($cond['add_date']);
            $end = strtotime($cond['add_date'] . ' 23:59:59');
            $this->erp_conn->where("createtime BETWEEN {$start} AND {$end}");
        }

        // 渠道
        if(!empty($cond['channel']))
        {
            $this->erp_conn->like('hmsr', $cond['channel'], 'after');
        }

        if(empty($cond['hmsr']))
        {
            $this->erp_conn->where('hmsr > ', '');
        }
        else
        {
            $this->erp_conn->like('hmsr', $cond['hmsr']);
        }
        if(!empty($cond['source_url']))
        {
            $this->erp_conn->where('source_url', $cond['source_url']);
        }
    }
}