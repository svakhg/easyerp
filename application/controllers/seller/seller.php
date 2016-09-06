<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("content-type:text/html;charset=utf-8");
class Seller extends App_Controller {
	public function __construct(){
		parent::__construct();
        $this->load->model('baidupush/baidupush');
	}
	//显示视图
	public function index(){
	

		$counta = "select count(*) from ew_demand_order left join ew_demand_content on ew_demand_content.id=ew_demand_order.content_id left join ew_user_shopers on ew_demand_order.shopper_user_id=ew_user_shopers.uid left join ew_users on ew_user_shopers.uid=ew_users.uid where ew_demand_order.status = 21";
		$totala=$this->ew_conn->query($counta);
		$totalaa = $totala->result_array();	
		
		$countb = "select count(*) from ew_demand_order left join ew_demand_content on ew_demand_content.id=ew_demand_order.content_id left join ew_user_shopers on ew_demand_order.shopper_user_id=ew_user_shopers.uid left join ew_users on ew_user_shopers.uid=ew_users.uid where ew_demand_order.status >= 31 and  ew_demand_order.status < 99";
		$totala=$this->ew_conn->query($countb);
		$totalbb = $totala->result_array();	
		
		$countc = "select count(*) from ew_demand_order left join ew_demand_content on ew_demand_content.id=ew_demand_order.content_id left join ew_user_shopers on ew_demand_order.shopper_user_id=ew_user_shopers.uid left join ew_users on ew_user_shopers.uid=ew_users.uid where ew_demand_order.status = 99";
		$totala=$this->ew_conn->query($countc);
		$totalcc = $totala->result_array();	

		//print_r($totalaa);die;
		$total[] = $totalaa[0]['count(*)'];
		$total[] = $totalbb[0]['count(*)'];
		$total[] = $totalcc[0]['count(*)'];
		//print_r($total);die;
		$data['list'] = $total;
	    $this->load->view('seller/list_seller',$data);
    }
	//获取自荐信列表
	public function list_seller()
	{
		 //分页
		$page = $this->input->get("page") ? $this->input->get("page") : 1;
		$page = $page < 1 ? 1 : $page;
		$pagesize = $this->input->get("pagesize");
		$offset = ($page-1)*$pagesize;
		//查询条件
		$where ='';
		$where.="1=1 ";
		if($this->input->get("cli_source")!="")
		{
			$where.=" and ew_demand_order.shopper_alias like '%".$this->input->get("cli_source") ."%'" ; 
		}
		if($this->input->get("condition_number")!="")
		{
			$where.=" and ew_demand_order.order_id like '%".$this->input->get("condition_number") ."%'" ; 
		}
		if($this->input->get("keywords")!="")
		{
			$where.= " and (";
			$where.= " ew_demand_order.order_id like '%".$this->input->get("keywords") ."%'";
			$where.= " or ew_demand_content.cli_name like '%".$this->input->get("keywords") ."%'";
			$where.= " or ew_demand_order.customer_phone like '%".$this->input->get("keywords") ."%'";
			$where.= " or ew_demand_order.customer_wechat like '%".$this->input->get("keywords") ."%'";
			$where.= ")";
		}	
		if($this->input->get("alia")=='ok')
		{
			$where.=" and ew_demand_order.status >=31 and ew_demand_order.status <99 " ; 
		}else if($this->input->get("alia")=='no')
		{
			$where.=" and ew_demand_order.status = 99 " ;
		}else if($this->input->get("alia")=='dai')
		{
			$where.=" and ew_demand_order.status = 21" ;
		}
		$arr = "select ew_demand_order.id,ew_demand_order.status,ew_demand_order.order_id,ew_demand_order.time_1,ew_demand_order.time_21,ew_demand_order.time_46,ew_demand_order.wish,ew_demand_order.shopper_alias,ew_demand_order.recommend_letter,ew_users.phone,ew_users.nickname,ew_user_shopers.uid,ew_user_shopers.info_source,ew_user_shopers.studio_name,ew_user_shopers.price,ew_user_shopers.recommends,ew_user_shopers.aboutme_detail,ew_user_shopers.website,ew_user_shopers.aboutme,ew_demand_content.channel,ew_demand_content.cli_name,ew_demand_content.wed_date,ew_demand_content.create_time from ew_demand_order left join ew_demand_content on ew_demand_content.id=ew_demand_order.content_id left join ew_user_shopers on ew_demand_order.shopper_user_id=ew_user_shopers.uid left join ew_users on ew_user_shopers.uid=ew_users.uid where ".$where." order by ew_demand_order.id desc limit ".$offset.",".$pagesize."";
		//print_r($arr);die();
		$lista=$this->ew_conn->query($arr);
		$list = $lista->result_array();
		//获取条数	
		//$total = $this->ew_conn->where($where)->count_all_results("ew_demand_order");
		$count = "select count(*) from ew_demand_order left join ew_demand_content on ew_demand_content.id=ew_demand_order.content_id left join ew_user_shopers on ew_demand_order.shopper_user_id=ew_user_shopers.uid left join ew_users on ew_user_shopers.uid=ew_users.uid where ".$where;
		$totala=$this->ew_conn->query($count);
		$totalaa = $totala->result_array();	
		//print_r($totalaa);die;
		$total = $totalaa[0]['count(*)'];	
		//echo $this->ew_conn->last_query();die;
		$infos = array();
		foreach ($list  as $key => $value)
		{
			$infos[$key]['id']               = $value['id']; //订单id
			$infos[$key]['status']           = $value['status']; //订单状态
			if($infos[$key]['status']==21){
			     $infos[$key]['status']="待审核";
			}else if($infos[$key]['status']>=31&&$infos[$key]['status']<99){
			     $infos[$key]['status']="审核通过";
			}else if($infos[$key]['status']==99){
			     $infos[$key]['status']="审核不通过";
			}
			$infos[$key]['nickname']         = $value['nickname']; //商家名称
			$infos[$key]['order_id']         = $value['order_id']; //订单编号
			$infos[$key]['phone']            = $value['phone']; //商家手机
			$infos[$key]['uid']              = $value['uid']; //商家ID
			$infos[$key]['time_1']           = $value['time_1']; //提交需求待审核的时间点
			$infos[$key]['time_21']          = $value['time_21']; //商家提交自荐信待审核的时间点
            $this->lang->load('date','chinese');
            $this->load->helper('date');
            $infos[$key]['now_old_time'] = compare_to_now($infos[$key]['time_21']);//距今时间
			if($infos[$key]['now_old_time']=="Unkown"){
			  $infos[$key]['now_old_time']="";
			}else{
			    $infos[$key]['now_old_time'] = compare_to_now($infos[$key]['time_21']);//距今时间
			}
			$infos[$key]['time_46']          = $value['time_46']; //商家提交自荐信待审核的时间点
			$infos[$key]['website']         = $value['website']; //婚礼网址，以，号隔开',
			$infos[$key]['wish']             = $value['wish']; //接单意愿
			$infos[$key]['studio_name']      = $value['studio_name']; //工作室名称或正式注册公司名称，个人为空
			$infos[$key]['price']            = $value['price'];       //报价 多少元起
			$infos[$key]['channel']          = $value['channel']; //获知渠道
			$infos[$key]['cli_name']         = $value['cli_name']; //客户姓名
			$infos[$key]['wed_date']         = $value['wed_date']; //婚礼日期(日期格式或者字符串)
			$infos[$key]['create_time']      = $value['create_time']; //需求创建时间
			$infos[$key]['info_source']      = $value['info_source']; //信息源（网站，微博，其他用，隔开）
			$infos[$key]['shopper_alias']    = $value['shopper_alias']; //商家类型  一站式 找主持人
			$infos[$key]['recommends']       = $value['recommends']; //商家推荐
			$infos[$key]['aboutme_detail']   = $value['aboutme_detail']; //关于我，最新的
			$infos[$key]['aboutme']          = $value['aboutme']; //关于我
			$infos[$key]['recommend_letter']    = json_decode($value['recommend_letter'],true); //自荐信 
    
			$infos[$key]['advantage']         = isset($infos[$key]['recommend_letter']['advantage']['value'])?($infos[$key]['recommend_letter']['advantage']['value']):"";
			$infos[$key]['experience']         = isset($infos[$key]['recommend_letter']['experience']['value'])?($infos[$key]['recommend_letter']['experience']['value']):"";
			$infos[$key]['opus']         = isset($infos[$key]['recommend_letter']['opus']['value'])?($infos[$key]['recommend_letter']['opus']['value']):"";
			$infos[$key]['file_url']         = isset($infos[$key]['recommend_letter']['file_url']['value'])?($infos[$key]['recommend_letter']['file_url']['value']):"";

			$infos[$key]['file_advise']         = isset($infos[$key]['recommend_letter']['file_advise']['value'])?($infos[$key]['recommend_letter']['file_advise']['value']):"";
		    $infos[$key]['file_name']         = isset($infos[$key]['recommend_letter']['file_name']['value'])?($infos[$key]['recommend_letter']['file_name']['value']):"";

			$infos[$key]['opus']         = isset($infos[$key]['recommend_letter']['opus']['value'])?($infos[$key]['recommend_letter']['opus']['value']):"";

			$infos[$key]['prices_service_type']         = isset($infos[$key]['recommend_letter']['prices']['value'][0]['service_type']['value'])?($infos[$key]['recommend_letter']['prices']['value'][0]['service_type']['value']):"";

			$infos[$key]['prices_price']         = isset($infos[$key]['recommend_letter']['prices']['value'][0]['price']['value'])?($infos[$key]['recommend_letter']['prices']['value'][0]['price']['value']):"";


			$infos[$key]['prices_service']         = isset($infos[$key]['recommend_letter']['prices']['value'][0]['service']['value'])?($infos[$key]['recommend_letter']['prices']['value'][0]['service']['value']):"";

			$infos[$key]['prices_reason']         = isset($infos[$key]['recommend_letter']['prices']['value'][0]['reason']['value'])?($infos[$key]['recommend_letter']['prices']['value'][0]['reason']['value']):"";

			$infos[$key]['prices_grabprice']         = isset($infos[$key]['recommend_letter']['prices']['value'][0]['grabprice']['value'])?($infos[$key]['recommend_letter']['prices']['value'][0]['grabprice']['value']):"";

		    //需求类型  
			if($infos[$key]['shopper_alias']=='wedplanners'){
				 $infos[$key]['shopper_alias']="一站式";
            }else{
				$infos[$key]['shopper_alias']="单项式";
		     }
		}
		$info = array(
            'total' => $total,
            'rows' => $infos
        );   
		return success($info);	   
	}
	//审核自荐信功能
	public function examine()
	{
		//获取字段
		$inputs = $this->input->post();
		$ids = $inputs["ids"];//修改记录的id
		if($ids <= 0)
		{
			return failure("参数错误");
		}
		if($inputs['flag']==1)
		{
			    //审核通过
				$init["status"] = 31;//审核通过status为31

				$order_id = explode(',', $ids);
				foreach($order_id as $v){
                     //添加需求日志
                     $did = $v;
                     $brr = $this->ew_conn->where('ew_demand_order.id',$did)->select('ew_demand_order.content_id,ew_demand_content.demand_id,ew_demand_order.order_id,ew_demand_order.shopper_user_id,ew_demand_order.shopper_alias')->from('ew_demand_content')->join('ew_demand_order','ew_demand_content.id=ew_demand_order.content_id')->get();
                     $data_log = $brr->row_array();
                     $this->load->model('demand_order_log_model'); 
                     $this->demand_order_log_model->demandlog($data_log['content_id'],$did,$data_log['order_id'],$data_log['demand_id'],'自荐信管理','自荐信管理-自荐信审核通过');

                    // 百度推送 begin ----------------------------------------------
//                    $order_info = $this->order->getOrderById($data['order_id']);
//                    $content_info = $this->content->getContentById($order_info['content_id'], 'demand_id');

                    $data['demand_id'] =  $data_log['demand_id'];
                    $data['id'] =  $did;
                    $data['status'] =  $init["status"];
                    $data['shopper_user_id'] =  $data_log['shopper_user_id'];
                    $data['shopper_alias'] =  $data_log['shopper_alias'];

                    $this->baidupush->BaiduPushForErp($data);

                    // 百度推送 end -----------------------------------------------

              }


				
		}else{
				//审核不通过
				$init["status"] = 99;//订单关闭status为99
                 $order_id = explode(',', $ids);
				foreach($order_id as $v){
                     //添加需求日志
                     $did = $v;
                     $brr = $this->ew_conn->where('ew_demand_order.id',$did)->select('ew_demand_order.content_id,ew_demand_content.demand_id,ew_demand_order.order_id')->from('ew_demand_content')->join('ew_demand_order','ew_demand_content.id=ew_demand_order.content_id')->get();
                     $data_log = $brr->row_array();
                     $this->load->model('demand_order_log_model'); 
                     $this->demand_order_log_model->demandlog($data_log['content_id'],$did,$data_log['order_id'],$data_log['demand_id'],'自荐信管理','自荐信管理-自荐信审核不通过');
              }

				
		}
		$row = "update ew_demand_order set status='".$init["status"]."',time_".$init["status"]."=NOW() where id in(".$ids.")";
		$rows=$this->ew_conn->query($row);
		//返回数据
		if(empty($rows)){
			return failure("操作失败");
		}else{
			return success('操作成功');
		}
		
	}
	
