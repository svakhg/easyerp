<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 在线签约Model
 */

class Demand_contract_model extends MY_Model{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 获取签约信息
     * $data['demand_id']:需求id
     * $data['order_id']:中标的订单id
     * $data['id']:签约表的自增长id
     */
    public function getContract($data)
    {
        $sql = 'select * from ew_demand_contract ';
        $sql.= ' where 1 ';
        if(isset($data['demand_id'])){
            $sql .= ' and demand_id='.$data['demand_id'];
        }
        if(isset($data['order_id'])){
            $sql .= ' and order_id='.$data['order_id'];
        }
        if(isset($data['id'])){
            $sql .= ' and id='.$data['id'];
        }
        $res = $this->erp_conn->query($sql)->result_array();
        return $res;
    }

    /*
     * 插入一条
     */
    public function insertOneContract($data)
    {
        $sql = 'insert into ew_demand_contract set ';
        $sql.= ' demand_id='.$data['demand_id'].',';
        $sql.= ' order_id='.$data['order_id'].',';
        $sql.= ' contract_code="'.$data['contract_code'].'",';
        $sql.= ' money='.$data['money'].',';
        $sql.= ' wed_date="'.$data['wed_date'].'",';
        $sql.= ' wed_location="'.$data['wed_location'].'",';
        $sql.= ' wed_place="'.$data['wed_place'].'",';
        $sql.= ' comment="'.$data['comment'].'",';
        $sql.= ' create_time=NOW()';
        $res = $this->erp_conn->query($sql);
        return $res;
    }


    /*
     * 有该条数据的时候更新
     */
    public function updateOneContract($data)
    {
        $sql = 'update ew_demand_contract set ';
        $sql.= ' contract_code="'.$data['contract_code'].'",';
        $sql.= ' money='.$data['money'].',';
        $sql.= ' wed_date="'.$data['wed_date'].'",';
        $sql.= ' wed_location="'.$data['wed_location'].'",';
        $sql.= ' wed_place="'.$data['wed_place'].'",';
        $sql.= ' comment="'.$data['comment'].'"';
        $sql.= ' where demand_id='.$data['demand_id'];
        $res = $this->erp_conn->query($sql);
        return $res;
    }

    /*
     * 检查contract_code是否存在
     */
    public function checkCodeExist($contract_code,$demand_id)
    {
        $sql = 'select count(1) from ew_demand_contract where contract_code="'.$contract_code.'"';
        if($demand_id != ''){
            $sql .= ' and demand_id!='.$demand_id;
        }
        $res = $this->erp_conn->query($sql)->result_array();
        if($res[0]['count(1)'] == 1){//已存在
            return false;
        }else{//未存在可写
            return true;
        }
    }

    /*
     * 根据需求id获取该需求下中标的订单
     */
    public function getOrderByDemand($demand_id)
    {
        $sql = 'select id from ew_demand_order';
        $sql.= ' where status=51 and content_id='.$demand_id;
        $res = $this->ew_conn->query($sql)->result_array();
        return $res;
    }

    /*
     * 获取一条需求中中标的商家
     * $data['demand_id']:需求id
     */
    public function getBingoShopper($data){
        $sql = 'select * from ew_demand_order ';
        $sql.= ' where 1';
        $sql.= ' and content_id='.$data['demand_id'];
        $sql.= ' and (status=51 or status=61)';
        $res = $this->ew_conn->query($sql)->result_array();
        if(isset($res[0])){
            foreach($res as $k => $v){
                $sql_shopper = 'select * from ew_users where uid='.$v['shopper_user_id'];
                $res_shopper = $this->ew_conn->query($sql_shopper)->result_array();
                if(isset($res_shopper[0]['nickname'])){
                    $res[$k]['nickname'] = $res_shopper[0]['nickname'];
                }
                if(isset($res_shopper[0]['phone'])){
                    $res[$k]['phone'] = $res_shopper[0]['phone'];
                }
            }
        }
        return $res;
    }

    /*
     * 保存签约信息的时候同时更新需求表中的数据
     * $data['demand_id']:需求id
     * $data['wed_data']:婚礼日期
     * $data['wed_location']:婚礼地点
     * $data['wed_place']:婚礼场地
     */
    public function updateDemand($data){
        $sql = 'update ew_demand_content set';
        $sql.= ' wed_date_sure = 1,';
        $sql.= ' wed_date = "'.$data['wed_date'].'",';
        $sql.= ' wed_location = "'.$data['wed_location'].'",';
        $sql.= ' wed_place = "'.$data['wed_place'].'"';
        $sql.= ' where id = '.$data['demand_id'];
        $res = $this->ew_conn->query($sql);
        return $res;
    }



    /*
     * 验证一站式未审核需求商家信息
     * $data['dmid']:需求id
     */
    public function checkDemandType($data){
        $result = $this->ew_conn->from('ew_demand_content as c')
            ->join('ew_demand_order as o', 'c.id = o.content_id','left')
            ->select('c.id,c.mode,c.type,c.status as c_status,o.status as o_status')
            ->where('c.id',$data['dmid'])->order_by('o_status','DESC')
            ->get()->row_array();

          if ($result['mode']==2) {//一站式指定商家
              return 1;
          }
          if($result['type']==1&&$result['o_status']>=51){//  一站式 
              return 1;
          }
      

    }

    /*
     * 验证单项式未审核需求商家信息
     * $data['dmid']:需求id
     */
    public function checkDemandTypes($data){
         $result = $this->ew_conn->from('ew_demand_content as c')
            ->join('ew_demand_order as o', 'c.id = o.content_id','left')
            ->select('c.id,c.mode,c.type,c.status as c_status,o.status as o_status')
            ->where('c.id',$data['dmid'])->order_by('o_status','DESC')
            ->get()->row_array();
        if ($result['mode']==2) {//单项式指定商家
           return 1;
        }
        if($result['type']==2&&$result['o_status']>=11){
            return 1;
        }

    }

     /*
     * 移除未审核策划师商家信息
     * $data['content_id']:需求id
     * $data['shopper_user_ids']:商家ids（字符串，逗号分隔）
     * return boolean
     */
    public function removeDemandShopper($data)
    {
      $shopper_arr = explode(',',$data['shopper_user_ids']);
      $res = $this->ew_conn->where('content_id',$data['dmid'])->where_in('id',$shopper_arr)->delete('ew_demand_order');
 
     if($res){
            return 1;
        }else{
            return 2;
        }
    }


    /*
     * 移除商家信息
     * $data['content_id']:需求id
     * $data['shopper_user_ids']:商家ids（字符串，逗号分隔）
     * return boolean
     */
    public function removeShopperDel($data)
    {
      $shopper_arr = explode(',',$data['shopper_user_ids']);
      $res = $this->ew_conn->where('content_id',$data['dmid'])->where_in('shopper_user_id',$shopper_arr)->delete('ew_demand_order');
 
     if($res){
            return 1;
        }else{
            return 2;
        }
    }



}