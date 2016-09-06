<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 商机管理公用方法
 * 
 */

class Common_model extends MY_Model{

    public function __construct(){
        parent::__construct();
    }
	
	/**
	 * 获取主站商家列表
	 * @param type $params  条件数组
	 * @return type
	 */
	public function shoperInfo($params)
	{
        $ret = $this->curl->post($this->config->item('ew_domain').'erp/business/planner-list', $params);
        $ret = json_decode($ret,true);

		$info = !empty($ret["info"]) ? $ret["info"] : array();

		$count = isset($info["total"]) ? $info["total"] : 0;
		$list = isset($info["rows"]) ? $info["rows"] : array();
//		$sql_con = "select user_shopers.uid, users.nickname, users.phone, user_shopers.realname, user_shopers.grade, user_shopers.address, user_shopers.studio_name from ew_user_shopers as user_shopers join ew_users as users on users.uid = user_shopers.uid where users.dostatus=2 and user_shopers.site_id = 1 ";
//		
//		//只取策划师
//		$sql_con .= ' and user_shopers.serves like "%1435%"';
//		
//		//拼接sql条件
//		if(isset($params["uid"]) && is_array($params["uid"])) // 商家id数组
//		{
//			$uids = implode(",", $params["uid"]);
//			if(empty($uids))
//			{
//				$sql_con .= "and 1<>1";
//			}else
//			{
//				$sql_con .= ' and user_shopers.uid in ('. $uids .')';
//			}
//		}
//		
//		if(!empty($params["grade"])) //等级
//		{
//			$sql_con .= ' and user_shopers.grade = '.$params['grade'];
//		}
//		
//		if(!empty($params['address']))  //地址
//		{
//            $sql_con .= ' and user_shopers.address like "%1,'.$params['address'].'%"';
//        }
//		
//        if(!empty($params['keyword'])) //关键字
//		{
//            $sql_con .= ' and (users.nickname like "%'.$params['keyword'].'%" or users.phone like "%'.$params['keyword'].'%" or user_shopers.studio_name like "%'.$params['keyword'].'%")';
//        }
//		
//		//分页
//		if($params['page'] == 1 || $params['page'] < 1 )
//        {
//            $offset = 1;
//        }else{
//            $offset = ($params['page'] -1)*$params['pagesize'];
//        }
//
//        if($offset == 1){
//            $sql_limit = " limit ".$params['pagesize'];
//        }else{
//            $sql_limit = " limit ".$offset.",".$params['pagesize'];
//        }
//		
//		//查询结果
//		$list = $this->ew_conn->query($sql_con.$sql_limit)->result_array();
//		$count = $this->ew_conn->query($sql_con)->num_rows();
		
		return array('total' => $count,'rows' => $list);
	}
	
	/**
	 * 取商家等级列表
	 * @return type
	 */
	public function getShoperGrade()
	{
		$ret = $this->curl->post($this->config->item('ew_domain').'/erp/business/planner-grade', array());
        $ret = json_decode($ret,true);
		
		$result = !empty($ret["info"]) ? $ret["info"] : array();
		
//		$result = $this->ew_conn
//				->select("id, grade_name, grade_type")
//				->where("grade_type", "wedplanners")
//				->get("ew_certificate_grade")
//				->result_array();
		
		return $result;
	}
	
	/**
	 * 判断商机基本信息是否完善
	 */
	public function is_perfect($bid = 0)
	{
		$business = $this->erp_conn->where("id", $bid)->get("business")->row_array();
		$business_extra = $this->erp_conn->where("bid", $bid)->get("business_extra")->row_array();
		
		$flag = 1;
		
		if(empty($business["source"]) || empty($business["source_note"])) //商机来源与备注不能为空
		{
			$flag = 0;
		}
		
		if(empty($business["status"]))  //商机状态不能为空
		{
			$flag = 0;
		}
		
		if(empty($business["usertype"])) //客户类型不能为空
		{
			$flag = 0;
		}
		
		if(empty($business["username"])) //客户姓名不能为空
		{
			$flag = 0;
		}
		
		if(empty($business["userpart"])) //客户身份不能为空
		{
			$flag = 0;
		}
		
		if(empty($business["mobile"])) //客户手机不能为空
		{
			$flag = 0;
		}
		
		if(empty($business["ordertype"])) //商机类型不能为空
		{
			$flag = 0;
		}
		
		if(empty($business["wed_date"]) && empty($business_extra["weddate_note"]))  //婚礼日期不能为空
		{
			$flag = 0;
		}
		
		if(empty($business_extra["location"])) //婚礼地点不能为空
		{
			$flag = 0;
		}
		
		if(empty($business_extra["wed_place"]) && empty($business_extra["wed_place_area"])) //婚礼场地不能为空
		{
			$flag = 0;
		}
		
		if(empty($business_extra["findtype"]) || empty($business_extra["findnote"])) //找商家方式及备注不能为空
		{
			$flag = 0;
		}
		
		return $flag;
		
	}
}