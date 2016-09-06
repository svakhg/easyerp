<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shopperorder extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 获取商家名称和中文的分类
     * $key,$value:返回数据的key和value的类型
     * $key:'option_alias',  $value:option_name;    返回array('wedplanners' => '策划师')
     * $key:'id',            $value:option_name;    返回array('1435' => '策划师')
     * $key:'option_name',   $value:id;             返回array('策划师' => '1435')
     * ....
     */
    private function getShopperOptions($key,$value)
    {
        $sql = 'select * from ew_options where option_type=1';
        $res = $this->ew_conn->query($sql)->result_array();
        foreach($res as $k => $v){
            $result[$v[$key]] = $v[$value];
        }
        print_R($result);exit;
    }

    /*
     * 获取订单的所有状态
     */
    public function getOrderStatus()
    {
        return array(
                '1'  => '初始订单待审核',
                '11' => '审核成功待合作商投标',
                '16' => '指定商家审核成功待合作商接单',
                '21' => '合作商竞标提交意向书',
                '26' => '合作商确定接单,待与商家沟通',
                '31' => '意向书审核通过待商家确定合作商',
                '36' => '合作商确定接单',
                '41' => '商家确定合作商',
                '51' => '商家确定完成服务',
                '61' => '商家对合作商已完成评价',
                '97' => '放弃订单',
                '98' => '订单失败',
                '99' => '需求关闭'
            );
    }

    /*
     * 获取意向书和弃单原因列表（by zhangmiao）
     */
    public function getLetterList($condition){
        $field = ' * ';
        $field_count = 'count(1)';
        $where = $this->getWhere($condition);
        $order = ' order by o.id desc ';
        if(!isset($condition['page']) || !is_numeric($condition['page']) || $condition['page']<=0){
            $condition['page'] = 1;
        }
        if(!isset($condition['pagesize']) || !is_numeric($condition['pagesize']) || $condition['pagesize']<=0){
            $condition['pagesize'] = 10;
        }
        $start = ($condition['page']-1)*$condition['pagesize'];
        $limit_sql = ' limit '.$start.','.$condition['pagesize'];
        $sql = 'select '.$field.' from ew_shopper_demand_order as o ';
        $sql.= ' left join ew_user_shopers as s on o.shopper_user_id=s.uid';
        $sql.= ' left join ew_users as u on o.shopper_user_id=u.uid';
        $sql.= ' where '.$where.' '.$order.' '.$limit_sql;

        $sql_count = 'select '.$field_count.' from ew_shopper_demand_order as o ';
        $sql_count.= ' left join ew_user_shopers as s on o.shopper_user_id=s.uid';
        $sql_count.= ' left join ew_users as u on o.shopper_user_id=u.uid';
        $sql_count.= ' where '.$where;

        $res = $this->ew_conn->query($sql)->result_array();
        $res = $this->handleJson($res,'recommend_letter');//将json字段decode
        $count = $this->ew_conn->query($sql_count)->result_array();
        $count = $count[0][$field_count];
        $result = array('result' => 'succ','info' => array('rows' => $res,'total' => $count));
        return $result;
    }

    /*
     * 获取意向书和弃单原因列表的条件（by zhangmiao）
     */
    private function getWhere($condition)
    {
        $where = ' 1 ';
/**************************       意向书审核 start     *****************************/
        //意向书状态
        if(isset($condition['letter_status'])){//意向书不能为空
            $where .= ' and o.wish != "" ';
        }
        if(isset($condition['letter_status']) && $condition['letter_status'] == 'dai'){//待审核
            $where .= ' and o.status=21';
        }
        if(isset($condition['letter_status']) && $condition['letter_status'] == 'yes'){//通过
            $where .= ' and o.status>=31 and o.status<=61';
        }
        if(isset($condition['letter_status']) && $condition['letter_status'] == 'no'){//未通过
            $where .= ' and o.status=98';
        }
        //提交审核时间
        if(isset($condition['time_21_from']) && !empty($condition['time_21_from'])){
            $where .= ' and time_21 >="'.$condition['time_21_from'].'"';
        }
        if(isset($condition['time_21_to']) && !empty($condition['time_21_to'])){
            $where .= ' and time_21 <="'.$condition['time_21_to'].'"';
        }
        //通过审核时间
        if(isset($condition['time_31_from']) && !empty($condition['time_31_from'])){
            $where .= ' and time_31 >="'.$condition['time_31_from'].'"';
        }
        if(isset($condition['time_31_to']) && !empty($condition['time_31_to'])){
            $where .= ' and time_31 <="'.$condition['time_31_to'].'"';
        }
/**************************       意向书审核 end     *****************************/
/**************************       弃单原因审核 start     *****************************/
        //弃单原因状态
        if(isset($condition['reason_status'])){//弃单原因不能为空
            $where .= ' and o.abort_reason != "" ';
        }
        if(isset($condition['reason_status']) && $condition['reason_status'] == 'dai'){//待审核
            $where .= ' and o.reason_status=1';
        }
        if(isset($condition['reason_status']) && $condition['reason_status'] == 'yes'){//通过
            $where .= ' and o.reason_status=2';
        }
        if(isset($condition['reason_status']) && $condition['reason_status'] == 'no'){//未通过
            $where .= ' and o.reason_status=3';
        }
        //放弃订单时间
        if(isset($condition['time_97_from']) && !empty($condition['time_97_from'])){
            $where .= ' and time_97 >="'.$condition['time_97_from'].'"';
        }
        if(isset($condition['time_97_to']) && !empty($condition['time_97_to'])){
            $where .= ' and time_97 <="'.$condition['time_97_to'].'"';
        }
/**************************       弃单原因审核 end     *****************************/
        //需求类型
        if(isset($condition['shopper_alias']) && !empty($condition['shopper_alias'])){
            $where .= ' and o.shopper_alias="'.$condition['shopper_alias'].'"';
        }
        //商家昵称 & 商家店铺 & 商家手机号码
        if(isset($condition['keywords']) && !empty($condition['keywords'])){
            $where .= ' and (';
            $where .= ' u.phone = "'.$condition['keywords'].'" ';
            $where .= ' or u.nickname like "%'.$condition['keywords'].'%" ';
            $where .= ' or s.studio_name like "%'.$condition['keywords'].'%" ';
            $where .= ' )';
        }
        return $where;
    }

    /*
     * 处理json字段（by zhangmiao）
     */
    private function handleJson($list,$key='recommend_letter'){
        $this->lang->load('date','chinese');
        $this->load->helper('date');
        $orderStatus = $this->getOrderStatus();
        foreach($list as $k => $v)
        {
            $list[$k]['status'] = $orderStatus[$v['status']];
            if(isset($v[$key]) && !empty($v[$key]))
            {
                $list[$k][$key] = json_decode($v[$key],true);
            }
            $list[$k]['time_11_16'] = ($v['time_11']!='0000-00-00 00:00:00')?$v['time_11']:$v['time_16'];
            $list[$k]['time_1'] = $v['time_1'] == '0000-00-00 00:00:00' ? '' : $v['time_1'];
            $list[$k]['time_11'] = $v['time_11'] == '0000-00-00 00:00:00' ? '' : $v['time_11'];
            $list[$k]['time_16'] = $v['time_16'] == '0000-00-00 00:00:00' ? '' : $v['time_16'];
            $list[$k]['time_21'] = $v['time_21'] == '0000-00-00 00:00:00' ? '' : $v['time_21'];
            $list[$k]['time_26'] = $v['time_26'] == '0000-00-00 00:00:00' ? '' : $v['time_26'];
            $list[$k]['time_31'] = $v['time_31'] == '0000-00-00 00:00:00' ? '' : $v['time_31'];
            $list[$k]['time_36'] = $v['time_36'] == '0000-00-00 00:00:00' ? '' : $v['time_36'];
            $list[$k]['time_41'] = $v['time_41'] == '0000-00-00 00:00:00' ? '' : $v['time_41'];
            $list[$k]['time_51'] = $v['time_51'] == '0000-00-00 00:00:00' ? '' : $v['time_51'];
            $list[$k]['time_61'] = $v['time_61'] == '0000-00-00 00:00:00' ? '' : $v['time_61'];
            $list[$k]['time_97'] = $v['time_97'] == '0000-00-00 00:00:00' ? '' : $v['time_97'];
            $list[$k]['time_98'] = $v['time_98'] == '0000-00-00 00:00:00' ? '' : $v['time_98'];
            $list[$k]['time_99'] = $v['time_99'] == '0000-00-00 00:00:00' ? '' : $v['time_99'];
            //意向书提交距今
            $list[$k]['submit_letter_now'] = compare_to_now($v['time_21']) == "Unkown" ? '' : compare_to_now($v['time_21']);
            //弃单原因提交距今
            $list[$k]['submit_abort_now'] = compare_to_now($v['time_97']) == "Unkown" ? '' : compare_to_now($v['time_97']);
        }
        // foreach($list as $k => $v)
        // {
        //     if(in_array($v['shopper_alias'],array('wedplanners','sitelayout')))
        //     {//策划师和场布只有附加说明(work_description)
        //         $list[$k]['letter_work_description'] = isset($v['recommend_letter']['work_description']) ? $v['recommend_letter']['work_description']['value'] : '';
        //     }
        //     else if(in_array($v['shopper_alias'],array('wedmaster','makeup','wedphotoer','wedvideo')))
        //     {//四大金刚的意向书中推荐服务(prices)和定制服务(customservice)只有一项有值
        //         $list[$k]['letter_additional_remark'] = isset($v['recommend_letter']['additional_remark']) ? $v['recommend_letter']['additional_remark']['value'] : '';
        //         if(isset($v['recommend_letter']['prices']['value']) && !empty($v['recommend_letter']['prices']['value']))
        //         {
        //             foreach($v['recommend_letter']['prices']['value'] as $kr => $vr)
        //             {
        //                 $list[$k]['letter_prices_service_type'] = isset($v['recommend_letter']['prices']['value'][$kr]['service_type']) ? $v['recommend_letter']['prices']['value'][$kr]['service_type']['value'] : '';
        //                 $list[$k]['letter_prices_service'] = isset($v['recommend_letter']['prices']['value'][$kr]['service']) ? $v['recommend_letter']['prices']['value'][$kr]['service']['value'] : '';
        //                 $list[$k]['letter_prices_price'] = isset($v['recommend_letter']['prices']['value'][$kr]['price']) ? $v['recommend_letter']['prices']['value'][$kr]['price']['value'] : '';
        //                 $list[$k]['letter_prices_grabprice'] = isset($v['recommend_letter']['prices']['value'][$kr]['grabprice']) ? $v['recommend_letter']['prices']['value'][$kr]['grabprice']['value'] : '';
        //             }
        //         }
        //         else{
        //             $list[$k]['letter_prices_service'] = isset($v['recommend_letter']['customservice']['value']['service']) ? $v['recommend_letter']['customservice']['value']['service']['value'] : '';
        //             $list[$k]['letter_prices_price'] = isset($v['recommend_letter']['customservice']['value']['price']) ? $v['recommend_letter']['customservice']['value']['price']['value'] : '';
        //         }
        //     }
        // }
        return $list;
    }

    /*
     * 添加商家订单
     */
    public static function addOrder($data){
        $Obj = get_Instance();
        $time = date('Y-m-d H:i:s');
        $orderParams = array(
            'content_id' => $data['content_id'], // 需求内容ID
            'shopper_alias' => $data['shopper_alias'],
            'shopper_user_id' => $data['shopper_user_id'],//商家id
            'service_type' => $data['service_type'], //服务类型
            'status' => 1, // 刚添加完订单的状态为 1
            'time_1' => $time,
            'recommend_letter' => '',
        );
        return $Obj->ew_conn->insert('shopper_demand_order', $orderParams);
    }

    /*
     * 移除订单
     * $data['bid_id']:需求id
     * $data['order_ids']:订单ids（字符串，逗号分隔）
     * return boolean
     */
    public function removeDemandOrder($data)
    {
        $order_arr = explode(',',$data['order_ids']);
        $res = $this->ew_conn->where('content_id',$data['bid_id'])->where_in('id',$order_arr)->delete('shopper_demand_order');

        return true;
    }

    /*
     * 获取订单的UID，用于获取手机号（by zhangmiao）
     */
    public function getTendererPhone($demand_id)
    {
        $sql = 'select shopper_user_id from ew_shopper_demand_order ';
        $sql.= 'where content_id in ('.$demand_id.')';
        $tenderer_uid = $this->ew_conn->query($sql)->result_array();
        $uid_arr = array();
        foreach($tenderer_uid as $k => $v)
        {
            $uid_arr[] = $v['shopper_user_id'];
        }
        $uid_str = implode($uid_arr,',');
        if($uid_str == ''){
            return array();
        }else{
            $sql_phone = 'select phone from ew_users where uid in ('.$uid_str.')';
            $phone = $this->ew_conn->query($sql_phone)->result_array();
            foreach($phone as $kp => $vp){
                $phone_arr[] = $vp['phone'];
            }
            return $phone_arr;
        }
    }

    /*
     * 保存意向书
     * $data['order_id']:订单id
     * $data['recommend_letter']:意向书json
     */
    public function updateRecommendLetter($data)
    {
        $sql = 'update ew_shopper_demand_order set';
        $sql.= " recommend_letter='".$data['recommend_letter']."',";
        $sql.= ' wish="'.$data['wish'].'"';
        $sql.= ' where id='.$data['order_id'];
        $res = $this->ew_conn->query($sql);
        return $res;
    }

    /*
     * 保存弃单原因
     * $data['order_id']:订单id
     * $data['abort_reason']:弃单原因
     */
    public function updateAbortReason($data)
    {
        $sql = 'update ew_shopper_demand_order set';
        $sql.= ' abort_reason="'.$data['abort_reason'].'"';
        $sql.= ' where id='.$data['order_id'];
        $res = $this->ew_conn->query($sql);
        return $res;
    }

    /*
     * 获取需求
     * @author by Abel
     * $id:需求id
     * return array
     */
    public function getDemandById($id)
    {
        $demand = $this->ew_conn->where('id',$id)->get('shopper_demand_content')->row_array();
        return ! empty($demand) ? $demand : array();
    }

    /*
     * 根据需求获取对应的订单
     * @author by Abel
     * $content_id
     * return array
     */
    public function getOrdersByContentId($content_id, $select = '*')
    {
        $orders = $this->ew_conn->select($select)->where('content_id',$content_id)->get('shopper_demand_order')->result_array();
        return ! empty($orders) ? $orders : array();
    }

    /*
     * 判断需求所处的状态
     * 根据检查需求所对应的订单走到的最大状态
     * @author by Abel
     * $bid_id:需求id
     * return array $data  需求对应的状态（根据订单得出的）
     */
    public function getDemandBidStatusInOrders($bid_id)
    {
        $data['result'] = array('bid_status'=> '0', 'status_txt' => '需求待分配商家');

        $orders = $this->getOrdersByContentId($bid_id);
        //没有分配商家
        if(empty($orders)){
            return $data;
        }

        //获取订单列表
        $order = $this->ew_conn->where('content_id',$bid_id)->where_not_in('status',array(97,98))->order_by('status','desc')->get('shopper_demand_order')->row_array();
        if(! empty($order)){
            switch($order['status']){
                case 1:
                    $data['result'] = array('bid_status'=> '1', 'status_txt' => '需求待审核');
                    break;
                case 11:
                case 21:
                case 31:
                    $data['result'] = array('bid_status'=> '2', 'status_txt' => '商家投标中');
                    break;
                case 41:
                case 51:
                    $data['result'] = array('bid_status'=> '3', 'status_txt' => '招投标完成');
                    break;
                case 61:
                    $data['result'] = array('bid_status'=> '4', 'status_txt' => '服务完成');
                    break;
                case 99:
                    $data['result'] = array('bid_status'=> '5', 'status_txt' => '需求关闭');
                    break;
            }
        }
        //获得最早的分配商家的时间
        $data['sent_time'] = $this->getDemandBidTimeInOrders($bid_id,'time_1');

        //获得最早商家响应的时间
        $data['answer_time'] = $this->getDemandBidTimeInOrders($bid_id,'time_21');

        $data['wonbid_time'] = $this->getDemandBidTimeInOrders($bid_id,'time_41');

        $data['complete_time'] = $this->getDemandBidTimeInOrders($bid_id,'time_61');

        $data['close_time'] = $this->getDemandBidTimeInOrders($bid_id,'time_99');

        return $data;
    }

    /*
     * 根据order获得时间 包括需求分配商家的时间time_1、需求响应的时间time_21等
     * @author by Abel
     * $bid_id:需求id
     * $time_txt 如 time_1 或 time_21
     * $sort 排序 默认'asc'
     */
    public function getDemandBidTimeInOrders($bid_id, $time_txt, $sort = 'asc')
    {
        $date = $this->ew_conn->select("$time_txt as time")->where('content_id',$bid_id)->where(array("$time_txt !=" => '0000-00-00 00:00:00'))->order_by($time_txt,$sort)->get('shopper_demand_order')->row_array();
//        print_r($time);die();
        if(empty($date)){
            $date['time'] = '';
        }
        return $date['time'];
    }
}
