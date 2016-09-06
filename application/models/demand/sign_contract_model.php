<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 新版在线签约Model
 */

class Sign_contract_model extends MY_Model{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 获取团队列表
     */
    public function getPartnerList($data)
    {
        // $demand_contract = $this->getContractByDemandid($data);
        // $contract_id = isset($demand_contract[0]) ? $demand_contract[0]["id"] : 0;
        // if(isset($demand_contract[0])){
            /*==================================== 获取合作商数据 ===============================================*/
            $this->ew_conn->from("sign_contract_partner");
            $partner_where = array(
                        "cid" => $data["sid"],
                        // "cooperation_status" => 1,//合作中的商家
                );
            $this->ew_conn->where($partner_where);
            if(isset($data['page']) && isset($data['pagesize'])){
                $start = ($data['page'] - 1) * $data['pagesize'];
                $this->ew_conn->limit($data['pagesize'],$start);
            }
            $partner_list = $this->ew_conn->get()->result_array();
            /*==================================== 获取合作商数据 ===============================================*/

            /*==================================== 获取合作商数量 ===============================================*/
            $this->ew_conn->from("sign_contract_partner");
            $partner_where = array(
                        "cid" => $data["sid"],
                        // "cooperation_status" => 1,//合作中的商家
                );
            $this->ew_conn->where($partner_where);
            $partner_count = $this->ew_conn->count_all_results();
            /*==================================== 获取合作商数量 ===============================================*/
        // }else{
        //     $partner_list = array();
        //     $partner_count = 0;
        // }
        foreach($partner_list as $k => $v){
            $user_info = $this->ew_conn->where("uid",$v["shopper_uid"])->get("users")->result_array();
            $shopper_info = $this->ew_conn->where("uid",$v["shopper_uid"])->get("user_shopers")->result_array();
            $partner_list[$k]['shopper_uid'] = $v["shopper_uid"];//商家id
            $partner_list[$k]['cancel_cooperation'] = $v["cooperation_status"];//取消合作的按钮
            $partner_list[$k]['shopper_uname'] = isset($shopper_info[0]) ? $shopper_info[0]["realname"] : "";//商家姓名
            $partner_list[$k]['studio_name'] = isset($shopper_info[0]) ? $shopper_info[0]["studio_name"] : "";//店铺名称
            $partner_list[$k]['shopper_alias'] = $v["shopper_alias"];//商家类型
            $partner_list[$k]['shopper_alias_text'] = self::getShopperAlias($v["shopper_alias"]);//商家类型名称
            $partner_list[$k]['phone'] = isset($user_info[0]) ? $user_info[0]["phone"] : "";//电话号码
            $partner_list[$k]['service_price'] = 0;//服务报价
            $partner_list[$k]['discount_amount'] = 0;//优惠金额
            $partner_list[$k]['deal_amount'] = 0;//成交价
            $partner_list[$k]['yijie_amount'] = 0;//易结价
            $partner_list[$k]['created_time'] = date("Y-m-d H:i:s",$v["created_time"]);//创建时间
            $partner_list[$k]['cooperation_status_text'] = ($v["cooperation_status"] == 1) ? "合作" : "非合作";//创建时间
        }
        return array("total" => $partner_count, "rows" => $partner_list);
    }

    public function getOnePartner($data)
    {
        $this->ew_conn->from("sign_contract_partner");
        $this->ew_conn->where($data);
        $result = $this->ew_conn->get()->result_array();
        return $result;
    }

    /*
     * $data['id']:
     */
    public function getContractByDemandid($data)
    {
        $this->ew_conn->from("sign_contract");
        $this->ew_conn->where("demand_id",$data["id"]);
        $demand_contract = $this->ew_conn->get()->result_array();
        return $demand_contract;
    }

    /*
     * 添加婚礼团队
     * $data['shopper_uid']:商家uid
     * $data['shopper_alias']:商家别名
     */
    public function addPartner($data)
    {
        $item = $this->getOnePartner(array('cid' => $data['cid'], 'shopper_uid' => $data['shopper_uid'], 'shopper_alias' => $data['shopper_alias']));
        if(!isset($item[0])){
            $params['cid'] = $data['cid'];
            $params['shopper_alias'] = $data['shopper_alias'];
            $params['shopper_uid'] = $data['shopper_uid'];
            $params['created_time'] = strtotime(date("Y-m-d H:i:s"));
            $result = $this->ew_conn->insert("sign_contract_partner",$params);
            //更新策划师成交量
            $turnover_url = $this->config->config["ew_domain"]."erp/demand/change-shopper-turnover";
            $res_turnover = $this->curl->post($turnover_url, array("uid"=>$data['shopper_uid']));
        }else{
            $result = false;
        }
        return $result;
    }

    /*
     * 取消合作
     */
    public function delPartner($data)
    {
        $params = array("cooperation_status" => $data["cooperation_status"]);
        $shopper_uids = explode(",",$data['shopper_uid']);
        $this->ew_conn->where(array("cid" => $data['sid']));
        $this->ew_conn->where_in('shopper_uid', $shopper_uids);
        $this->ew_conn->from("sign_contract_partner");
        $result = $this->ew_conn->update("sign_contract_partner", $params);
        //更新策划师成交量
        foreach($shopper_uids as $k => $v){
            $turnover_url = $this->config->config["ew_domain"]."erp/demand/change-shopper-turnover";
            $res_turnover = $this->curl->post($turnover_url, array("uid"=>$v));
        }
        return $result;
    }

}
?>

