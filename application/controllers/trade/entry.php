<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entry extends App_Controller {

     public function __construct(){
        parent::__construct();
        $this->load->model("customer/record_model",'record');
        $this->load->model('sys_basesetting_model','baseset');
        $this->load->model('demand_order_log_model','order_log');
        $this->load->model('demand/demand_contract_model','contract');
        $this->load->model('demand/demands','demands');
        $this->load->model('ew/demand_content_model','content');
    }

    //录入客户信息引导页
    public function index()
    {
        $this->load->view('trade/entry/guide_view');
    }
    
    //提交引导页
    public function guide()
    {
        $wedtype = $this->input->post("wedtype");
//      $wedtype = 1;
        
        //获取网站色系
        $base_color = $this->func->getInfoByName("色系类型");
        if(! empty($base_color)){
            $this->_data["color"] =  $this->baseset->getInfosBySetting_id($base_color["id"], "id, name");
        }
        //获取婚礼形容词
        $base_adj = $this->func->getInfoByName("婚礼形容词");
        $this->_data["adj"] =  $this->baseset->getInfosBySetting_id($base_adj["id"], "id, name");

        if($wedtype == 1)
        {
            //一站式婚礼需求
            //1435
            $this->_data["serves"] = "1435";
            $this->load->view('trade/entry/demand_view', $this->_data);
          }elseif($wedtype == 2)
        {
            //单项式婚礼服务
            //wedmaster,wedphotoer,wedvideo 
//          $str = "wedmaster,makeup,wedphotoer,wedvideo,sitelayout";
//          $str = "1424,1425,1426,1423,1427,1435";
//          $this->_data["serves"] = $str;
            $this->_data["serves"] = $this->input->post("multvalue");
            $this->load->view('trade/entry/individual_view', $this->_data);
        }else
        {
            $this->load->view('trade/entry/guide_view');
        }
    }


    //提交一站式需求
    public function demand_add()
    {
        //接受参数
        $inputs = $this->input->post();
        //处理接受数组
        $base_arr = json_decode($inputs["baseinfo"], TRUE);
        $base = array();
        @array_walk($base_arr, function($item) use(&$base){
            $base = array_merge_recursive($base, $item);
        });
        if((!isset($base['mode']) || empty($base['mode']))&&$inputs['submitype']==1){
            return failure('请选择招投标或是指定商家');
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

        //判断手机号是否存在客户档案
        $phone = $base["cli_mobile"];
        if(!empty($phone))
        {
            $record_info = $this->record->getRecordByPhone($phone);
            if(empty($record_info))
            {
                $record_id = $this->record->addRecord($base);
            }
        }
        
        //处理需求数组
        //客户来源
        $cli_source = isset($base["cli_source"]) ? $base["cli_source"] : 0;
//      $source_info = $this->baseset->getInfoById($cli_source);
//      $cli_source_detail = $source_info ? $source_info["name"] : "";
        //获知渠道
        $channel = isset($base["channel"]) ? $base["channel"] : 0;
//      $channel_info = $this->baseset->getInfoById($channel);
//      $channel_detail = $channel_info ? $channel_info["name"] : "";
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
        $cli_province = isset($base["cli_province"]) ? $base["cli_province"] : 0;
        $cli_city = isset($base["cli_city"]) ? $base["cli_city"] : 0;
        $cli_location = $cli_country . ',' . $cli_province . ',' . $cli_city;
        //客户标签
        $tag_id_str = isset($base["tag"]) ? $base["tag"] : "";




       // print_r($tag_id_str);die;
        $tag_id_arr = explode(",", $tag_id_str);
       // print_r($tag_id_arr);die;
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

        //if($inputs['submitype']==1){
           // $cli_tag = $cli_tag;

      //  }else{
            $cli_tag = $tag_id_str;

        //}
       // print_r($cli_tag);die;
        //婚礼日期
        $wed_date_sure = isset($wed_info["wed_date_sure"]) ? $wed_info["wed_date_sure"] : 0;
        if($wed_date_sure == 1)
        {
            if(!isset($wed_info["wed_date"]) || empty($wed_info["wed_date"])&&$inputs['submitype']==1){
                return failure('请填写婚礼日期');
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
        $ideal = isset($wedplanner["ideals"]) ? $wedplanner["ideals"] : array();
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
        
        //商家信息id
        $id_arr = $wedplanner["bsnesids"];
        //print_r($id_arr);
        if(empty($id_arr) && $inputs['submitype']==1){
             return failure("请选择商家信息");
        }
            $shoper_ids = array();
            foreach($id_arr as $key => $ids)
            {
                if($base['mode'] == 2 && count($ids) > 1&&$inputs['submitype']==1){
                    return failure('指定商家只能选定一个策划师');
                }
                if(!empty($ids))
                {
                    foreach ($ids as $id)
                    {
                        $shoper_ids[$key][] = array(
                            "id" => $id
                        );
                        $sms_user[] = $id;
                    }
                }
            }


    	       if(empty($shoper_ids)&&$inputs['submitype']==1){
                    //echo 121;
                     return failure("请选择商家信息");
                }
                if(empty($shoper_ids)&&$inputs['submitype']==3){
                     $sms_user[] = array(0);
                }

        //期望联系方式
        $way = isset($base["cli_hope_contect_way"]) ? $base["cli_hope_contect_way"] : array();
        if(!is_array($way))
        {
            $way = array($way);
        }
        $cli_way = implode(",", $way);
        $cli_hope_contect_way = rtrim($cli_way, ",");
       // print_r($cli_hope_contect_way);die;
        //提交需求数组
        $entry = array(
            "base" => array(
                "status" => isset($inputs["submitype"]) ? $inputs["submitype"] : 3,
                "mode" => isset($base["mode"]) ? $base["mode"] : 0,
                "counselor_uid" => isset($base["counselor_uid"]) ? $base["counselor_uid"] : 0,
                "cli_name" => isset($base["cli_name"]) ? $base["cli_name"] : "",
                "cli_source" => $cli_source,
                "channel" => $channel,
                "cli_gender" => $cli_gender,
                "cli_birth" => isset($base["cli_birth"]) ? $base["cli_birth"] : "",
                "cli_edu" => $cli_edu_detail,
                "cli_mobile" => isset($base["cli_mobile"]) ? $base["cli_mobile"] : "",
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
                "cli_hope_contect_way" => $cli_hope_contect_way,
            ),
            "wed_info" => array(
                "type" => 1,
                "wed_date_sure" => $wed_date_sure,
                "wed_date" => $wed_date,
                "wed_location" => $wed_location,
                "wed_place" => isset($wed_info["wed_place"]) ? $wed_info["wed_place"] : "",
                "wed_party_type" => isset($wed_info["wed_party_type"]) ? $wed_info["wed_party_type"] : "",
                "people_num" => isset($wed_info["people_num"]) ? $wed_info["people_num"] : "",
                "budget" => isset($wed_info["budget"]) ? $wed_info["budget"] : "",
            ),
            "wedplanners" => array(
                array(
                    'question' => '关于婚礼，以下哪种描述更符合您的要求？',
                    'word' => '自我描述',
                    'alias' => 'description',
                    'datatype' => 'radio',
                    'answer' => isset($wedplanner["description"]) ? $wedplanner["description"] : "",
                  ),
                array(
                    'question' => '您期待自己的婚礼现场是？',
                    'word' => '风格偏好',
                    'alias' => 'style',
                    'datatype' => 'radio',
                    'answer' => isset($wedplanner["style"]) ? $wedplanner["style"] : "",
                  ),
                array(
                    'question' => '您希望自己的婚礼现场的主色系是？',
                    'word' => '色系偏好',
                    'alias' => 'color',
                    'datatype' => 'radio',
                    'answer' => $color_detail,
                  ),
                array(
                    'question' => '请选择2-5个词描述您理想的婚礼：',
                    'word' => '婚礼形容词',
                    'alias' => 'ideal',
                    'datatype' => 'checkbox',
                    'answer' => $ideal_detail,
                  ),
                array(
                    'question' => '在婚礼筹备时，您希望重点投入的是？',
                    'word' => '婚礼希望重点投入',
                    'alias' => 'emphasis',
                    'datatype' => 'checkbox',
                    'answer' => $emphasis_detail,
                  ),
                array(
                    'question' => '在婚礼过程中，您最看重的是？',
                    'word' => '婚礼过程最看重',
                    'alias' => 'importance',
                    'datatype' => 'radio',
                    'answer' => isset($wedplanner["importance"]) ? $wedplanner["importance"] : "",
                  ),
                array(
                    'question' => '请描述您的喜好，以便策划师更好地了解您并为您提供更满意的婚礼方案：',
                    'word' => '更多描述',
                    'alias' => 'moreinfo',
                    'datatype' => 'textarea',
                    'answer' => isset($wedplanner["moreinfo"]) ? $wedplanner["moreinfo"] : "",
                  ),
                array(
                    'question' => '如果您在易结上看到了喜欢的婚礼案例，请输入链接地址：',
                    'word' => '心仪案例',
                    'alias' => 'opus',
                    'datatype' => 'text',
                    'answer' => $opus_detail,
                  ),
            ),
            "shopper_ids" => $shoper_ids
        );
       //print_r($entry);die;
        if($inputs['submitype']==1){
			//保存
			 //向主站提交需求,返回主站id
			$config = $this->_data["config"];
			//print_r($config);die;
			$ewapi_url = $config["ew_domain"]."erp/demand/insert-one-demand";
			//print_r($ewapi_url);die;
			$result = $this->curl->post($ewapi_url, $entry);
			//print_r($result);die;
			$result_arr = json_decode($result, TRUE);
			//print_r($result_arr);die;
			if(!empty($result_arr)){
				$ew_uid = isset($result_arr['onestop'][0]["uid"]) ? $result_arr['onestop'][0]["uid"] : 0;
			}else{			
				$ew_uid  = "0";
			}
            //给商家发短信
            $msg = "您有一条客户咨询，请您马上登录易结，前往订单管理中查看，48小时内接单有效。";
            if(!empty($sms_user)){
                foreach($sms_user as $k => $v){
                    $phone_arr[] = $this->content->getPhoneByUid($v);
                }
            }
            $this->sms->send($phone_arr,$msg);
			
			//更新客户表的ew_uid
			if(isset($record_id) && $record_id > 0)
			{
				$data["ew_uid"] = $ew_uid;
				$res = $this->erp_conn->where('id', $record_id)->update(Record_model::TBL, $data);
			}
           // echo $result_arr['onestop'][0]['demandID'];
			if(isset($result_arr['onestop'][0]['demandID']))
			{
                
				//添加日志：一站式添加
				if(isset($result_arr['onestop'])){
					foreach($result_arr['onestop'] as $v){
						$this->addOrderLog($v['id'],$v['demandID'],0,'','提交需求','一站式_招投标');//插入需求日志
						foreach($v['order'] as $_v){
							$this->addOrderLog($v['id'],$v['demandID'],$_v['id'],$_v['orderID'],'提交订单','一站式_招投标');//插入订单日志
						}
					}
				}
                

				return success('添加成功');
			}else
			{
			   //return failure($result_arr['message']);

			   return failure("添加失败");
			}
		}else{

			$data['json_content'] = json_encode($entry,true);
			//保存草稿
			$data['create_time'] = date("Y:m:d H:i:s");
			$rows = $this->erp_conn->insert('ew_demand_draft', $data);
			if(empty($rows))
			{
				return failure("添加失败");     
			}else{
				//添加日志：一站式添加
               
				if(isset($result_arr['onestop'])){
					foreach($result_arr['onestop'] as $v){
						$this->addOrderLog($v['id'],$v['demandID'],0,'','提交需求','一站式_招投标');//插入需求日志
						foreach($v['order'] as $_v){
							$this->addOrderLog($v['id'],$v['demandID'],$_v['id'],$_v['orderID'],'提交订单','一站式_招投标');//插入订单日志
						}
					}
				}
				return success("保存成功");
			}

		}

    }


    //提交单项式服务需求
    public function individual_add()
    {
        //接受参数
        $inputs = $this->input->post();
        //处理接受数组
        $base_arr = json_decode($inputs["baseinfo"], TRUE);

        $base = array();
        @array_walk($base_arr, function($item) use(&$base){
            $base = array_merge_recursive($base, $item);
        });
        if((!isset($base['mode']) || empty($base['mode']))&&$inputs['submitype']==1){
            return failure('请选择招投标或是指定商家');
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

        //判断手机号是否存在客户档案
        $phone = $base["cli_mobile"];
        if(!empty($phone))
        {
            $record_info = $this->record->getRecordByPhone($phone);
            if(empty($record_info))
            {
                $record_id = $this->record->addRecord($base);
            }
        }

        //处理需求数组
        //客户来源
        $cli_source = isset($base["cli_source"]) ? $base["cli_source"] : 0;
//      $source_info = $this->baseset->getInfoById($cli_source);
//      $cli_source_detail = $source_info ? $source_info["name"] : "";
        //获知渠道
        $channel = isset($base["channel"]) ? $base["channel"] : 0;
//      $channel_info = $this->baseset->getInfoById($channel);
//      $channel_detail = $channel_info ? $channel_info["name"] : "";
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
        $cli_province = isset($base["cli_province"]) ? $base["cli_province"] : 0;
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
        //if($inputs['submitype']==1){
           // $cli_tag = $cli_tag;

       // }else{
            $cli_tag = $tag_id_str;

        //}
        //婚礼日期
        $wed_date_sure = isset($wed_info["wed_date_sure"]) ? $wed_info["wed_date_sure"] : 0;
        if($wed_date_sure == 1)
        {
            if(!isset($wed_info["wed_date"]) || empty($wed_info["wed_date"])&&$inputs['submitype']==1){
                return failure('请填写婚礼日期');
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
        //商家信息id
        $id_arr = json_decode($inputs['shopperids'],TRUE);

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
         if(empty($id_arr) && $inputs['submitype']==1){
             return failure("请选择商家信息");
         }
        $shoper_ids = array();
        if(isset($id_arr['wedmaster'])){
            if($base['mode'] == 2 && count($id_arr['wedmaster']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个主持人');
            }
            foreach($id_arr['wedmaster'] as $key => $ids)
            {
                $shoper_ids['wedmaster'][] = array('id' => $ids);
                //给发短信拼接用户
                $sms_user[] = $ids;
            }
        }

        if(isset($id_arr['makeup'])){
            if($base['mode'] == 2 && count($id_arr['makeup']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个化妆师');
            }
            foreach($id_arr['makeup'] as $key => $ids)
            {
                $shoper_ids['makeup'][] = array('id' => $ids);
                //给发短信拼接用户
                $sms_user[] = $ids;
            }
        }
        if(isset($id_arr['wedphotoer'])){
            if($base['mode'] == 2 && count($id_arr['wedphotoer']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个摄影师');
            }
            foreach($id_arr['wedphotoer'] as $key => $ids)
            {
                //给发短信拼接用户
                $sms_user[] = $ids;
                $shoper_ids['wedphotoer'][] = array('id' => $ids);
                // foreach($wedphotoer_service as $kws => $vws)
                // {
                //     $shoper_ids['wedphotoer'][] = array('id' => $ids,'service_type' => $vws);
                // }
            }
        }
        if(isset($id_arr['wedvideo'])){
            if($base['mode'] == 2 && count($id_arr['wedvideo']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个摄像师');
            }
            foreach($id_arr['wedvideo'] as $key => $ids)
            {
                //给发短信拼接用户
                $sms_user[] = $ids;
                $shoper_ids['wedvideo'][] = array('id' => $ids);
                // foreach($wedvideo_service as $kws => $vws)
                // {
                //     $shoper_ids['wedvideo'][] = array('id' => $ids,'service_type' => $vws);
                // }
            }
        }
        if(isset($id_arr['sitelayout'])){
            if($base['mode'] == 2 && count($id_arr['sitelayout']) > 1&&$inputs['submitype']==1){
                return failure('指定商家只能选定一个场地布置');
            }
            foreach($id_arr['sitelayout'] as $key => $ids)
            {
                $shoper_ids['sitelayout'][] = array('id' => $ids);
                //给发短信拼接用户
                $sms_user[] = $ids;
            }
        }

        if(empty($shoper_ids)&&$inputs['submitype']==1){
                 return failure("请选择商家信息");
            }
            if(empty($shoper_ids)&&$inputs['submitype']==3){
                 $sms_user[] = array(0);
            }

        //期望联系方式
        $way = isset($base["cli_hope_contect_way"]) ? $base["cli_hope_contect_way"] : array();
        if(!is_array($way))
        {
            $way = array($way);
        }
        $cli_way = implode(",", $way);
        $cli_hope_contect_way = rtrim($cli_way, ",");

        $entry = array(
            "base" => array(
                "status" => isset($inputs["submitype"]) ? $inputs["submitype"] : 3,
                "mode" => isset($base["mode"]) ? $base["mode"] : 0,
                "counselor_uid" => isset($base["counselor_uid"]) ? $base["counselor_uid"] : 0,
                "cli_name" => isset($base["cli_name"]) ? $base["cli_name"] : "",
                "cli_source" => $cli_source,
                "channel" => $channel,
                "cli_gender" => $cli_gender,
                "cli_birth" => isset($base["cli_birth"]) ? $base["cli_birth"] : "",
                "cli_edu" => $cli_edu_detail,
                "cli_mobile" => isset($base["cli_mobile"]) ? $base["cli_mobile"] : "",
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
                "cli_hope_contect_way" => $cli_hope_contect_way,
            ),
            "wed_info" => array(
                "type" => 2,
                "wed_date_sure" => $wed_date_sure,
                "wed_date" => $wed_date,
                "wed_location" => $wed_location,
                "wed_place" => isset($wed_info["wed_place"]) ? $wed_info["wed_place"] : "",
                "wed_party_type" => isset($wed_info["wed_party_type"]) ? $wed_info["wed_party_type"] : "",
            ),
            "shopper_ids" => $shoper_ids
        );

        if(isset($multwed["wedmaster_amount"]))
        {
            $entry["wedmaster"] = array(
                array(
                    'question' => '预定主持人，您的预算是？',
                    'word' => '预算',
                    'alias' => 'amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedmaster_amount"]) ? $multwed["wedmaster_amount"] : "",
                ),
                array(
                    'question' => '对于主持人及其服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    "answer" => isset($multwed["wedmaster_remark"]) ? $multwed["wedmaster_remark"] : "",
                ),
            );
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1){
                $entry["wedmaster"][] = array(
                    'question' => '对于为您服务的婚礼主持人，您的要求是？',
                    'word' => '对人的要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => $wedmaster_people,
                );
            }
        }
        if(isset($multwed["makeup_amount"]))
        {
            $entry["makeup"] = array(
                array(
                    'question' => '预定化妆师，您的预算是？',
                    'word' => '预算',
                    'alias' => 'amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["makeup_amount"]) ? $multwed["makeup_amount"] : "",
                ),

                array(
                    'question' => '您计划婚礼当天选几套造型？',
                    'word' => '造型需求',
                    'alias' => 'modeling',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["modeling"]) ? $multwed["modeling"] : "",
                ),
                array(
                    'question' => '对于化妆师及其服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    'answer' => isset($multwed["makeup_remark"]) ? $multwed["makeup_remark"] : "",
                ),
                 array(
                    'question' => '对于为您服务的婚礼化妆师，您的要求是？',
                    'word' => '备注要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["makeup_people"]) ? $multwed["makeup_people"] : "",
                ),
            );
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1){
                $entry["makeup"][] = array(
                    'question' => '对于您服务的化妆师，您的要求是？',
                    'word' => '对人的要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["makeup_people"]) ? $multwed["makeup_people"] : "",
                );
            }
        }
        if(isset($multwed["wedphotoer_service"]))
        {
            $entry["wedphotoer"] = array(
                array(
                    'question' => '对于摄影师及其服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    'answer' => isset($multwed["wedphotoer_remark"]) ? $multwed["wedphotoer_remark"] : "",
                )
            );

            if(in_array("婚纱照拍摄", $photoer_service))
            {
                $entry["wedphotoer"][] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'alias' => 'service',
                    'datatype' => 'checkbox',
                    'service_type' => "hspz",
                    'answer' => "婚纱照拍摄",
                );
                //当选择选择婚纱照拍摄才会有此项
                $entry["wedphotoer"][] = array(
                    'question' => '对于婚纱照拍摄，您的预算是？',
                    'word' => '婚纱照拍摄预算',
                    'alias' => 'hspz_amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["hspz_amount"]) ? $multwed["hspz_amount"] : "",
                );
            }
            if(in_array("婚礼当天跟拍", $photoer_service))
            {
                $entry["wedphotoer"][] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'alias' => 'service',
                    'datatype' => 'checkbox',
                    'service_type' => "hlgp",
                    'answer' => "婚礼当天跟拍",
                );
                //选择婚礼跟拍才会有此项
                $entry["wedphotoer"][] = array(
                    'question' => '对于婚礼当天跟拍，您的预算是？',
                    'word' => '跟拍预算',
                    'alias' => 'hlgp_amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedphotoer_hlgp_amount"]) ? $multwed["wedphotoer_hlgp_amount"] : "",
                );
                //当选择婚礼跟拍才会有此项
                $entry["wedphotoer"][] = array(
                    'question' => '您希望选择哪种跟拍方案？',
                    'word' => '跟拍方案',
                    'alias' => 'hlgp_scheme',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedphotoer_hlgp_scheme"]) ? $multwed["wedphotoer_hlgp_scheme"] : "",
                );
            }
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1)
            {
                $entry["wedphotoer"][] = array(
                    'question' => '对于您服务的摄影师，您的要求是？',
                    'word' => '对人的要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedphotoer_people"]) ? $multwed["wedphotoer_people"] : "",
                );
            }
        }
        if(isset($multwed["wedvideo_service"]))
        {
            $entry["wedvideo"] = array(
                array(
                    'question' => '对于摄像师及其服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    'answer' => isset($multwed["wedvideo_remark"]) ? $multwed["wedvideo_remark"] : "",
                )
            );

            if(in_array("婚礼前的爱情微电影", $video_service))
            {
                $entry["wedvideo"][] = array(
                    'question' => '您需要的摄像服务是？',
                    'word' => '服务选择',
                    'alias' => 'service',
                    'datatype' => 'checkbox',
                    'service_type' => "hspz",
                    'answer' => "婚礼前的爱情微电影",
                );
                //选择爱情微电影才会有此项
                $entry["wedvideo"][] = array(
                    'question' => '对于爱情微电影，您的预算是？',
                    'word' => '微电影预算',
                    'alias' => 'wdy_amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wdy_amount"]) ? $multwed["wdy_amount"] : "",
                );
            }
            if(in_array("婚礼当天跟拍", $video_service))
            {
                $entry["wedvideo"][] = array(
                    'question' => '您需要的摄影服务是？',
                    'word' => '服务选择',
                    'alias' => 'service',
                    'datatype' => 'checkbox',
                    'service_type' => "hlgp",
                    'answer' => "婚礼当天跟拍",
                );
                //选择婚礼跟拍才会有此项
                $entry["wedvideo"][] = array(
                    'question' => '对于婚礼当天跟拍，您的预算是？',
                    'word' => '跟拍预算',
                    'alias' => 'hlgp_amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedvideo_hlgp_amount"]) ? $multwed["wedvideo_hlgp_amount"] : "",
                );
                //当选择婚礼跟拍才会有此项
                $entry["wedvideo"][] = array(
                    'question' => '您希望选择哪种跟拍方案？',
                    'word' => '跟拍方案',
                    'alias' => 'hlgp_scheme',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedvideo_hlgp_scheme"]) ? $multwed["wedvideo_hlgp_scheme"] : "",
                );
            }
            //如果是指定商家没有此项
            if(isset($base["mode"]) && $base["mode"] == 1)
            {
                $entry["wedvideo"][] = array(
                    'question' => '对于为您服务的摄像师，您的要求是？',
                    'word' => '对人的要求',
                    'alias' => 'people',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["wedvideo_people"]) ? $multwed["wedvideo_people"] : "",
                );
            }
        }
        if(isset($multwed["sitelayout_amount"]))
        {
            $entry["sitelayout"] = array(
                array(
                    'question' => '对于婚礼现场的场地布置，您的预算是？',
                    'word' => '预算',
                    'alias' => 'amount',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["sitelayout_amount"]) ? $multwed["sitelayout_amount"] : "",
                ),
                array(
                     'question' => '您期待自己的婚礼现场是？',
                    'word' => '风格偏好',
                    'alias' => 'style',
                    'datatype' => 'radio',
                    'answer' => isset($multwed["style"]) ? $multwed["style"] : "",
                ),
                array(
                    'question' => '您希望自己的婚礼现场的主色系是？',
                    'word' => '色系偏好',
                    'alias' => 'color',
                    'datatype' => 'radio',
                    'answer' => $color_detail,
                ),
                array(
                    'question' => '请选择2-5个词描述您理想的婚礼：',
                    'word' => '婚礼形容词',
                    'alias' => 'ideal',
                    'datatype' => 'checkbox',
                    'answer' => $ideal_detail,
                ),
                array(
                    'question' => '对于场地布置及其服务，您是否还有其他的要求或喜好？',
                    'question_appoint' => '对于我的服务，您是否还有其他的要求或喜好？',
                    'word' => '备注要求',
                    'alias' => 'remark',
                    'datatype' => 'textarea',
                    'answer' => isset($multwed["sitelayout_remark"]) ? $multwed["sitelayout_remark"] : "",
                ),
            );
        }
       // print_r($entry);die;
		if($inputs['submitype']==1){
				//向主站提交需求,返回主站id
				$config = $this->_data["config"];
				$ewapi_url = $config["ew_domain"]."erp/demand/insert-one-demand";
				$result = $this->curl->post($ewapi_url, $entry);
				$result_arr = json_decode($result, TRUE);
				//给商家发短信
                $msg = "您有一条客户咨询，请您马上登录易结，前往订单管理中查看，48小时内接单有效。";
                if(!empty($sms_user)){
                    foreach($sms_user as $k => $v){
                        $phone_arr[] = $this->content->getPhoneByUid($v);
                    }
                }
                $this->sms->send($phone_arr,$msg);

				$ew_uid = $result_arr ? $result_arr['monomial'][0]["uid"] : 0;
                //更新客户表的ew_uid
				if(isset($record_id) && $record_id > 0)
				{
					$data = array("ew_uid" => $ew_uid);
					$res = $this->erp_conn->where('id', $record_id)->update(Record_model::TBL, $data);
				}

				if(isset($result_arr['monomial'][0]['demandID']))
				{
					//添加日志：一站式添加
					if(isset($result_arr['monomial'])){
						foreach($result_arr['monomial'] as $v){
							$this->addOrderLog($v['id'],$v['demandID'],0,'','提交需求','单项_招投标');//插入需求日志
							foreach($v['order'] as $_v){
								$this->addOrderLog($v['id'],$v['demandID'],$_v['id'],$_v['orderID'],'提交订单','单项_招投标');//插入订单日志
							}
						}
					}
					return success('添加成功');
				}else
				{
					return failure($result_arr['message']);
				}
		}else{
           //print_r($entry);die;
			$data['json_content'] = json_encode($entry,true);
			//保存草稿
			$data['create_time'] = date("Y:m:d H:i:s");
			$rows = $this->erp_conn->insert('ew_demand_draft', $data);
			if(empty($rows))
			{
				return failure("添加失败");
			}else{
				//添加日志：一站式添加
					if(isset($result_arr['monomial'])){
						foreach($result_arr['monomial'] as $v){
							$this->addOrderLog($v['id'],$v['demandID'],0,'','提交需求','单项_招投标');//插入需求日志
							foreach($v['order'] as $_v){
								$this->addOrderLog($v['id'],$v['demandID'],$_v['id'],$_v['orderID'],'提交订单','单项_招投标');//插入订单日志
							}
						}
					}
				return success("保存成功");
			}
		}



    }
    
    //数据字典表
    public function dictionary()
    {
        //找商家方式
        $data["find_type"] = array(
            array(
                "id" => 1,
                "text" => "招投标",
            ),
            array(
                "id" => 2,
                "text" => "指定商家",
            ),
        );
        //需求类型
        $data["type"] = array(
            array(
                'id' => 1,
                'text'=>'一站式',
                'value' => array(
                    array(
                        'id' => 'wedplanners',
                        'text' => '策划师',
                        'value' => array(
                            // array(
                            //     'id' => '1.5万以下',
                            //     'text' => '1.5万以下'
                            // ),
                            // array(
                            //     'id' => '1.5-3万',
                            //     'text' => '1.5-3万'
                            // ),
                            // array(
                            //     'id' => '2万以下',
                            //     'text' => '2万以下'
                            // ),
                            // array(
                            //     'id' => '2-5万',
                            //     'text' => '2-5万'
                            // ),
                            // array(
                            //     'id' => '3-5万',
                            //     'text' => '3-5万'
                            // ),
                            // array(
                            //     'id' => '5-10万',
                            //     'text' => '5-10万'
                            // ),
                            // array(
                            //     'id' => '10万以上',
                            //     'text' => '10万以上'
                            // ),
                            // array(
                            //     'id' => '20-50万',
                            //     'text' => '20-50万'
                            // ),
                            // array(
                            //     'id' => '50万以上',
                            //     'text' => '50万以上'
                            // ),
                            array(
                                'id' => '2万以下',
                                'text' => '2万以下'
                            ),
                            array(
                                'id' => '2-4万',
                                'text' => '2-4万'
                            ),
                            array(
                                'id' => '4-7万',
                                'text' => '4-7万'
                            ),
                            array(
                                'id' => '7-10万',
                                'text' => '7-10万'
                            ),
                            array(
                                'id' => '10万以上',
                                'text' => '10万以上'
                            ),
                            array(
                                'id' => '没有概念，先聊聊',
                                'text' => '没有概念，先聊聊'
                            ),
                        ),
                    )
                ),
            ),
            array(
                'id' => 2,
                'text'=>'单项',
                'value' => array(
                    array(
                        'id' => 'wedmaster',
                        'text' => '主持人',
                        'value' => array(
                            array(
                                'id' => '2000以下',
                                'text' => '2000以下'
                            ),
                            array(
                                'id' => '2001-4000',
                                'text' => '2001-4000'
                            ),
                            array(
                                'id' => '4001-6000',
                                'text' => '4001-6000'
                            ),
                            array(
                                'id' => '6000以上',
                                'text' => '6000以上'
                            ),
                        ),
                    ),
                    array(
                        'id' => 'makeup',
                        'text' => '化妆师',
                        'value' => array(
                            array(
                                'id' => '2000以下',
                                'text' => '2000以下'
                            ),
                            array(
                                'id' => '2001-4000',
                                'text' => '2001-4000'
                            ),
                            array(
                                'id' => '4001-6000',
                                'text' => '4001-6000'
                            ),
                            array(
                                'id' => '6000以上',
                                'text' => '6000以上'
                            ),
                        ),
                    ),

                    array(
                        'id' => 'wedphotoer',
                        'text' => '摄影师',
                        'value' => array(
                            array(
                                'id' => '3000以下',
                                'text' => '3000以下'
                            ),
                            array(
                                'id' => '3000-5000',
                                'text' => '3000-5000'
                            ),
                            array(
                                'id' => '5000以下',
                                'text' => '5000以下'
                            ),
                            array(
                                'id' => '5000-8000',
                                'text' => '5000-8000'
                            ),
                            array(
                                'id' => '5001-9000',
                                'text' => '5001-9000'
                            ),
                            array(
                                'id' => '8000-13000',
                                'text' => '8000-13000'
                            ),
                            array(
                                'id' => '9000以上',
                                'text' => '9000以上'
                            ),
                            array(
                                'id' => '13000以上',
                                'text' => '13000以上'
                            ),
                        ),
                    ),

                    array(
                        'id' => 'wedvideo',
                        'text' => '摄像师',
                        'value' => array(
                            array(
                                'id' => '4000以下',
                                'text' => '4000以下'
                            ),
                            array(
                                'id' => '4000-8000',
                                'text' => '4000-8000'
                            ),
                            array(
                                'id' => '8001-15000',
                                'text' => '8001-15000'
                            ),
                            array(
                                'id' => '10000以下',
                                'text' => '10000以下'
                            ),
                            array(
                                'id' => '15000以上',
                                'text' => '15000以上'
                            ),
                            array(
                                'id' => '10001-20000',
                                'text' => '10001-20000'
                            ),
                            array(
                                'id' => '20001-30000',
                                'text' => '20001-30000'
                            ),
                            array(
                                'id' => '30000以上',
                                'text' => '30000以上'
                            ),
                        ),
                    ),

                    array(
                        'id' => 'sitelayout',
                        'text' => '场地布置',
                        'value' => array(
                            array(
                                'id' => '1.5万以下',
                                'text' => '1.5万以下'
                            ),
                            array(
                                'id' => '1.5-3万',
                                'text' => '1.5-3万'
                            ),
                            array(
                                'id' => '2万以下',
                                'text' => '2万以下'
                            ),
                            array(
                                'id' => '2-5万',
                                'text' => '2-5万'
                            ),
                            array(
                                'id' => '3-5万',
                                'text' => '3-5万'
                            ),
                            array(
                                'id' => '5-10万',
                                'text' => '5-10万'
                            ),
                            array(
                                'id' => '10万以上',
                                'text' => '10万以上'
                            ),
                            array(
                                'id' => '20-50万',
                                'text' => '20-50万'
                            ),
                            array(
                                'id' => '50万以上',
                                'text' => '50万以上'
                            ),
                        ),
                    )

                ),

            ),
        );

        $data['status'] = array(
            array(
                'id' => 0,
                'text' => '无分配',
            ),
            array(
                'id' => 4,
                'text' => '已分配',
            ),
        );
        //新人顾问
        $data["consultant"] = $this->erp_conn->select("id, username as text")->where('status',Sys_user_model::STATUS_NORMAL)->get(Sys_user_model::TBL)->result_array();
        //客户来源
        $auth_info = $this->func->getInfoByName("客户来源");
        $data["cli_source"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name as text");
        
        //获知渠道
        $auth_info = $this->func->getInfoByName("获知渠道");
        $data["channel"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name as text");
        
        //学历
        $data["cli_edu"] = array(
            array(
                "id" => 1,
                "text" => "小学"
            ),
            array(
                "id" => 2,
                "text" => "初中"
            ),
            array(
                "id" => 3,
                "text" => "高中"
            ),
            array(
                "id" => 4,
                "text" => "专科"
            ),
            array(
                "id" => 5,
                "text" => "本科"
            ),
            array(
                "id" => 6,
                "text" => "硕士"
            ),
            array(
                "id" => 7,
                "text" => "博士"
            ),
            array(
                "id" => 8,
                "text" => "博士后"
            ),
        );
        
        //民族
        $data["cli_race"] = $this->erp_conn->select("id, nation as text")->get('erp_nation')->result_array();
        
        //血型
        $data["cli_blood"] = array(
            array(
                "id" => 1,
                "text" => "O型"
            ),
            array(
                "id" => 2,
                "text" => "A型"
            ),
            array(
                "id" => 3,
                "text" => "B型"
            ),
            array(
                "id" => 4,
                "text" => "AB型"
            )
        );
        
        //地区联级-国家
        $this->load->model("commons/region_model",'region');
        $region = $this->region->getInfoByPid(0, "id, name as text");
        $data["country"] = $region;
        
        //所在省份
        $ch_info = $this->region->getRegionByName("中国");
        $province = $this->region->getInfoByPid($ch_info["id"], "id, name as text");
        $data["province"] = $province;

        //自选条件查询
        $data["condition"] = array(
            array(
                "id" => 'demand_id',
                "text" => "交易编号"
            ),
            array(
                "id" => 'cli_name',
                "text" => "客户姓名"
            ),
            array(
                "id" => 'nick_name',
                "text" => "客户昵称"
            ),
            array(
                "id" => 'cli_mobile',
                "text" => "手机号码"
            ),
            array(
                "id" => 'cli_qq',
                "text" => "QQ"
            ),
            array(
                "id" => 'cli_weixin',
                "text" => "微信"
            ),
            array(
                "id" => 'cli_weibo',
                "text" => "客户微博"
            ),
            array(
                "id" => 'cli_email',
                "text" => "电子邮箱"
            ),
        );

        //交易提示
        $data['trademark'] = $this->erp_conn->select("id, name as text")->where('mark_id',173)->where('enable',1)->get('erp_sys_trademarksetting')->result_array();
        return success($data);
    }

    //获取所有商家信息
    public function getShopers()
    {
        $inputs = $this->input->get();

        //服务类型
        $data['serves'] = isset($inputs['serves']) ? $inputs['serves'] : "";

        //价格起始
        $data['price_start'] = isset($inputs['price_start']) ? $inputs['price_start'] : 0;

        //价格截至
        $data['price_end'] = isset($inputs['price_end']) ? $inputs['price_end'] : 0;

        //案例个数起始
        $data['opus_num_start'] = isset($inputs['opus_num_start']) ? $inputs['opus_num_start'] : 0;

        //案例个数截至
        $data['opus_num_end'] = isset($inputs['pous_num_end']) ? $inputs['pous_num_end'] : 0;

        //所在地区
        $province_1 = isset($inputs['province']) ? $inputs['province'] : '';
        $province_2 = isset($inputs['province_dlg']) ? $inputs['province_dlg'] : '';
        $city_1 = isset($inputs['city']) ? $inputs['city'] : '';
        $city_2 = isset($inputs['city_dlg']) ? $inputs['city_dlg'] : '';

        if(!empty($province_1)){
            $province = $province_1;
        }elseif(! empty($province_2)){
            $province = $province_2;
        }else{
            $province = 0;
        }

        if(!empty($city_1)){
            $city = $city_1;
        }elseif(! empty($city_2)){
            $city = $city_2;
        }else{
            $city = 0;
        }

        if(!empty($province) && !empty($city))
        {
            $address = "$province,$city";
        }
        elseif(!empty($province))
        {
            $address = "$province,";
        }
        elseif(!empty($city))
        {
            $address = ",$city";
        }

        $data['address'] = isset($address) ? $address : '';

        //商家类型
        $data['mode'] = isset($inputs['shoper_mode']) ? $inputs['shoper_mode'] : 0;

        //关键字
        $data['keyword'] = isset($inputs['keywords']) ? $inputs['keywords'] : '';

        //page
        $data['page'] = (isset($inputs['page']) && $inputs['page'] > 1) ? $inputs['page'] : 1;
        $data['pagesize'] = isset($inputs['pagesize']) ? $inputs['pagesize'] : 10;

        $shoper_list = $this->demands->shopper_info($data);

        return success($shoper_list);
    }

	public function review()
	{
		$id = $this->input->get("id") ? $this->input->get("id") : 0;
		$this->_data['page'] = $this->input->get("page") ? $this->input->get("page") : 1;
		$this->_data['pagesize'] = $this->input->get("pagesize") ? $this->input->get("pagesize") : 10;
		$this->_data['backurl'] = urldecode($this->input->get("backurl")) ? urldecode($this->input->get("backurl")) : "";
        $this->_data['pageflag'] = $this->input->get("pageflag") ? $this->input->get("pageflag") : "";
        $order_status = $this->input->get('order_status');
        $be_status = $this->input->get('be_status');
        $order_status = isset($order_status) ? $order_status : '';
        $be_status = isset($be_status) ? $be_status : '';
        $this->_data['be_status'] = $be_status;
        $this->_data['order_status'] = $order_status;

//		$id = 1;
		$config = $this->_data["config"];

        $demand = $this->demands->demand_detail(array('id' => $id));

		$question_arr = array();
        if(isset($demand['question']) && !empty($demand['question'])) {
            foreach ($demand["question"] as $key => $ques) {
                foreach ($ques as $k => $v) {
                    $question_arr[$key][$v["alias"]] = $v;
                }
            }
        }

        //通过shopper_alias然后查询出alias_code
        $qa_info = $this->ew_conn->select('id as alias_code')->where('option_alias',$demand['base']['shopper_alias'])->limit(1)->get('options')->row_array();
        $this->_data['qa_info'] = $qa_info;

        //判断当前需求的 初选 or 确认
        $order_info = $this->ew_conn->select('demand_order.id,demand_order.content_id,options.id as alias_code,demand_order.status')->from('demand_order')->join('demand_content','demand_content.id = demand_order.content_id')->join('options','options.option_alias = demand_order.shopper_alias')->where('demand_order.content_id', $id)->where('demand_order.status <> 99')->order_by('demand_order.status','desc')->limit(1)->get()->row_array();
        $confirm_button = '';

        $primary_status_arr = array(11, 21, 31);//初选状态
        $confirm_status_arr = array(41, 46);//初选状态

        if(! empty($order_info)){

            if(in_array($order_info['status'], $primary_status_arr)){

                $confirm_button = 1;
            }elseif(in_array($order_info['status'], $confirm_status_arr)){

                $confirm_button = 2;
            }
        }
        $this->_data['confirm_button'] = $confirm_button;

        //分配商家按钮显示配置
        $send_button = '';

        if($demand['base']["mode"] == 1){//招投标的需求

            if($demand['base']["type"] == 1){//一站式

                if($demand['base']["status"] != 80 && $demand['base']["status"] != 99){

                    $send_button = 1;
                }
            }elseif($demand['base']["type"] == 2) {//四大金刚

                if($demand['base']["status"] == 0 || $demand['base']["status"] == 4){

                    $send_button = 2;
                }
            }
        }
        $this->_data['send_button'] = $send_button;

		//客户标签
		$demand["base"]["cli_tag"] = isset($demand["base"]["cli_tag"]) ? str_replace("||", ",", $demand["base"]["cli_tag"]) : "";
        //新人顾问
        $couselor_uid = isset($demand['base']['counselor_uid']) ? $demand['base']['counselor_uid'] : '';

        $consultant_info = $this->erp_conn->select("id, username")->where('id',$couselor_uid)->get(Sys_user_model::TBL)->row_array();
        $demand['base']['consultant_name'] = !empty($consultant_info['username']) ? $consultant_info['username'] : '';
        $demand['base']['consultant_id'] = !empty($consultant_info['id']) ? $consultant_info['id'] : '';

		$this->_data["base"] = $demand["base"];
		$this->_data["wedplanners"] = isset($demand["question"]["wedplanners"]) ? $demand["question"]["wedplanners"] : array();
		$this->_data["wedmaster"] = isset($question_arr["wedmaster"]) ? $question_arr["wedmaster"] : array();
		$this->_data["makeup"] = isset($question_arr["makeup"]) ? $question_arr["makeup"] : array();
		$this->_data["wedphotoer"] = isset($question_arr["wedphotoer"]) ? $question_arr["wedphotoer"] : array();
		$this->_data["wedvideo"] = isset($question_arr["wedvideo"]) ? $question_arr["wedvideo"] : array();
		$this->_data["sitelayout"] = isset($question_arr["sitelayout"]) ? $question_arr["sitelayout"] : array();


		//新人顾问
		$this->_data["consultant"] = $this->erp_conn->select("id, username")->get(Sys_user_model::TBL)->result_array();
		//客户来源
		$auth_info = $this->func->getInfoByName("客户来源");
		$this->_data["cli_source"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
		//获知渠道
		$auth_info = $this->func->getInfoByName("获知渠道");
		$this->_data["channel"] =  $this->baseset->getInfosBySetting_id($auth_info["id"], "id, name");
		//支付方式
	    $sql = "SELECT `id`,`setting_id`, `name`,`enable`,`order` FROM (`ew_erp_sys_basesetting`) WHERE `setting_id` = 176  and `order` = 22 and `enable`= 1";
	    $arr = $this->erp_conn->query($sql);
	    $this->_data['list']= $arr->result_array();
		
		$mode = $this->input->get("wedtype");
//		$mode = 1;
		$this->_data["mode"] = $mode;

        //在线签约
        $contract = $this->contract->getContract(array('demand_id' => $id));
        if(isset($contract[0])){
            $this->_data['contract'] = $contract[0];
        }else{
            $this->_data['contract'] = array(
                        'contract_code'=>'',
                        'money'=>'',
                        'wed_date'=>($this->_data['base']['wed_date_sure']==1)?$this->_data['base']['wed_date']:'',
                        'wed_location'=>!empty($this->_data['base']['wed_location'])?$this->_data['base']['wed_location']:'',
                        'wed_place'=>!empty($this->_data['base']['wed_place'])?$this->_data['base']['wed_place']:'',
                        'comment'=>'',
                    );
        }
        //获取已中标的商家
        $bingoShopper = $this->contract->getBingoShopper(array('demand_id' => $id));
        if(isset($bingoShopper[0])){
            $this->_data['bingoShopper'] = $bingoShopper[0];
        }else{
            $this->_data['bingoShopper'] = array(
                    'order_id' => '',
                    'nickname' => '',
                    'phone' => ''
                );
        }
        // print_r($this->_data);die;
		$this->load->view('trade/entry/review_view', $this->_data);
	}
	//复制功能
    public function copy(){
		//接受参数
		$inputs = $this->input->post();
		//print_r($inputs);
		if($inputs['wedtype']==0){
			//一站式复制
				//处理接受数组
				$base_arr = json_decode($inputs["baseinfo"], TRUE);
				$base = array();
				@array_walk($base_arr, function($item) use(&$base){
					$base = array_merge_recursive($base, $item);
				});
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

				//判断手机号是否存在客户档案
				$phone = $base["cli_mobile"];
				if(!empty($phone))
				{
					$record_info = $this->record->getRecordByPhone($phone);
					if(empty($record_info))
					{
						$record_id = $this->record->addRecord($base);
					}
				}
				
				//处理需求数组
				//客户来源
				$cli_source = isset($base["cli_source"]) ? $base["cli_source"] : 0;
                //print_r($cli_source);die;
				//获知渠道
				$channel = isset($base["channel"]) ? $base["channel"] : 0;
                
				//性别
				$cli_gender = isset($base["cli_gender"]) ? $base["cli_gender"] : 0;
				
				//学历
				$cli_edu_detail = isset($base["cli_edu"]) ? $base["cli_edu"] : 0;
				
				//民族
				$cli_nation = isset($base["cli_nation"]) ? $base["cli_nation"] : 0;
				
				//客户通讯地址
                $cli_location = isset($base["cli_location"]) ? $base["cli_location"] : 0;
				//客户标签
				$cli_tag = isset($base["tag"]) ? $base["tag"] : "";
               // print_r(cli_tag);die;
				//婚礼日期
				$wed_date_sure = isset($wed_info["wed_date_sure"]) ? $wed_info["wed_date_sure"] : 0;
                if($wed_date_sure == 1)
                {
                    //if(!isset($wed_info["wed_date"]) || empty($wed_info["wed_date"])){
                      //  return failure('请填写婚礼日期');
                    //}
                    $wed_date = $wed_info["wed_date"];
                }else
                {
                    $wed_date = isset($wed_info["wed_date"]) ? $wed_info["wed_date"] : "";
                }
				//婚礼地点
                $wed_location = isset($wed_info["wed_location"]) ? $wed_info["wed_location"] : 0;
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
				
				//商家信息id
				$id_arr = $wedplanner["bsnesids"];
				//print_r($id_arr);
				if(empty($id_arr)){
					 return failure("请选择商家信息");
				}
					$shoper_ids = array();
					foreach($id_arr as $key => $ids)
					{
					//	if($base['mode'] == 2 && count($ids) > 1){
						//	return failure('指定商家只能选定一个策划师');
						//}
						if(!empty($ids))
						{
							foreach ($ids as $id)
							{
								$shoper_ids[$key][] = array(
									"id" => $id
								);
								$sms_user[] = $id;
							}
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
				//提交需求数组
				$entry = array(
					"base" => array(
						"status" => isset($inputs["submitype"]) ? $inputs["submitype"] : 3,
						"mode" => isset($base["mode"]) ? $base["mode"] : 0,
						"counselor_uid" => isset($base["counselor_uid"]) ? $base["counselor_uid"] : 0,
						"cli_name" => isset($base["cli_name"]) ? $base["cli_name"] : "",
						"cli_source" => $cli_source,
						"channel" => $channel,
						"cli_gender" => $cli_gender,
						"cli_birth" => isset($base["cli_birth"]) ? $base["cli_birth"] : "",
						"cli_edu" => $cli_edu_detail,
						"cli_mobile" => isset($base["cli_mobile"]) ? $base["cli_mobile"] : "",
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
                        "cli_hope_contect_way" => $cli_hope_contect_way,
					),
					"wed_info" => array(
						"type" => 1,
						"wed_date_sure" => $wed_date_sure,
						"wed_date" => $wed_date,
						"wed_location" => $wed_location,
						"wed_place" => isset($wed_info["wed_place"]) ? $wed_info["wed_place"] : "",
						"wed_party_type" => isset($wed_info["wed_party_type"]) ? $wed_info["wed_party_type"] : "",
						"people_num" => isset($wed_info["people_num"]) ? $wed_info["people_num"] : "",
						"budget" => isset($wed_info["budget"]) ? $wed_info["budget"] : "",
					),
					"wedplanners" => array(
						array(
							'question' => '关于婚礼，以下哪种描述更符合您的要求？',
							'word' => '自我描述',
							'alias' => 'description',
							'datatype' => 'radio',
							'answer' => isset($wedplanner["description"]) ? $wedplanner["description"] : "",
						  ),
						array(
							'question' => '您期待自己的婚礼现场是？',
							'word' => '风格偏好',
							'alias' => 'style',
							'datatype' => 'radio',
							'answer' => isset($wedplanner["style"]) ? $wedplanner["style"] : "",
						  ),
						array(
							'question' => '您希望自己的婚礼现场的主色系是？',
							'word' => '色系偏好',
							'alias' => 'color',
							'datatype' => 'radio',
							'answer' => $color_detail,
						  ),
						array(
							'question' => '请选择2-5个词描述您理想的婚礼：',
							'word' => '婚礼形容词',
							'alias' => 'ideal',
							'datatype' => 'checkbox',
							'answer' => $ideal_detail,
						  ),
						array(
							'question' => '在婚礼筹备时，您希望重点投入的是？',
							'word' => '婚礼希望重点投入',
							'alias' => 'emphasis',
							'datatype' => 'checkbox',
							'answer' => $emphasis_detail,
						  ),
						array(
							'question' => '在婚礼过程中，您最看重的是？',
							'word' => '婚礼过程最看重',
							'alias' => 'importance',
							'datatype' => 'radio',
							'answer' => isset($wedplanner["importance"]) ? $wedplanner["importance"] : "",
						  ),
						array(
							'question' => '请描述您的喜好，以便策划师更好地了解您并为您提供更满意的婚礼方案：',
							'word' => '更多描述',
							'alias' => 'moreinfo',
							'datatype' => 'textarea',
							'answer' => isset($wedplanner["moreinfo"]) ? $wedplanner["moreinfo"] : "",
						  ),
						array(
							'question' => '如果您在易结上看到了喜欢的婚礼案例，请输入链接地址：',
							'word' => '心仪案例',
							'alias' => 'opus',
							'datatype' => 'text',
							'answer' => $opus_detail,
						  ),
					),
					"shopper_ids" => $shoper_ids
				);
               // print_r($entry);die;
				$data['json_content'] = json_encode($entry,true);
				//保存草稿
				$data['create_time'] = date("Y:m:d H:i:s");
				$rows = $this->erp_conn->insert('ew_demand_draft', $data);
                $id = $this->erp_conn->insert_id();
				if(empty($rows))
				{
					return failure("复制失败");     
				}else{
					//return success($infos);
                    echo json_encode(array('result' => 'succ','info' => '复制成功','id'=>$id));exit;
				}

			 
		}else{
		  //单项式复制
		          $base_arr = json_decode($inputs["baseinfo"], TRUE);
				$base = array();
				@array_walk($base_arr, function($item) use(&$base){
					$base = array_merge_recursive($base, $item);
				});
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

				//判断手机号是否存在客户档案
				$phone = $base["cli_mobile"];
				if(!empty($phone))
				{
					$record_info = $this->record->getRecordByPhone($phone);
					if(empty($record_info))
					{
						$record_id = $this->record->addRecord($base);
					}
				}

				//处理需求数组
				//客户来源
				$cli_source = isset($base["cli_source"]) ? $base["cli_source"] : 0;
				//获知渠道
				$channel = isset($base["channel"]) ? $base["channel"] : 0;
				//性别
				$cli_gender = isset($base["cli_gender"]) ? $base["cli_gender"] : 0;

				//学历
				$cli_edu_detail = isset($base["cli_edu"]) ? $base["cli_edu"] : 0;
               
				//民族
				$cli_nation = isset($base["cli_nation"]) ? $base["cli_nation"] : 0;
				
				//客户通讯地址
				$cli_location = isset($base["cli_location"]) ? $base["cli_location"] : 0;
				
				//客户标签
				$cli_tag = isset($base["tag"]) ? $base["tag"] : "";
				//婚礼日期
				$wed_date_sure = isset($wed_info["wed_date_sure"]) ? $wed_info["wed_date_sure"] : 0;
                if($wed_date_sure == 1)
                {
                    if(!isset($wed_info["wed_date"]) || empty($wed_info["wed_date"])){
                        return failure('请填写婚礼日期');
                    }
                    $wed_date = $wed_info["wed_date"];
                }else
                {
                    $wed_date = isset($wed_info["wed_date"]) ? $wed_info["wed_date"] : "";
                }

				//婚礼地点   
				$wed_location = isset($wed_info["wed_location"]) ? $wed_info["wed_location"] : 0;
				

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
				//商家信息id
				$id_arr = json_decode($inputs['shopperids'],TRUE);

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
				 if(empty($id_arr)){
					 return failure("请选择商家信息");
				 }
				$shoper_ids = array();
				if(isset($id_arr['wedmaster'])){
					//if($base['mode'] == 2 && count($id_arr['wedmaster']) > 1){
					//	return failure('指定商家只能选定一个主持人');
					//}
					foreach($id_arr['wedmaster'] as $key => $ids)
					{
						$shoper_ids['wedmaster'][] = array('id' => $ids);
						//给发短信拼接用户
						$sms_user[] = $ids;
					}
				}

				if(isset($id_arr['makeup'])){
					//if($base['mode'] == 2 && count($id_arr['makeup']) > 1){
					//	return failure('指定商家只能选定一个化妆师');
					//}
					foreach($id_arr['makeup'] as $key => $ids)
					{
						$shoper_ids['makeup'][] = array('id' => $ids);
						//给发短信拼接用户
						$sms_user[] = $ids;
					}
				}
				if(isset($id_arr['wedphotoer'])){
					//if($base['mode'] == 2 && count($id_arr['wedphotoer']) > 1){
					//	return failure('指定商家只能选定一个摄影师');
					//}
					foreach($id_arr['wedphotoer'] as $key => $ids)
					{
						//给发短信拼接用户
						$sms_user[] = $ids;
						$shoper_ids['wedphotoer'][] = array('id' => $ids);
						// foreach($wedphotoer_service as $kws => $vws)
						// {
						//     $shoper_ids['wedphotoer'][] = array('id' => $ids,'service_type' => $vws);
						// }
					}
				}
				if(isset($id_arr['wedvideo'])){
					//if($base['mode'] == 2 && count($id_arr['wedvideo']) > 1){
					//	return failure('指定商家只能选定一个摄像师');
					//}
					foreach($id_arr['wedvideo'] as $key => $ids)
					{
						//给发短信拼接用户
						$sms_user[] = $ids;
						$shoper_ids['wedvideo'][] = array('id' => $ids);
					}
				}
				if(isset($id_arr['sitelayout'])){
					//if($base['mode'] == 2 && count($id_arr['sitelayout']) > 1){
					//	return failure('指定商家只能选定一个场地布置');
					//}
					foreach($id_arr['sitelayout'] as $key => $ids)
					{
						$shoper_ids['sitelayout'][] = array('id' => $ids);
						//给发短信拼接用户
						$sms_user[] = $ids;
					}
				}

				   //if(empty($shoper_ids)){
					//	 return failure("请选择商家信息");
					//}
					if(empty($shoper_ids)){
						 $sms_user[] = array(0);
					}

                        //期望联系方式
               $way = isset($base["cli_hope_contect_way"]) ? $base["cli_hope_contect_way"] : array();
               if(!is_array($way))
               {
                    $way = array($way);
               }
               $cli_way = implode(",", $way);
               $cli_hope_contect_way = rtrim($cli_way, ",");
				$entry = array(
					"base" => array(
						"status" => isset($inputs["submitype"]) ? $inputs["submitype"] : 3,
						"mode" => isset($base["mode"]) ? $base["mode"] : 0,
						"counselor_uid" => isset($base["counselor_uid"]) ? $base["counselor_uid"] : 0,
						"cli_name" => isset($base["cli_name"]) ? $base["cli_name"] : "",
						"cli_source" => $cli_source,
						"channel" => $channel,
						"cli_gender" => $cli_gender,
						"cli_birth" => isset($base["cli_birth"]) ? $base["cli_birth"] : "",
						"cli_edu" => $cli_edu_detail,
						"cli_mobile" => isset($base["cli_mobile"]) ? $base["cli_mobile"] : "",
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
                        "cli_hope_contect_way" => $cli_hope_contect_way,
					),
					"wed_info" => array(
						"type" => 2,
						"wed_date_sure" => $wed_date_sure,
						"wed_date" => $wed_date,
						"wed_location" => $wed_location,
						"wed_place" => isset($wed_info["wed_place"]) ? $wed_info["wed_place"] : "",
						"wed_party_type" => isset($wed_info["wed_party_type"]) ? $wed_info["wed_party_type"] : "",
					),
					"shopper_ids" => $shoper_ids
				);
				if(isset($multwed["wedmaster_amount"]))
				{
					$entry["wedmaster"] = array(
						array(
							'question' => '预定主持人，您的预算是？',
							'word' => '预算',
							'alias' => 'amount',
							'datatype' => 'radio',
							'answer' => isset($multwed["wedmaster_amount"]) ? $multwed["wedmaster_amount"] : "",
						),
						array(
							'question' => '对于主持人及其服务，您是否还有其他的要求或喜好？',
							'word' => '备注要求',
							'alias' => 'remark',
							'datatype' => 'textarea',
							"answer" => isset($multwed["wedmaster_remark"]) ? $multwed["wedmaster_remark"] : "",
						),
					);
					//如果是指定商家没有此项
					if(isset($base["mode"]) && $base["mode"] == 1){
						$entry["wedmaster"][] = array(
							'question' => '对于为您服务的婚礼主持人，您的要求是？',
							'word' => '对人的要求',
							'alias' => 'people',
							'datatype' => 'radio',
							'answer' => $wedmaster_people,
						);
					}
				}
				if(isset($multwed["makeup_amount"]))
				{
					$entry["makeup"] = array(
						array(
							'question' => '预定化妆师，您的预算是？',
							'word' => '预算',
							'alias' => 'amount',
							'datatype' => 'radio',
							'answer' => isset($multwed["makeup_amount"]) ? $multwed["makeup_amount"] : "",
						),

						array(
							'question' => '您计划婚礼当天选几套造型？',
							'word' => '造型需求',
							'alias' => 'modeling',
							'datatype' => 'radio',
							'answer' => isset($multwed["modeling"]) ? $multwed["modeling"] : "",
						),
						array(
							'question' => '对于化妆师及其服务，您是否还有其他的要求或喜好？',
							'word' => '备注要求',
							'alias' => 'remark',
							'datatype' => 'textarea',
							'answer' => isset($multwed["makeup_remark"]) ? $multwed["makeup_remark"] : "",
						),
						 array(
							'question' => '对于为您服务的婚礼化妆师，您的要求是？',
							'word' => '备注要求',
							'alias' => 'people',
							'datatype' => 'radio',
							'answer' => isset($multwed["makeup_people"]) ? $multwed["makeup_people"] : "",
						),
					);
					//如果是指定商家没有此项
					if(isset($base["mode"]) && $base["mode"] == 1){
						$entry["makeup"][] = array(
							'question' => '对于您服务的化妆师，您的要求是？',
							'word' => '对人的要求',
							'alias' => 'people',
							'datatype' => 'radio',
							'answer' => isset($multwed["makeup_people"]) ? $multwed["makeup_people"] : "",
						);
					}
				}
				if(isset($multwed["wedphotoer_service"]))
				{
					$entry["wedphotoer"] = array(
						array(
							'question' => '对于摄影师及其服务，您是否还有其他的要求或喜好？',
							'word' => '备注要求',
							'alias' => 'remark',
							'datatype' => 'textarea',
							'answer' => isset($multwed["wedphotoer_remark"]) ? $multwed["wedphotoer_remark"] : "",
						)
					);

					if(in_array("婚纱照拍摄", $photoer_service))
					{
						$entry["wedphotoer"][] = array(
							'question' => '您需要的摄影服务是？',
							'word' => '服务选择',
							'alias' => 'service',
							'datatype' => 'checkbox',
							'service_type' => "hspz",
							'answer' => "婚纱照拍摄",
						);
						//当选择选择婚纱照拍摄才会有此项
						$entry["wedphotoer"][] = array(
							'question' => '对于婚纱照拍摄，您的预算是？',
							'word' => '婚纱照拍摄预算',
							'alias' => 'hspz_amount',
							'datatype' => 'radio',
							'answer' => isset($multwed["hspz_amount"]) ? $multwed["hspz_amount"] : "",
						);
					}
					if(in_array("婚礼当天跟拍", $photoer_service))
					{
						$entry["wedphotoer"][] = array(
							'question' => '您需要的摄影服务是？',
							'word' => '服务选择',
							'alias' => 'service',
							'datatype' => 'checkbox',
							'service_type' => "hlgp",
							'answer' => "婚礼当天跟拍",
						);
						//选择婚礼跟拍才会有此项
						$entry["wedphotoer"][] = array(
							'question' => '对于婚礼当天跟拍，您的预算是？',
							'word' => '跟拍预算',
							'alias' => 'hlgp_amount',
							'datatype' => 'radio',
							'answer' => isset($multwed["wedphotoer_hlgp_amount"]) ? $multwed["wedphotoer_hlgp_amount"] : "",
						);
						//当选择婚礼跟拍才会有此项
						$entry["wedphotoer"][] = array(
							'question' => '您希望选择哪种跟拍方案？',
							'word' => '跟拍方案',
							'alias' => 'hlgp_scheme',
							'datatype' => 'radio',
							'answer' => isset($multwed["wedphotoer_hlgp_scheme"]) ? $multwed["wedphotoer_hlgp_scheme"] : "",
						);
					}
					//如果是指定商家没有此项
					if(isset($base["mode"]) && $base["mode"] == 1)
					{
						$entry["wedphotoer"][] = array(
							'question' => '对于您服务的摄影师，您的要求是？',
							'word' => '对人的要求',
							'alias' => 'people',
							'datatype' => 'radio',
							'answer' => isset($multwed["wedphotoer_people"]) ? $multwed["wedphotoer_people"] : "",
						);
					}
				}
				if(isset($multwed["wedvideo_service"]))
				{
					$entry["wedvideo"] = array(
						array(
							'question' => '对于摄像师及其服务，您是否还有其他的要求或喜好？',
							'word' => '备注要求',
							'alias' => 'remark',
							'datatype' => 'textarea',
							'answer' => isset($multwed["wedvideo_remark"]) ? $multwed["wedvideo_remark"] : "",
						)
					);

					if(in_array("婚礼前的爱情微电影", $video_service))
					{
						$entry["wedvideo"][] = array(
							'question' => '您需要的摄像服务是？',
							'word' => '服务选择',
							'alias' => 'service',
							'datatype' => 'checkbox',
							'service_type' => "hspz",
							'answer' => "婚礼前的爱情微电影",
						);
						//选择爱情微电影才会有此项
						$entry["wedvideo"][] = array(
							'question' => '对于爱情微电影，您的预算是？',
							'word' => '微电影预算',
							'alias' => 'wdy_amount',
							'datatype' => 'radio',
							'answer' => isset($multwed["wdy_amount"]) ? $multwed["wdy_amount"] : "",
						);
					}
					if(in_array("婚礼当天跟拍", $video_service))
					{
						$entry["wedvideo"][] = array(
							'question' => '您需要的摄影服务是？',
							'word' => '服务选择',
							'alias' => 'service',
							'datatype' => 'checkbox',
							'service_type' => "hlgp",
							'answer' => "婚礼当天跟拍",
						);
						//选择婚礼跟拍才会有此项
						$entry["wedvideo"][] = array(
							'question' => '对于婚礼当天跟拍，您的预算是？',
							'word' => '跟拍预算',
							'alias' => 'hlgp_amount',
							'datatype' => 'radio',
							'answer' => isset($multwed["wedvideo_hlgp_amount"]) ? $multwed["wedvideo_hlgp_amount"] : "",
						);
						//当选择婚礼跟拍才会有此项
						$entry["wedvideo"][] = array(
							'question' => '您希望选择哪种跟拍方案？',
							'word' => '跟拍方案',
							'alias' => 'hlgp_scheme',
							'datatype' => 'radio',
							'answer' => isset($multwed["wedvideo_hlgp_scheme"]) ? $multwed["wedvideo_hlgp_scheme"] : "",
						);
					}
					//如果是指定商家没有此项
					if(isset($base["mode"]) && $base["mode"] == 1)
					{
						$entry["wedvideo"][] = array(
							'question' => '对于为您服务的摄像师，您的要求是？',
							'word' => '对人的要求',
							'alias' => 'people',
							'datatype' => 'radio',
							'answer' => isset($multwed["wedvideo_people"]) ? $multwed["wedvideo_people"] : "",
						);
					}
				}
				if(isset($multwed["sitelayout_amount"]))
				{
					$entry["sitelayout"] = array(
						array(
							'question' => '对于婚礼现场的场地布置，您的预算是？',
							'word' => '预算',
							'alias' => 'amount',
							'datatype' => 'radio',
							'answer' => isset($multwed["sitelayout_amount"]) ? $multwed["sitelayout_amount"] : "",
						),
						array(
							 'question' => '您期待自己的婚礼现场是？',
							'word' => '风格偏好',
							'alias' => 'style',
							'datatype' => 'radio',
							'answer' => isset($multwed["style"]) ? $multwed["style"] : "",
						),
						array(
							'question' => '您希望自己的婚礼现场的主色系是？',
							'word' => '色系偏好',
							'alias' => 'color',
							'datatype' => 'radio',
							'answer' => $color_detail,
						),
						array(
							'question' => '请选择2-5个词描述您理想的婚礼：',
							'word' => '婚礼形容词',
							'alias' => 'ideal',
							'datatype' => 'checkbox',
							'answer' => $ideal_detail,
						),
						array(
							'question' => '对于场地布置及其服务，您是否还有其他的要求或喜好？',
							'question_appoint' => '对于我的服务，您是否还有其他的要求或喜好？',
							'word' => '备注要求',
							'alias' => 'remark',
							'datatype' => 'textarea',
							'answer' => isset($multwed["sitelayout_remark"]) ? $multwed["sitelayout_remark"] : "",
						),
					);
				}
				//print_r($entry);die;
				$data['json_content'] = json_encode($entry,true);
				//保存草稿
				$data['create_time'] = date("Y:m:d H:i:s");
				$rows = $this->erp_conn->insert('ew_demand_draft', $data);
                $id = $this->erp_conn->insert_id();
				if(empty($rows))
				{
					return failure("复制失败");
				}else{

					//return success($infos);
                    echo json_encode(array('result' => 'succ','info' => '复制成功','id'=>$id));exit;

				}


		}


    }
	
    //需求对应商家列表
    public function shoper_review()
    {
        $inputs = $this->input->get();
        $id = $this->input->get('id');
        $status = $this->input->get('shoper_status'); //带投标状态
        $nickname= $this->input->get('nickname');// 昵称
        $time_21 = $this->input->get('time_21');// 投标时间1
        $endtime_21 = $this->input->get('endtime_21');// 投标时间2
        $time_46 = $this->input->get('time_46');//出方案时间1
        $endtime_46 = $this->input->get('endtime_46');// 出方案时间2
        $phone = $this->input->get('phone');// 手机号
        $studio_name = $this->input->get('studio_name');//商铺名称
        //分页
        //$page = $this->input->get("page") ? $this->input->get("page") : 1;
       // $page = $page < 1 ? 1 : $page;
        //$pagesize = $this->input->get("pagesize");
       // $offset = ($page-1)*$pagesize;
        $where ='';
        $where.="1=1 ";
        //拼接搜索条件
        //$where=array();
        if(!empty($id)){//需求id
            $where.=" and ew_demand_content.id=".$id;
        }
        if(!empty($status)){//投标状态
            $where.=" and ew_demand_order.status=".$status;
        }
        if(!empty($nickname)){//昵称
            $where.=" and ew_users.nickname like '%".$nickname."%'";
        }
        
        if(!empty($time_21)&&!empty($endtime_21)){//投标时间都不为空
            $where.=" and ew_demand_order.time_21 >="."'$time_21'"." and ew_demand_order.time_21 <="."'$endtime_21'";
        }
        
        if(!empty($time_21)&&empty($endtime_21)){//投标时间1不为空
            $where.=" and ew_demand_order.time_21 >="."'$time_21'";
        }
        
        if(empty($time_21)&&!empty($endtime_21)){//投标时间2不为空
            $where.=" and ew_demand_order.time_21 <="."'$endtime_21'";
        }
        //出方案时间
        if(!empty($time_46)&&!empty($endtime_46)){//出方案时间都不为空
            $where.=" and ew_demand_order.time_46 >="."'$time_46'"." and ew_demand_order.time_46 <="."'$endtime_46'";
        }
        if(!empty($time_46)&&empty($endtime_46)){//出方案时间1不为空
            $where.=" and ew_demand_order.time_46 >="."'$time_46'";
        }
        if(empty($time_46)&&!empty($endtime_46)){//出方案时间2不为空
            $where.=" and ew_demand_order.time_46 <="."'$endtime_46'";
        }        
        if(!empty($phone)){//手机号
            $where.=" and ew_users.phone like '%".$phone."%'";
        }
        if(!empty($studio_name)){//商铺名称
            $where.=" and ew_user_shopers.studio_name like '%".$studio_name."%'";
        }   
        //地区条件拼接
        $country = isset($inputs['country_dlgs']) ? $inputs['country_dlgs'] : '';//国家
        $province = isset($inputs['province_dlgs']) ? $inputs['province_dlgs'] : '';//城市
        $city = isset($inputs['city_dlgs']) ? $inputs['city_dlgs'] : '';//地区
     
        if(!empty($country)&&!empty($province)&&!empty($city)){
			$where.="  and ew_user_shopers.address="."'$country".","."$province".","."$city'";
		}
		if(!empty($country)&&!empty($province)&&empty($city)){
			$where.="  and ew_user_shopers.address="."'$country".","."$province'";
		}
		if(!empty($country)&&empty($province)&&empty($city)){
			$where.="  and ew_user_shopers.address="."'$country'";
		}

        //echo $where;
        $arr = "SELECT ew_user_shopers.uid,ew_demand_content.id,ew_demand_order.order_step_end,ew_demand_order.shopper_alias,ew_demand_order.id as orderid,ew_user_shopers.address,ew_demand_order.recommend_letter,ew_demand_order.status,ew_users.nickname,ew_demand_order.time_21,ew_demand_order.time_46,ew_users.phone,ew_user_shopers.studio_name from ew_user_shopers join ew_users on ew_user_shopers.uid = ew_users.uid join ew_demand_order on ew_demand_order.shopper_user_id = ew_users.uid join ew_demand_content on ew_demand_content.id = ew_demand_order.content_id where ".$where."";
        $lista=$this->ew_conn->query($arr);
        //echo $arr;die;
        $list = $lista->result_array();
        //获取条数
        $count = "select count(*) from ew_user_shopers join ew_users on ew_user_shopers.uid = ew_users.uid join ew_demand_order on ew_demand_order.shopper_user_id = ew_users.uid join ew_demand_content on ew_demand_content.id = ew_demand_order.content_id where ".$where;
        $totala=$this->ew_conn->query($count);
        $totalaa = $totala->result_array(); 
        $total = $totalaa[0]['count(*)'];
        $infos = array();
        foreach ($list  as $key => $value) {
            $infos[$key]['id']               = $value['orderid']; //用户商家表id

            $infos[$key]['uid']               = $value['uid']; //用户uid
            $infos[$key]['shopper_alias']               = $value['shopper_alias']; //类型别名

            $infos[$key]['order_step_end']    = $value['order_step_end']; //订单关闭原因
            $infos[$key]['status_txt']        = $value['status']; //投标状态
            $infos[$key]['status']        = $value['status']; //投标状态
            $infos[$key]['nickname']          = $value['nickname']; //名称
            $infos[$key]['address']          = $value['address']; //地址
            $infos[$key]['recommend_letter']  = $value['recommend_letter']; //自荐信
            if($value['status'] >= 21 && $value['status'] < 46)
            {
                $infos[$key]['time_21']           = $value['time_21']=='0000-00-00 00:00:00' ? '' : $value['time_21']; //投标时间
                $infos[$key]['time_46']           = ''; //出方案时间
            }elseif($value['status'] >= 46)
            {
                $infos[$key]['time_21']           = $value['time_21']=='0000-00-00 00:00:00' ? '' : $value['time_21']; //投标时间
                $infos[$key]['time_46']           = $value['time_46']=='0000-00-00 00:00:00' ? '' : $value['time_46']; //出方案时间
            }
            $infos[$key]['phone']             = $value['phone']; //手机
            $infos[$key]['studio_name']       = $value['studio_name']; //商铺名称
            if($this->_IsOrderSigned($value['orderid']))
            {
                $ishowsign = 1;
            }else{

                $ishowsign = 0;
            }
            $infos[$key]['ishowsign']       = $ishowsign; //订单签约状态


            //判断订单状态
            if($infos[$key]['status_txt']==11||$infos[$key]['status_txt']==1){
                 $infos[$key]['status_txt']="待投标";
            }
            if($infos[$key]['status_txt']==21){
                 $infos[$key]['status_txt']="已投标，待审核";
            }
            if($infos[$key]['status_txt']==31){
                 $infos[$key]['status_txt']="已投标，待初选";
            }
            if($infos[$key]['status_txt']==41){
                 $infos[$key]['status_txt']="初选中标，待出方案";
            }
            if($infos[$key]['status_txt']==46){
                 $infos[$key]['status_txt']="已出方案，待确认";
            }
            if($infos[$key]['status_txt']==51){
                 $infos[$key]['status_txt']="已中标";
            }
            if($infos[$key]['status_txt']==61){
                 $infos[$key]['status_txt']="订单完成";
            }
            if($infos[$key]['status_txt']==99){
                 $infos[$key]['status_txt']="未中标";
            }
            
            //判断订单关闭原因
            if($infos[$key]['order_step_end']==0){
                 $infos[$key]['order_step_end']="订单成功";
            }
            if($infos[$key]['order_step_end']==1){
                 $infos[$key]['order_step_end']="初选完成时未被选中的订单（商家响应）";
            }
            if($infos[$key]['order_step_end']==2){
                 $infos[$key]['order_step_end']="确定商家时未被选中的订单";
            }
            if($infos[$key]['order_step_end']==11){
                 $infos[$key]['order_step_end']="需求审核时终止";
            }
            if($infos[$key]['order_step_end']==12){
                 $infos[$key]['order_step_end']="商家投标时终止";
            }
            if($infos[$key]['order_step_end']==13){
                 $infos[$key]['order_step_end']="初选中标时终止";
            }
            if($infos[$key]['order_step_end']==14){
                 $infos[$key]['order_step_end']="招投标完成时终止";
            }
        }
        $info = array(
            'total' => $total,
            'rows' => $infos
        );
        return success($info);  
    }

    //添加需求订单日志
    public function addOrderLog($id,$demand_code,$order_id = '',$order_code = '',$action = '',$comment = ''){
        $this->order_log->demandlog($id,$order_id,$order_code,$demand_code,$action,$comment);
    }

    //检查商家的签约情况
    private function _IsOrderSigned($order_id)
    {
        $contract = $this->erp_conn->where('order_id',$order_id)->get('demand_contract')->row_array();
        return empty($contract) ? 0 : 1;
    }

}