	//保存自荐信详细信息
	public function update_letter()
	{
	    $inputs = $this->input->post();
		$id = $inputs['id'];//修改记录的订单表中的id
		if($id <= 0)
		{
			return failure("参数错误");
		}   
		$data['recommend_letter']['advantage']['name']= "我的优势"; 
		$data['recommend_letter']['advantage']['value']= isset($inputs['advantage'])?$inputs['advantage']:"";
		$data['recommend_letter']['experience']['name']= "经验简介"; 
		$data['recommend_letter']['experience']['value']=isset($inputs['experience'])?$inputs['experience']:""; 
		$data['recommend_letter']['opus']['name']= "案例链接";
        $data['recommend_letter']['opus']['value']= isset($inputs['opus'])?$inputs['opus']:"";
		$data['recommend_letter']['file_name']['name']= "婚礼方案建议附件名称";
		$data['recommend_letter']['file_name']['value']= isset($inputs['file_name'])?$inputs['file_name']:"";
		$data['recommend_letter']['file_url']['name']= "婚礼方案建议附件";
		$data['recommend_letter']['file_url']['value']= isset($inputs['file_url'])?$inputs['file_url']:"";	
		
		$data['recommend_letter']['file_advise']['name']= "婚礼方案建议";
		$data['recommend_letter']['file_advise']['value']= isset($inputs['file_advise'])?$inputs['file_advise']:"";

		$data['recommend_letter']['prices']['name']= "推荐服务";
		$data['recommend_letter']['prices']['value'][0]['service_type']['name']= "服务类型";
		$data['recommend_letter']['prices']['value'][0]['service_type']['value']= isset($inputs['prices_service_type'])?$inputs['prices_service_type']:"";

		$data['recommend_letter']['prices']['name']= "推荐服务";
		$data['recommend_letter']['prices']['value'][0]['service']['name']= "服务内容";
		$data['recommend_letter']['prices']['value'][0]['service']['value']= isset($inputs['prices_service'])?$inputs['prices_service']:"";


		$data['recommend_letter']['prices']['name']= "推荐服务";
		$data['recommend_letter']['prices']['value'][0]['price']['name']= "服务报价";
		$data['recommend_letter']['prices']['value'][0]['price']['value']= isset($inputs['prices_price'])?$inputs['prices_price']:"";


		$data['recommend_letter']['prices']['name']= "推荐服务";
		$data['recommend_letter']['prices']['value'][0]['reason']['name']= "推荐理由";
		$data['recommend_letter']['prices']['value'][0]['reason']['value']= isset($inputs['prices_reason'])?$inputs['prices_reason']:"";

		$data['recommend_letter']['prices']['name']= "推荐服务";
		$data['recommend_letter']['prices']['value'][0]['grabprice']['name']= "抢单价";
		$data['recommend_letter']['prices']['value'][0]['grabprice']['value']= isset($inputs['prices_grabprice'])?$inputs['prices_grabprice']:"";

		$list["wish"] = $inputs['wish'];    //接单意愿
		$list['recommend_letter'] = json_encode($data['recommend_letter']);
        $rows = $this->ew_conn->where("id",$id)->update('ew_demand_order', $list);
	   if(empty($rows)){
			return failure("保存失败");
		}else{
			return success('保存成功');
		}
	
	}

}
