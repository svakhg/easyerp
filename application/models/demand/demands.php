<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Api_newly
 * erp  业务处理类 逐步脱离从主站CURL方式获取数据
 */

class Demands extends MY_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ew/demand_content_model','content');
        $this->load->model('ew/demand_qa_model','qa');
    }

    /**
     * 返回规定的格式
     * @param mixed $data
     * @param bool $status
     * @return array $result
     */
    public static function returnArray($data, $status = 1)
    {
        $result = array();

        if($status == 1){

            $result = array('result' => 'succ', 'info' => $data);
        }elseif($status == 0){

            $result = array('result' => 'fail', 'info' => $data);
        }
        return $result;
    }

    /*
     * 查询需求列表
     * $condition:array()条件列表
     */
    public function DemandList($param){

        $pagesize = isset($param['pagesize']) ? $param['pagesize'] : 10;
        $page = isset($param['page']) && $param['page'] > 0 ? $param['page'] : 1;

        $offset = $pagesize * ($page - 1);
        if($offset){
            $limit = "$offset,$pagesize";
        }else{
            $limit = $pagesize;
        }

        $sql_base = "select * from ew_demand_content as content where 1=1";

        $sql_status = '';
        if(isset($param['status']))
        {
            //需求状态
            if($param['status'] == 'all'){

                $sql_status = " and (content.status = 0 or content.status = 4)";
            }
            else
            {
                $sql_status = " and content.status = ".intval($param['status']);
            }
        }

        //新人顾问
        $sql_counselor = '';
        if(isset($param['counselor_uid']))
        {
            $sql_counselor = " and content.counselor_uid = ".$param['counselor_uid'];
        }

        //来源渠道
        $sql_channel = '';
        if(isset($param['channel']))
        {
            $sql_channel = " and content.channel = ".$param['channel'];
        }

        //客户来源
        $sql_cli_source = '';
        if(isset($param['cli_source']))
        {
            $sql_cli_source = " and content.cli_source = '".$param['cli_source']."'";
        }

        //找商家方式指定商家or招投标
        $sql_mode = '';
        if(isset($param['mode']))
        {
            $sql_mode = " and content.mode = ".$param['mode'];
        }

        //交易提示ids
        $sql_remander_id = '';
        if(isset($param['remander_id']))
        {
            $sql_remander_id = " and content.remander_id = ".$param['remander_id'];
        }

        //暂时没用
        $sql_type = '';
        if(isset($param['type']))
        {
            $sql_type = " and content.type = ".$param['type'];
        }

        //添加时间筛选
        $sql_add_time = '';
        if(isset($param['add_from']) && $param['add_from']!= '')
        {
            if(isset($param['add_to']) && $param['add_to']!= '')
            {
                $sql_add_time = " and content.create_time >= '".$param['add_from']."' and content.create_time < '".$param['add_to']."'";
            }
            else
            {
                $sql_add_time = " and content.create_time >= '".$param['add_from']."'";
            }
        }
        elseif(isset($param['add_to']) && $param['add_to']!= '')
        {
            $sql_add_time = " and content.create_time < '".$param['add_to']."'";
        }

        //婚期时间筛选
        $sql_wed_time = '';
        if(isset($param['wed_from']) && $param['wed_from']!= '')
        {
            if(isset($param['wed_to']) && $param['wed_to']!= '')
            {
                $sql_wed_time = " and content.wed_date >= '".$param['wed_from']."' and content.wed_date <= '".date('Y-m-d',(strtotime($param['wed_to'])+3600*24))."'";
            }
            else
            {
                $sql_wed_time = " and content.wed_date >= '".$param['wed_from']."'";
            }
        }
        elseif(isset($param['wed_to']) && $param['wed_to']!= '')
        {
            $sql_wed_time = " and content.wed_date <= '".date('Y-m-d',(strtotime($param['wed_to'])+3600*24))."'";
        }

        //自定义筛选
        $sql_condition = '';
        if((isset($param['condition']) && $param['condition'] != '') && (isset($param['condition_text']) && $param['condition_text'] != ''))
        {
            $sql_condition = " and content.`".$param['condition']."` like '%".$param['condition_text']."%'";
        }

        //婚礼地点
        $sql_wed_location = '';
        if(isset($param['wed_location']))
        {
            $location_arr = explode(',',$param['wed_location']);

            $location_str = '';
            if($location_arr[1] == ''){

                $location_str = $location_arr[0].',%';
            }
            elseif($location_arr[2] == '')
            {
                $location_str = $location_arr[0].','.$location_arr[1].',%';
            }
            else
            {
                $location_str = $param['wed_location'];
            }

            $sql_wed_location = " and content.wed_location like '".$location_str."'";
        }

        //客户标签id
        $sql_cli_tag = '';
        if(isset($param['cli_tag']) && $param['cli_tag'] != '')
        {
            $sql_cli_tag = " and content.`cli_tag` like '%".$param['cli_tag']."%'";
        }

        //类型是单项；一站式;
        $sql_shopper_alias = '';
        if(isset($param['shopper_alias']) && $param['shopper_alias'] != '')
        {
            $sql_shopper_alias = " and content.shopper_alias = '".$param['shopper_alias']."'";
        }

        //类型和预算 根据一站式或是单项 判断预算的类型 ；一站式的预算在content表里面，然而单项的预算在qa表里，只能用这种比较郁闷的方式处理了
        $sql_budget = '';
        if(isset($param['shopper_alias']) && $param['shopper_alias'] != '')
        {
            if(isset($param['budget']) && $param['budget'] != '')
            {
                if($param['shopper_alias'] == 'wedplanners')
                {
                    $sql_budget = " and content.budget = '".$param['budget']."'";
                }
                else
                {
                    $sql_budget = " and content.id in (select content_id from ew_demand_qa where shopper_alias = '". $param['shopper_alias']."' and answer = '".$param['budget']."')";
                }
            }
        }

        //商家投标中/初选中标
        $sql_bid = '';
//        $param['bidding'] = 'bidding';
        if(isset($param['bidding']) && $param['bidding'] != '')
        {
            if($param['bidding'] == 'bidding') //商家投标中 11,21,31
            {
                $sql_bid = " and content.id in (select distinct content_id from ew_demand_order where status = 11 union select distinct content_id from ew_demand_order where status = 21 union select distinct content_id from ew_demand_order where status = 31) ";
            }
            elseif($param['bidding'] == 'primary')//初选中标 41,46
            {
                $sql_bid = " and content.id in (select distinct content_id from ew_demand_order where status = 41 union select distinct content_id from ew_demand_order where status = 46) ";
            }
        }

        //投标完成 51， 订单完成 61
        $sql_com = '';
//        $param['inbidding_com'] = 'complete';
        if(isset($param['inbidding_com']) && $param['inbidding_com'] == 'complete')
        {
            $sql_com = " and content.id in (select distinct content_id from ew_demand_order where status = 51 union select distinct content_id from ew_demand_order where status = 61) ";
        }

        $sql_conn = $sql_base . $sql_status . $sql_counselor . $sql_channel . $sql_cli_source . $sql_mode . $sql_remander_id . $sql_add_time . $sql_wed_time . $sql_condition . $sql_wed_location . $sql_cli_tag .$sql_shopper_alias. $sql_budget . $sql_bid . $sql_com;

        $sql = $sql_conn . " order by content.id desc limit $limit";

        $total = $this->ew_conn->query($sql_conn)->num_rows();

        $list = $this->ew_conn->query($sql)->result_array();

        // print_r($this->ew_conn->last_query());exit;

        $cli_source_arr = $this->getCliSource();//客户来源文案
        //获得当前需求对应订单时间的节点
        $default_time = '0000-00-00 00:00:00';
        foreach($list as &$v){
            $v['channel'] = $v['channel'] == '' ? 0 : $v['channel'];
            $order_info = $this->ew_conn->where('content_id',$v['id'])->where('status <>',99)->order_by('status','desc')->get('demand_order')->row_array();
            $v['time_11'] = isset($order_info['time_11']) ? $order_info['time_11'] : $default_time;
            $v['time_21'] = isset($order_info['time_21']) ? $order_info['time_21'] : $default_time;
            $v['time_31'] = isset($order_info['time_31']) ? $order_info['time_31'] : $default_time;
            $v['time_41'] = isset($order_info['time_41']) ? $order_info['time_41'] : $default_time;
            $v['time_46'] = isset($order_info['time_46']) ? $order_info['time_46'] : $default_time;
            $v['time_51'] = isset($order_info['time_51']) ? $order_info['time_51'] : $default_time;
            $v['time_61'] = isset($order_info['time_61']) ? $order_info['time_61'] : $default_time;
            $v['time_99'] = isset($order_info['time_99']) ? $order_info['time_99'] : $default_time;

            //客户来源索引转换为文字
            $v['cli_source_text'] = isset($cli_source_arr[$v['cli_source']]) ? $cli_source_arr[$v['cli_source']] : "";
        }
        $infos['total']= $total;
        $infos['rows']= $list;

         return $infos;
    }

    /**
     * 获取需求的详情
     * $param ['id']
     * $param ['order_status'] 11,21,31
     */
    public function demand_detail($param)
    {
        if(! isset($param['id']))
        {
            return array('massage' => '需求id不能为空', 'code' => 5);
        }

        $data['base'] = $this->content->getContentById($param['id']);
        $question = $this->qa->getQasByContentId($param['id']);

        foreach($question as $k => $v){
            $data['question'][$v['shopper_alias']][] = $v;
        }

        $obj = $this->ew_conn->from('demand_order');

        if(!empty($param['order_status'])){

            $obj->where_in('demand_order.status',$param['order_status']);
        }

        $page = ! empty($param['page']) ? $param['page'] : 1;
        $pagesize = ! empty($param['pagesize']) ? $param['pagesize'] : 10;

        $offset = ($page -1)*$pagesize;

        $data['order'] = $obj->join('user_shopers','demand_order.shopper_user_id = user_shopers.uid')
            ->join('users','demand_order.shopper_user_id = users.uid')
            ->select('demand_order.*,users.nickname,users.phone,user_shopers.address,user_shopers.aboutme_detail,user_shopers.price,user_shopers.studio_name')
            ->where('content_id', $param['id'])
            ->offset($offset)->limit($pagesize)
            ->get()->result_array();

        return $data;
    }

    /**
     * 一站式修改执行
     * @param $inputs
     */
    public function demand_update_exe($inputs){

        $base_arr = json_decode($inputs["baseinfo"], TRUE);
        $demand_id = $base_arr[0]['demand_id'];
        $params['demand_id'] = $demand_id;

        $base = array();
        @array_walk($base_arr, function($item) use(&$base){
            $base = array_merge_recursive($base, $item);
        });
        if((!isset($base['mode']) || empty($base['mode']))&&$inputs['submitype']==1){
            return self::returnArray('请选择招投标或是指定商家',0);
        }

        $wed_info_arr = json_decode($inputs["wedbaseinfo"], TRUE);
        $wed_info = array();
        @array_walk($wed_info_arr, function($item) use(&$wed_info){
            $wed_info = array_merge_recursive($wed_info, $item);
        });
        $wedplanner_arr = json_decode($inputs["onestation"], TRUE);
        $wedplanner = array();
        @array_walk($wedplanner_arr, function($item) use(&$wedplanner){
            $wedplanner = array_merge_recursive($wedplanner, $item);
        });
        //客户来源
        $cli_source = isset($base["cli_source"]) ? $base["cli_source"] : 0;
        //获知渠道
        $channel = isset($base["channel"]) ? $base["channel"] : 0;

        //性别
        $sex = isset($base["cli_gender"]) ? $base["cli_gender"] : 0;
        switch ((int)$sex)
        {
            case 1:$cli_gender = "男";break;
            case 2:$cli_gender = "女";break;
            default:$cli_gender = "";break;
        }
        //学历
        $cli_edu_detail = isset($base["cli_edu"]) ? $base["cli_edu"] : 0;

        //民族
        $nation = isset($base["cli_nation"]) ? $base["cli_nation"] : 0;
        $nation_info = $this->erp_conn->where("id", $nation)->get('erp_nation')->row_array();
        $cli_nation = $nation_info ? $nation_info["nation"] : "";
        //客户通讯地址
        $cli_country = isset($base["cli_country"]) ? $base["cli_country"] : 0;
        $cli_province = isset($base["cli_province"]) ? $base["cli_province"] : 0;
        $cli_city = isset($base["cli_city"]) ? $base["cli_city"] : 0;
        $cli_location = $cli_country . ',' . $cli_province . ',' . $cli_city;
        //客户标签
        $tag_id_str = isset($base["tag"]) ? $base["tag"] : "";
        $tag_id_arr = explode(",", $tag_id_str);;
        $cli_tag_arr = array();
        foreach ($tag_id_arr as $tag_id)
        {
            $tag_info = $this->erp_conn->where("id", $tag_id)->get("erp_client_tag")->row_array();
            if($tag_info)
            {
                $cli_tag_arr[] = $tag_info["tag_name"];
            }
        }
        $cli_tag = (!empty($cli_tag_arr)) ? implode('||', $cli_tag_arr) : "";
        $cli_tag = $tag_id_str;
        //婚礼日期
        $wed_date_sure = isset($wed_info["wed_date_sure"]) ? $wed_info["wed_date_sure"] : 0;
        if($wed_date_sure == 1)
        {
            if(!isset($wed_info["wed_date"]) || empty($wed_info["wed_date"])&&$inputs['submitype']==1){
                return self::returnArray('请填写婚礼日期',0);
            }
            $wed_date = $wed_info["wed_date"];
        }else
        {
            $wed_date = isset($wed_info["wed_date_notsure"]) ? $wed_info["wed_date_notsure"] : "";
        }
        //婚礼地点
        $wed_country = isset($wed_info["wed_country"]) ? $wed_info["wed_country"] : 0;
        $wed_province = isset($wed_info["wed_province"]) ? $wed_info["wed_province"] : 0;
        $wed_city = isset($wed_info["wed_city"]) ? $wed_info["wed_city"] : 0;
        $wed_location = $wed_country . ',' . $wed_province . ',' . $wed_city;

        //婚礼色系
        $color = isset($wedplanner["color"]) ? $wedplanner["color"] : array();
        if(!is_array($color))
        {
            $color = array($color);
        }
        $color_detail = implode("||", $color);

        //婚礼形容词
        $ideal = isset($wedplanner["ideal"]) ? $wedplanner["ideal"] : array();
        if(!is_array($ideal))
        {
            $ideal = array($ideal);
        }
        $ideal_detail = implode("||", $ideal);
        //希望重点投入
        $emphasis = isset($wedplanner["emphasis"]) ? $wedplanner["emphasis"] : array();
        if(!is_array($emphasis))
        {
            $emphasis = array($emphasis);
        }
        $emphasis_detail = implode("||", $emphasis);
        //婚礼案例
        $opus = isset($wedplanner["opus"]) ? $wedplanner["opus"] : array();
        if(!is_array($opus))
        {
            $opus = array($opus);
        }
        $opus_detail = implode("||", $opus);

        //期望联系方式
        $way = isset($base["cli_hope_contect_way"]) ? $base["cli_hope_contect_way"] : array();
        if(!is_array($way))
        {
            $way = array($way);
        }
        $cli_way = implode(",", $way);
        $cli_hope_contect_way = rtrim($cli_way, ",");
        //基本信息和基础信息
        $params['content'] = array(
//            "mode" => isset($base["mode"]) ? $base["mode"] : 0,
            "counselor_uid" => isset($base["counselor_uid"]) ? $base["counselor_uid"] : 0,
            "cli_name" => isset($base["cli_name"]) ? $base["cli_name"] : "",
//            "cli_source" => $cli_source,
//            "channel" => $channel,
            "cli_gender" => $cli_gender,
            "cli_birth" => isset($base["cli_birth"]) ? $base["cli_birth"] : "",
            "cli_edu" => $cli_edu_detail,
//            "cli_mobile" => isset($base["cli_mobile"]) ? $base["cli_mobile"] : "",
            "cli_tel" => isset($base["cli_tel"]) ? $base["cli_tel"] : "",
            "cli_nation" => $cli_nation,
            "cli_weixin" => isset($base["cli_weixin"]) ? $base["cli_weixin"] : "",
            "cli_qq" => isset($base["cli_qq"]) ? $base["cli_qq"] : "",
            "cli_weibo" => isset($base["cli_weibo"]) ? $base["cli_weibo"] : "",
            "cli_postcode" => isset($base["cli_postcode"]) ? $base["cli_postcode"] : "",
            "cli_email" => isset($base["cli_email"]) ? $base["cli_email"] : "",
            "cli_othercontect" => isset($base["cli_othercontect"]) ? $base["cli_othercontect"] : "",
            "cli_location" => $cli_location,
            "cli_address" => isset($base["cli_address"]) ? $base["cli_address"] : "",
            "cli_hope_contect_time" => isset($base["cli_hope_contect_time"]) ? $base["cli_hope_contect_time"] : "",
            "cli_tag" => $cli_tag,
            "comment" => isset($base["comment"]) ? $base["comment"] : "",
            "type" => 1,
            "wed_date_sure" => $wed_date_sure,
            "wed_date" => $wed_date,
            "wed_location" => $wed_location,
            "wed_place" => isset($wed_info["wed_place"]) ? $wed_info["wed_place"] : "",
            "wed_party_type" => isset($wed_info["wed_party_type"]) ? $wed_info["wed_party_type"] : "",
            "people_num" => isset($wed_info["people_num"]) ? $wed_info["people_num"] : "",
            "budget" => isset($wed_info["budget"]) ? $wed_info["budget"] : "",
            "cli_hope_contect_way" => $cli_hope_contect_way,
        );

        //自我描述
        $params['qa']['description'] = array(
            'question' => '关于婚礼，以下哪种描述更符合您的要求？',
            'word' => '自我描述',
            'answer' => isset($wedplanner["description"]) ? $wedplanner["description"] : "",
        );

        //风格偏好
        $params['qa']['style'] = array(
            'question' => '您期待自己的婚礼现场是？',
            'word' => '风格偏好',
            'answer' => isset($wedplanner["style"]) ? $wedplanner["style"] : "",
        );

        //色系偏好
        $params['qa']['color'] = array(
            'question' => '您希望自己的婚礼现场的主色系是？',
            'word' => '色系偏好',
            'answer' => $color_detail,
        );

        //婚礼形容词
        $params['qa']['ideal'] = array(
            'question' => '请选择2-5个词描述您理想的婚礼：',
            'word' => '婚礼形容词',
            'answer' => $ideal_detail,
        );

        //婚礼过程最看重
        $params['qa']['importance'] = array(
            'question' => '在婚礼筹备时，您希望重点投入的是？',
            'word' => '婚礼希望重点投入',
            'answer' => isset($wedplanner["importance"]) ? $wedplanner["importance"] : "",
        );

        //婚礼希望重点投入
        $params['qa']['emphasis'] = array(
            'question' => '在婚礼过程中，您最看重的是？',
            'word' => '婚礼过程最看重',
            'answer' => $emphasis_detail,
        );

        //更多描述
        $params['qa']['moreinfo'] = array(
            'question' => '请描述您的喜好，以便策划师更好地了解您并为您提供更满意的婚礼方案：',
            'word' => '更多描述',
            'answer' => isset($wedplanner["moreinfo"]) ? $wedplanner["moreinfo"] : "",
        );

        //心仪案例
        $params['qa']['opus'] = array(
            'question' => '如果您在易结上看到了喜欢的婚礼案例，请输入链接地址：',
            'word' => '心仪案例',
            'answer' => $opus_detail,
        );

        //执行更新操作
        $ret = $this->execute_update($params);

        return self::returnArray('执行成功');
    }

    /**
     * 单项修改执行
     * @param $inputs
     */
    public function individual_update_exe($inputs){

        $base_arr = json_decode($inputs["baseinfo"], TRUE);
        $params['demand_id'] = $base_arr[0]['demand_id'];
        $base = array();
        @array_walk($base_arr, function($item) use(&$base){
            $base = array_merge_recursive($base, $item);
        });
        if((!isset($base['mode']) || empty($base['mode']))&&$inputs['submitype']==1){
            return self::returnArray('请选择招投标或是制定商家',0);
        }
        $wed_info_arr = json_decode($inputs["wedbaseinfo"], TRUE);
        $wed_info = array();
        @array_walk($wed_info_arr, function($item) use(&$wed_info){
            $wed_info = array_merge_recursive($wed_info, $item);
        });
        $multwed_arr = json_decode($inputs["multwedneed"], TRUE);
        $multwed = array();
        @array_walk($multwed_arr, function($item) use(&$multwed){
            $multwed = array_merge_recursive($multwed, $item);
        });

        //处理需求数组
        //客户来源
        $cli_source = isset($base["cli_source"]) ? $base["cli_source"] : 0;
        //获知渠道
        $channel = isset($base["channel"]) ? $base["channel"] : 0;

        //性别
        $sex = isset($base["cli_gender"]) ? $base["cli_gender"] : 0;
        switch ((int)$sex)
        {
            case 1:$cli_gender = "男";break;
            case 2:$cli_gender = "女";break;
            default:$cli_gender = "";break;
        }
        //学历
        $cli_edu = isset($base["cli_edu"]) ? $base["cli_edu"] : 0;
        switch ($cli_edu)
        {
            case 1:$cli_edu_detail = "小学";break;
            case 2:$cli_edu_detail = "初中";break;
            case 3:$cli_edu_detail = "高中";break;
            case 4:$cli_edu_detail = "专科";break;
            case 5:$cli_edu_detail = "本科";break;
            case 6:$cli_edu_detail = "硕士";break;
            case 7:$cli_edu_detail = "博士";break;
            case 8:$cli_edu_detail = "博士后";break;
            default:$cli_edu_detail = "";break;
        }
        //民族
        $nation = isset($base["cli_nation"]) ? $base["cli_nation"] : 0;
        $nation_info = $this->erp_conn->where("id", $nation)->get('erp_nation')->row_array();
        $cli_nation = $nation_info ? $nation_info["nation"] : "";
        //客户通讯地址
        $cli_country = isset($base["cli_country"]) ? $base["cli_country"] : 0;
        $cli_province =  isset($base["cli_province"]) ? $base["cli_province"] : 0;
        $cli_city = isset($base["cli_city"]) ? $base["cli_city"] : 0;
        $cli_location = $cli_country . ',' . $cli_province . ',' . $cli_city;
        //客户标签
        $tag_id_str = isset($base["cli_tag"]) ? $base["cli_tag"] : "";
        $tag_id_arr = explode(",", $tag_id_str);
        $cli_tag_arr = array();
        foreach ($tag_id_arr as $tag_id)
        {
            $tag_info = $this->erp_conn->where("id", $tag_id)->get("erp_client_tag")->row_array();
            if($tag_info)
            {
                $cli_tag_arr[] = $tag_info["tag_name"];
            }
        }
        $cli_tag = (!empty($cli_tag_arr)) ? implode('||', $cli_tag_arr) : "";
        $cli_tag = $tag_id_str;

        //婚礼日期
        $wed_date_sure = isset($wed_info["wed_date_sure"]) ? $wed_info["wed_date_sure"] : 0;
        if($wed_date_sure == 1)
        {
            if(!isset($wed_info["wed_date"]) || empty($wed_info["wed_date"])&&$inputs['submitype']==1){
                return self::returnArray('请填写婚礼日期',0);
            }
            $wed_date = $wed_info["wed_date"];
        }else
        {
            $wed_date = isset($wed_info["wed_date_notsure"]) ? $wed_info["wed_date_notsure"] : "";
        }
        //婚礼地点
        $wed_country = isset($wed_info["wed_country"]) ? $wed_info["wed_country"] : 0;
        $wed_province = isset($wed_info["wed_province"]) ? $wed_info["wed_province"] : 0;
        $wed_city = isset($wed_info["wed_city"]) ? $wed_info["wed_city"] : 0;
        $wed_location = $wed_country . ',' . $wed_province . ',' . $wed_city;
        //摄影师服务
        $photoer_service = isset($multwed["wedphotoer_service"]) ? $multwed["wedphotoer_service"] : array();
        if(!is_array($photoer_service))
        {
            $photoer_service = array($photoer_service);
        }
        //摄像师服务
        $video_service = isset($multwed["wedvideo_service"]) ? $multwed["wedvideo_service"] : array();
        if(!is_array($video_service))
        {
            $video_service = array($video_service);
        }

        //婚礼色系
        $color = isset($multwed["color"]) ? $multwed["color"] : array();
        if(!is_array($color))
        {
            $color = array($color);
        }
        $color_detail = implode("||", $color);

        //婚礼形容词
        $ideal = isset($multwed["ideal"]) ? $multwed["ideal"] : array();
        if(!is_array($ideal))
        {
            $ideal = array($ideal);
        }
        $ideal_detail = implode("||", $ideal);

        //主持人性别要求
        $wedmaster_sex = isset($multwed["wedmaster_people"]) ? $multwed["wedmaster_people"] : "";
        $wedmaster_height = isset($multwed["height"]) ? $multwed["height"] : "";
        $wedmaster_people = $wedmaster_sex . '||' . $wedmaster_height;

        $multwedneed = json_decode($inputs['multwedneed'],TRUE);
        $wedphotoer_service = array();
        $wedvideo_service = array();
        foreach($multwedneed as $k => $v){
            //摄影师的多项服务选择
            if(isset($v['wedphotoer_service'])){
                $wedphotoer_service[] = $v['wedphotoer_service'];
            }
            //摄像师的多项服务选择
            if(isset($v['wedvideo_service'])){
                $wedvideo_service[] = $v['wedvideo_service'];
            }
        }

        //期望联系方式
        $way = isset($base["cli_hope_contect_way"]) ? $base["cli_hope_contect_way"] : array();
        if(!is_array($way))
        {
            $way = array($way);
        }
        $cli_way = implode(",", $way);
        $cli_hope_contect_way = rtrim($cli_way, ",");
        //基本信息
        $params['content'] = array(
//            "mode" => isset($base["mode"]) ? $base["mode"] : 0,
            "counselor_uid" => isset($base["counselor_uid"]) ? $base["counselor_uid"] : 0,
            "cli_name" => isset($base["cli_name"]) ? $base["cli_name"] : "",
//            "cli_source" => $cli_source,
//            "channel" => $channel,
            "cli_gender" => $cli_gender,
            "cli_birth" => isset($base["cli_birth"]) ? $base["cli_birth"] : "",
            "cli_edu" => $cli_edu,
//            "cli_mobile" => isset($base["cli_mobile"]) ? $base["cli_mobile"] : "",
            "cli_tel" => isset($base["cli_tel"]) ? $base["cli_tel"] : "",
            "cli_nation" => $cli_nation,
            "cli_weixin" => isset($base["cli_weixin"]) ? $base["cli_weixin"] : "",
            "cli_qq" => isset($base["cli_qq"]) ? $base["cli_qq"] : "",
            "cli_weibo" => isset($base["cli_weibo"]) ? $base["cli_weibo"] : "",
            "cli_postcode" => isset($base["cli_postcode"]) ? $base["cli_postcode"] : "",
            "cli_email" => isset($base["cli_email"]) ? $base["cli_email"] : "",
            "cli_othercontect" => isset($base["cli_othercontect"]) ? $base["cli_othercontect"] : "",
            "cli_location" => $cli_location,
            "cli_address" => isset($base["cli_address"]) ? $base["cli_address"] : "",
            "cli_hope_contect_time" => isset($base["cli_hope_contect_time"]) ? $base["cli_hope_contect_time"] : "",
            "cli_hope_contect_way" => $cli_hope_contect_way,
            "cli_tag" => $cli_tag,
            "comment" => isset($base["comment"]) ? $base["comment"] : "",
            "type" => 2,
            "wed_date_sure" => $wed_date_sure,
            "wed_date" => $wed_date,
            "wed_location" => $wed_location,
            "wed_place" => isset($wed_info["wed_place"]) ? $wed_info["wed_place"] : "",
            "wed_party_type" => isset($wed_info["wed_party_type"]) ? $wed_info["wed_party_type"] : "",
        );
        $shoper = $this->content->getContentById($params['demand_id'],'shopper_alias');

        //化妆师
        if($shoper['shopper_alias']=='makeup'){
            //预定支持人的预算
            $params['qa']['amount'] = array(
                'question' => '预定化妆师，您的预算是？',
                'word' => '预算',
                'answer' => isset($multwed["makeup_amount"]) ? $multwed["makeup_amount"] : "",
            );

            //造型需求
            $params['qa']['modeling'] = array(
                'question' => '您计划婚礼当天选几套造型？',
                'word' => '造型需求',
                'answer' => isset($multwed["modeling"]) ? $multwed["modeling"] : "",
            );

            //备注要求
            $params['qa']['remark'] = array(
                'question' => '对于化妆师及其服务，您是否还有其他的要求或喜好？',
                'word' => '备注要求',
                'answer' => isset($multwed["makeup_remark"]) ? $multwed["makeup_remark"] : "",
            );

            //婚礼化妆师性别
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1){
                $params['qa']['people'] = array(
                    'question' => '对于您服务的化妆师，您的要求是？',
                    'word'=> '对人的要求',
                    'answer' => isset($multwed["makeup_people"]) ? $multwed["makeup_people"] : "",
                );
            }

        }

        //主持人
        if($shoper['shopper_alias']=='wedmaster'){
            //主持人预算
            $params['qa']['amount'] = array(
                'question' => '预定主持人，您的预算是？',
                'word' => '预算',
                'answer' => isset($multwed["wedmaster_amount"]) ? $multwed["wedmaster_amount"] : "",);

            //备注要求
            $params['qa']['remark'] = array(
                'question' => '对于主持人及其服务，您是否还有其他的要求或喜好？',
                'word' => '备注要求',
                'answer' => isset($multwed["wedmaster_remark"]) ? $multwed["wedmaster_remark"] : "",);

            //性别、身高
            if(isset($base["mode"]) && $base["mode"] == 1) {
                $params['qa']['people'] = array(
                    'question' => '对于为您服务的婚礼主持人，您的要求是？',
                    'word' => '对人的要求',
                    'answer' => $wedmaster_people,);
            }
        }

        //场地布置
        if($shoper['shopper_alias']=='sitelayout'){
            //预定支持人的预算
            $params['qa']['amount'] = array(
                'question' => '对于婚礼现场的场地布置，您的预算是？',
                'word' => '预算',
                'answer' => isset($multwed["sitelayout_amount"]) ? $multwed["sitelayout_amount"] : "",);

            //造型需求
            $params['qa']['style'] = array(
                'question' => '您期待自己的婚礼现场是？',
                'word' => '风格偏好',
                'answer' => isset($multwed["style"]) ? $multwed["style"] : "",);

            //备注要求
            $params['qa']['remark'] = array(
                'question' => '对于场地布置及其服务，您是否还有其他的要求或喜好？',
                'word' => '备注要求',
                'answer' => isset($multwed["sitelayout_remark"]) ? $multwed["sitelayout_remark"] : "",);

            //色系偏好
            $params['qa']['color'] = array(
                'question' => '您希望自己的婚礼现场的主色系是？',
                'word' => '色系偏好',
                'answer' => $color_detail,);

            //
            $params['qa']['ideal'] = array(
                'question' => '请选择2-5个词描述您理想的婚礼：',
                'word' => '婚礼形容词',
                'answer' => $ideal_detail,);

        }

        //婚礼摄像
        if($shoper['shopper_alias']=='wedphotoer'){
            //婚礼摄像师备注
            $params['qa']['remark'] = array(
                'question' => '对于摄影师及其服务，您是否还有其他的要求或喜好？',
                'word' => '备注要求',
                'answer' => isset($multwed["wedphotoer_remark"]) ? $multwed["wedphotoer_remark"] : "",
            );
            //性别
            if(isset($base["mode"]) && $base["mode"] == 1){
                $params['qa']['people'] = array(
                    'question' => '对于您服务的摄影师，您的要求是？',
                    'word' => '对人的要求',
                    'answer' => isset($multwed["wedphotoer_people"]) ? $multwed["wedphotoer_people"] : "",
                );
            }
            if(in_array("婚礼当天跟拍", $photoer_service)){
                //摄影服务
                $params['qa']['service'] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'answer' => "婚礼当天跟拍",
                );

                // 您的预算是
                $params['qa']['hlgp_amount'] = array(
                    'question' => '对于婚礼当天跟拍，您的预算是？',
                    'word' => '跟拍预算',
                    'answer' => isset($multwed["wedphotoer_hlgp_amount"]) ? $multwed["wedphotoer_hlgp_amount"] : "",
                );

                //跟拍方案
                $params['qa']['hlgp_scheme'] = array(
                    'question' => '您希望选择哪种跟拍方案？',
                    'word' => '跟拍方案',
                    'answer' => isset($multwed["wedphotoer_hlgp_scheme"]) ? $multwed["wedphotoer_hlgp_scheme"] : "",
                );

            }
            if(in_array("婚纱照拍摄", $photoer_service)){
                //摄影服务
                $params['qa']['service'] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'answer' => "婚纱照拍摄",
                );

                // 您的预算是
                $params['qa']['hspz_amount'] = array(
                    'question' => '对于婚纱照拍摄，您的预算是？',
                    'word' => '婚纱照拍摄预算',
                    'answer' => isset($multwed["hspz_amount"]) ? $multwed["hspz_amount"] : "",
                );
            }
        }
        //婚礼前的爱情微电影
        if($shoper['shopper_alias']=='wedvideo'){

            //备注要求
            $params['qa']['remark'] = array(
                'question' => '对于摄像师及其服务，您是否还有其他的要求或喜好？',
                'word' => '备注要求',
                'answer' => isset($multwed["wedvideo_remark"]) ? $multwed["wedvideo_remark"] : "",
            );

            if(in_array("婚礼前的爱情微电影", $video_service)){
                //摄像服务
                $params['qa']['service'] = array(
                    'question' => '您需要的摄像服务是？',
                    'word' => '服务选择',
                    'answer' => "婚礼前的爱情微电影",
                );

                //预算
                $params['qa']['wdy_amount'] = array(
                    'question' => '对于爱情微电影，您的预算是？',
                    'word' => '微电影预算',
                    'answer' => isset($multwed["wdy_amount"]) ? $multwed["wdy_amount"] : "",
                );

                if(isset($base["mode"]) && $base["mode"] == 1){
                    $params['qa']['people'] = array(
                        'question' => '对于为您服务的摄像师，您的要求是？',
                        'word' => '对人的要求',
                        'answer' => isset($multwed["wedvideo_people"]) ? $multwed["wedvideo_people"] : "",
                    );

                }
            }

            //婚礼当天跟拍
            if(in_array("婚礼当天跟拍", $video_service)){
                //摄像服务
                $params['qa']['service'] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'answer' => "婚礼当天跟拍",
                );

                //跟拍预算
                $params['qa']['hlgp_amount'] = array(
                    'question' => '对于婚礼当天跟拍，您的预算是？',
                    'word' => '跟拍预算',
                    'answer' => isset($multwed["wedvideo_hlgp_amount"]) ? $multwed["wedvideo_hlgp_amount"] : "",
                );

                //跟拍方案
                $params['qa']['hlgp_scheme'] = array(
                    'question' => '您希望选择哪种跟拍方案？',
                    'word' => '跟拍方案',
                    'answer' => isset($multwed["wedvideo_hlgp_scheme"]) ? $multwed["wedvideo_hlgp_scheme"] : "",
                );

                if(isset($base["mode"]) && $base["mode"] == 1){
                    $params['qa']['people'] = array(
                        'question' => '对于为您服务的摄像师，您的要求是？',
                        'word' => '对人的要求',
                        'answer' => isset($multwed["wedvideo_people"]) ? $multwed["wedvideo_people"] : "",
                    );
                }

            }
        }

        //执行更新操作
        $this->execute_update($params);

        return self::returnArray('执行成功');

    }

    /**
     * 执行更新操作
     */
    public function execute_update($params)
    {
        //更新content
        $this->content->edit($params['demand_id'],$params['content']);

        //更新期望联系方式的手机到cli_tel（如果cli_tel为空）
        $content = $this->content->getContentById($params['demand_id'],"id,cli_mobile,cli_tel,cli_hope_contect_way");
        $contect_way_arr = explode(',', $content['cli_hope_contect_way']);

        if($content['cli_tel'] == '')
        {
            if(in_array(1, $contect_way_arr)){

                $cli_tel_arr = array('cli_tel' => $content['cli_mobile']);
                $this->content->edit($content['id'],$cli_tel_arr);
            }
        }

        //更新 or 添加 qa操作
        if(isset($params['qa']) && is_array($params['qa']))
        {
            foreach($params['qa'] as $k => $v){

                $single_qa = $this->qa->getByCondition($params['demand_id'], $k);
                $demand = $this->content->getContentById($params['demand_id'], 'shopper_alias, service_type');

                if(!empty($single_qa)){

                    $this->qa->editByCondition($params['demand_id'], $v, $k);
                }else
                {
                    $data['content_id'] = $params['demand_id'];
                    $data['alias'] = $k;
                    $data['shopper_alias'] = $demand['shopper_alias'];
                    $data['service_type'] = $demand['service_type'];
                    $data = array_merge($data, $v);

                    $this->qa->addByCondition($data);
                }

            }
        }
        return self::returnArray('执行成功');
    }

    /*
     * 获取商家类型自荐表
     */
    private function getServiceType()
    {
        $index = array();
        $res = $this->ew_conn->from('options')
            ->get()->result_array();
        foreach($res as $k => $v){
            $index[$v['id']] = $v['option_alias'];
        }
        return $index;
    }

    /**
     * 获取商家列表
     * 分配商家
     */
    public function shopper_info($param){

        $index = $this->getServiceType();
        $serves_arr = explode(',',$param['serves']);
        $res = array();
        foreach($serves_arr as $ka => $va){
            $res[$index[$va]] = $this->getShopperInfoByType($param,$va);
        }
        return $res;
    }

    public function getShopperInfoByType($params,$type)
    {
        $sql_con = "select user_shopers.uid, users.nickname, users.phone, user_shopers.address, user_shopers.studio_name from ew_user_shopers as user_shopers join ew_users as users on users.uid = user_shopers.uid where users.dostatus=2 and user_shopers.site_id = 1 ";

        //类型：个人，公司或者工作室
        if(!empty($params['mode'])){
            $sql_con .= ' and user_shopers.mode = '.$params['mode'];
        }
        //价格范围（最低）
        if(!empty($params['price_start'])){
            $sql_con .= ' and user_shopers.price >'.$params['price_start'];
        }
        //价格范围（最高）
        if(!empty($params['price_end'])){
            $sql_con .= ' and user_shopers.price >'.$params['price_end'];
        }
        //地址
        if(!empty($params['address'])){
            $sql_con .= ' and user_shopers.address like "%1,'.$params['address'].'%"';
        }
        //关键词
        if(!empty($params['keyword'])){
            $sql_con .= ' and (users.nickname like "%'.$params['keyword'].'%" or users.phone like "%'.$params['keyword'].'%" or user_shopers.studio_name like "%'.$params['keyword'].'%")';
        }
        //作品数量（最低）
        if(!empty($params['opus_num_start'])){
            //$sql_con .= ' and ew_user_shopers.realname like "%'.$params['keyword'].'%"';
        }
        //作品数量（最高）
        if(!empty($params['opus_num_end'])){
            //$sql_con .= ' and ew_user_shopers.realname like "%'.$params['keyword'].'%"';
        }
        $sql_con .= ' and user_shopers.serves like "%'.$type.'%"';

        if($params['page'] == 1 || $params['page'] < 1 )
        {
            $offset = 1;
        }else{
            $offset = ($params['page'] -1)*$params['pagesize'];
        }

        if($offset == 1){
            $sql_limit = " limit ".$params['pagesize'];
        }else{
            $sql_limit = " limit ".$offset.",".$params['pagesize'];
        }

        $list = $this->ew_conn->query($sql_con.$sql_limit)->result_array();

//        print_r($this->ew_conn->last_query());die();

        $count = $this->ew_conn->query($sql_con)->num_rows();

        return array('total' => $count,'rows' => $list);
    }

    /*
     * 获取客户来源数组
     */
    public function getCliSource()
    {
        $cli_source_config = $this->erp_conn->from("erp_sys_func")->where("func_name","客户来源")->get()->result_array();
        if(isset($cli_source_config[0])){
            $cli_source_arr = $this->erp_conn->from("erp_sys_basesetting")->where("setting_id",$cli_source_config[0]['id'])->where("enable",1)->get()->result_array();
        }else{
            $cli_source_arr = array();
        }
        if(!empty($cli_source_arr)){
            foreach($cli_source_arr as $k => $v){
                $res[$v['id']] = $v['name'];
            }
        }
        return $res;
    }


}
