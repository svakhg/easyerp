<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 酒店商机管理
 */

class Tools extends App_Controller {


	public function __construct(){
        parent::__construct();
        $this->load->model("business/business_model",'business');
        $this->load->model("business/tools_model",'tools');
        $this->load->library('sms');
    }

	/**
	 *  发送(添加)短信
	 */
	public function sendtMessage()
	{
         $inputs = $this->input->post();
         if(!$inputs['bid'])
         {
             return failure("商机不存在");
         }
        if(!empty($inputs['type']) && $inputs['type'] == Tools_model::STATUS_SAVE){
            $tools_info = $this->erp_conn->where('bid',$inputs['bid'])->where('type',Tools_model::STATUS_SAVE)->get('tools')->result_array();
            if(!empty($tools_info)){
                return failure("挽救短信只能发生一次");
            }
        }

        $sendname = $this->session->userdata("admin");
        $init = array(
             "bid" => $inputs['bid'],
             "mobile"=>isset($inputs['mobile']) ? $inputs['mobile'] : "",
             "type" =>isset($inputs['type']) ? $inputs['type'] : "",
             "content" =>isset($inputs['content']) ? $inputs['content'] : "",
             "sendname" =>$sendname,
             "sendtime" =>time(),
             );
        if(!empty($init['mobile']) && !empty($init['content'])){
            $send = $this->sms->send(array($init['mobile']) , $init['content']);
        }else{
            return failure('手机号或者短信内容不能为空');
        }
        if($send["code"] == 1 && $this->tools->increase($init))
        {
            return success("发送成功");
        }else
        {
            return failure("发送失败");
        }


	}
    /**
     * 顾问工具 短信列表
     * @return type
     */
    public function toolsList()
    {
        //商机id
        $bid = $this->input->get("bid", 0);
        $bid = intval($bid);
        if(empty($bid)){
            return failure("bid error!");
        }
        if(!$this->business->findRow(array("id" => $bid)))
        {
            return failure("商机不存在");
        }

        //分页
        $inputs = $this->input->get();
        $page = (isset($inputs['page']) && $inputs['page'] > 1) ? $inputs['page'] : 1;
        $pagesize = isset($inputs['pagesize']) ? $inputs['pagesize'] : 10;

        //取列表
        $where["bid"] = $bid;
        //$where["status"] = isset($inputs["type"]) ? $inputs["type"] : '';
        $tools = $this->tools->getList($where, $page, $pagesize);
        foreach($tools['rows'] as $k => &$v){
            if($v['type'] == Tools_model::STATUS_CUSTOMER){
                $v['type'] = "给客户推荐酒店";
            }elseif($v['type'] == Tools_model::STATUS_SALES){
                $v['type'] = "给销售客户电话";
            }elseif($v['type'] == Tools_model::STATUS_SAVE){
                $v['type'] = "给客户挽救短信";
            }
            $v['sendtime'] = date('Y-m-d H:i:s',$v['sendtime']);
        }

        return success($tools);
    }
	
}
