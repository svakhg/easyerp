<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detaildemand extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bid/Shopperorder');
    }

    /*
     * 获取需求内容的详细信息
     * @param  $id  需求的id
     * @return array
     */
    public function getDetailContent($id)
    {
        $result = $this->ew_conn->from('shopper_demand_content as content')
            ->join('users', 'content.uid = users.uid','left')
            ->join('user_shopers', 'content.uid = user_shopers.uid','left')
            ->join('options','content.shopper_alias = options.option_alias','left')
            ->select('content.*, users.nickname, user_shopers.realname, users.phone,users.email, user_shopers.sex, user_shopers.address, user_shopers.address_detail, options.option_name as shopper_service_name,options.id as shoper_alia_code')
            ->where('content.id',$id)
            ->get()->row_array();
        $result = !empty($result) ? $result : array();
        return $result;
    }

    /*
     * 验证需求是否审核
     * $data['bid_id']:需求id
     */
    public static function checkDemandExamine($data){
        $Obj = get_Instance();
        $res = $Obj->ew_conn->where('id',$data['bid_id'])->get('shopper_demand_content')->row_array();
        if(empty($res)){
            return false;
        }
        return ($res['status'] == 1 || $res['status'] == 4) ? false : true;
    }

    /*
     * 获得指定类型的商家列表
     * @author by Abel Yang 2015/5/8
     * @param $params 筛选条件数组
     * @param $type  商家类别 代号 for example ：1425
     * @return array 商家列表
     */
    public function getShopperInfoByType($params,$type){

        $sql_con = 'where users.dostatus=2 and shopers.site_id=1 ';

        //默认按作品数量倒序排
        $sql_order = ' order by shopers.wed_num desc ';

        //类型：个人，公司或者工作室
        if(!empty($params['mode'])){
            $sql_con .= ' and shopers.mode = '.$params['mode'];
        }
        //地址 筛选的地区只有国内的
        if(!empty($params['address'])){
            $sql_con .= ' and shopers.address like "%1,'.$params['address'].'%"';
        }
        //关键词 商家昵称 店铺名称 商家手机
        if(!empty($params['keyword'])){
            $sql_con .= ' and (users.nickname like "%'.$params['keyword'].'%" or users.phone like "%'.$params['keyword'].'%" or shopers.studio_name like "%'.$params['keyword'].'%")';
        }

        //服务报价
        if(!empty($params['price_start']) && !empty($params['price_end'])){
            $sql_con .= ' and shopers.price >= '.$params['price_start'].' and shopers.price <= '.$params['price_end'];
        }elseif(!empty($params['price_start']) && empty($params['price_end'])){
            $sql_con .= ' and shopers.price >= '.$params['price_start'];
        }elseif(!empty($params['price_end']) && empty($params['price_start'])){
            $sql_con .= ' and shopers.price <= '.$params['price_end'];
        }

        //案例数量 对应shoper_user表里的 “从事婚礼次数（1代表10场以内，2代表11-50场，3代表51-200场，4代表200场以内）”；
        if(!empty($params['wed_num_start']) && !empty($params['wed_num_end'])){
            $sql_con .= ' and shopers.wed_num >= '.$params['wed_num_start'].' and shopers.wed_num <= '.$params['wed_num_end'];
        }elseif(!empty($params['wed_num_start']) && empty($params['wed_num_end'])){
            $sql_con .= ' and shopers.wed_num >= '.$params['wed_num_start'];
        }elseif(!empty($params['wed_num_end']) && empty($params['wed_num_start'])){
            $sql_con .= ' and shopers.wed_num <= '.$params['wed_num_end'];
        }

        //分页
        $page = !empty($params['page']) ? $params['page'] : 1;
        $pagesize = !empty($params['pagesize']) ? $params['pagesize'] : 10;

        $sql_con .= ' and shopers.serves like "%'.$type.'%"';

        $sql_base = "select shopers.uid,users.nickname,users.phone,shopers.address,shopers.studio_name,shopers.serves,shopers.price,shopers.wed_num from ew_user_shopers as shopers left join ew_users as users on users.uid = shopers.uid  ";

//        print_r($sql_base.$sql_con.$sql_order);die();
        $sql_limit = ' limit '.(($page-1)*10).','.$pagesize;
        //列表
        $list = $this->ew_conn->query($sql_base.$sql_con.$sql_order.$sql_limit)->result_array();
        //总数
        $count = count($this->ew_conn->query($sql_base.$sql_con)->result_array());
//        return $this->ew_conn->last_query();
        return array('total' => $count, 'rows' => $list);
    }

    /*
     * 分配商家
     * @author by Abel Yang 2015/5/8
     * @param  $data['bid_id']:需求id
     * @param  $data['shopper_user_ids']:选择的商家ids->  1,2,3
     * return  boolean
     */
    public function sendShopper($data){
        $demand_id = $data['bid_id'];
        $shopers_arr = explode(',',$data['shopper_user_ids']);

        $demand = $this->ew_conn->where('id',$demand_id)->get('shopper_demand_content')->row_array();
        foreach($shopers_arr as $val){
            $check_order = $this->ew_conn->where('content_id',$demand_id)->where('shopper_user_id',$val)->limit(1)->get('shopper_demand_order')->row_array();
            if(! empty($check_order)){
                continue;
            }
            $params = array(
                'content_id' => $demand_id, // 需求内容ID
                'shopper_alias' => $demand['shopper_alias'],
                'shopper_user_id' => $val,//商家id
                'service_type' => $demand['service_type'], //服务类型
            );
            //添加数据
            Shopperorder::addOrder($params);
        }
        //待插入成功后更新需求id的状态status 为4

        $this->ew_conn->where('id',$demand_id)->update('shopper_demand_content', array('status' => 4));

        return true;
    }

    /*
     * 获得需求对应订单列表
     * @author by Abel Yang
     * @param $params 筛选条件数组
     * @return array 订单列表
     */
    public function getDemandOrdersById($params){

        //获取分页步长
        $offset = ($params['page']-1)*$params['pagesize'];

        $where = '';
        //需求id
        if(!empty($params['bid_id'])) {
            $where .= 'orders.content_id = '. $params['bid_id'];
        }

        //商家投标中筛选，投标状态
        if(!empty($params['order_status'])) {
            if($params['order_status'] == 'unbiding'){//未应标
                $where .= ' and orders.status = 11 ';
            }
            if($params['order_status'] == 'bidding'){//已应标，还未到中标
                $where .= " and ( orders.status = 21 or orders.status = 31) ";
            }
            if($params['order_status'] == 'wonbid'){//中标
                $where .= " and (orders.status >= 41 and orders.status <= 61) ";
            }
        }

        //商家意向书筛选
        if(!empty($params['letter_status']))
        {
            if($params['letter_status'] == 'pending'){//待审核
                $where .= ' and orders.status = 21 ';
            }

            if($params['letter_status'] == 'yes'){//通过
                $where .= ' and (orders.status = 31 or orders.status = 41 or orders.status = 51 or orders.status = 61 )';
            }

            if($params['letter_status'] == 'no'){//不通过
                $where .= ' and orders.order_step_end = 31 and orders.status = 98 ';
            }
        }

        //地址 筛选的地区只有国内的
        if(!empty($params['address'])) {
            $where .= ' and shopers.address like "%1,' . $params['address'] . '%"';
        }
        //关键词 商家昵称 店铺名称 商家手机
        if(!empty($params['keyword'])){
            $where .= ' and (users.nickname like "%'.$params['keyword'].'%" or users.phone like "%'.$params['keyword'].'%" or shopers.studio_name like "%'.$params['keyword'].'%")';
        }
        //总数
        $count = $this->ew_conn->from('ew_shopper_demand_order as orders')
            ->join('ew_user_shopers as shopers','orders.shopper_user_id = shopers.uid','left')
            ->join('ew_users as users','orders.shopper_user_id = users.uid','left')
            ->select('orders.id,users.uid,users.nickname,shopers.studio_name,shopers.address,users.phone,orders.status,orders.recommend_letter,orders.time_1,orders.time_21')
            ->where($where)
            ->count_all_results();

        //列表
        $list = $this->ew_conn->from('ew_shopper_demand_order as orders')
            ->join('ew_user_shopers as shopers','orders.shopper_user_id = shopers.uid','left')
            ->join('ew_users as users','orders.shopper_user_id = users.uid','left')
            ->select('orders.id,users.uid,users.nickname,shopers.studio_name,shopers.address,users.phone,orders.status,orders.recommend_letter,orders.time_1,orders.time_21')
            ->where($where)
            ->limit($params['pagesize'])->offset($offset)->get()->result_array();

//        print_r($this->ew_conn->last_query());die();
        $list = $this->handleJson($list);
        return array('total' => $count, 'rows' => $list);
    }

    /*
     * 处理json字段（by zhangmiao）
     */
    private function handleJson($list,$key='recommend_letter'){
        foreach($list as $k => $v)
        {
            if(isset($v[$key]) && !empty($v[$key]))
            {
                $list[$k][$key] = json_decode($v[$key],true);
            }
        }
        return $list;
    }
}
?>