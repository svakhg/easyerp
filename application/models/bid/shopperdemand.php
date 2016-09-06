<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shopperdemand extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 根据需求id获取订单列表
     */
    public function getOrderList($data)
    {
        $sql = 'select * from ew_shopper_demand_order';
        $sql.= ' where content_id='.$data['demand_id'];
        if(isset($data['start']) && isset($data['limit']))
        {
            $sql.= ' limit '.$data['start'].','.$data['limit'];
        }
        $res = $this->ew_conn->query($sql)->result_array();
        return $res;
    }

    /*
     * 获取一条需求
     */
    public function getOneDemand($data)
    {
        $sql = 'select * from ew_shopper_demand_content';
        $sql.= ' where id='.$data['bid_id'];
        $res = $this->ew_conn->query($sql)->result_array();
        return $res;
    }

    /*
     * 获取一条订单
     */
    public function getOneOrder($data)
    {
        $sql = 'select * from ew_shopper_demand_order';
        $sql.= ' where id='.$data['order_id'];
        $res = $this->ew_conn->query($sql)->result_array();
        return $res;
    }

    /*
     * 获取该需求的订单数量
     */
    public function getOrderCountByDemandId($demand_id){
        $sql = 'select count(1) from ew_shopper_demand_order';
        $sql.= ' where content_id='.$demand_id;
        $res = $this->ew_conn->query($sql)->result_array();
        return $res[0]['count(1)'];
    }

    /*
     * 修改招投标信息content表的status
     * $data['content_status']:状态值
     * $data['bid_id']:自增长id
     */
    public function changeBidContentStatus($data)
    {
        $sql = 'update ew_shopper_demand_content set ';
        $sql.= ' status='.$data['content_status'].',';
        if($data['content_status'] == 2)//审核成功的状态要修改verify_time
        {
            $sql .= ' verify_time = NOW(),';
        }
        $sql = trim($sql,',');
        $sql.= ' where id in ('.$data['bid_id'].')';
        $res = $this->ew_conn->query($sql);
        return $res;
    }

    /*
     * 修改招投标信息order表的status
     * $data['order_status']:状态值
     * $data['bid_id']:需求id
     * $data['order_id']:自增长id
     */
    public function changeBidOrderStatus($data)
    {
        $sql = 'update ew_shopper_demand_order set ';
        if($data['order_status'] == 98)//订单失败的时候将order_step_end改为status的下一步的值
        {
            $order_item = $this->getOneOrder($data);
            $order_step_end = $this->getStepEnd($order_item[0]['status']);
            $sql .= ' order_step_end='.$order_step_end.',';
        }
        else if($data['order_status'] == 99)//关闭需求的时候将order_step_end改为当前status的值
        {
            $sql .= ' order_step_end=status, ';
        }
        $sql.= ' status='.$data['order_status'].',';
        $sql.= ' time_'.$data['order_status'].'= NOW()';
        $sql.= ' where 1';
        if(isset($data['bid_id']) && !empty($data['bid_id']))
        {
            $sql .= ' and content_id in ('.$data['bid_id'].')';
        }
        if(isset($data['order_id']) && !empty($data['order_id']))
        {
            $sql .= ' and id in('.$data['order_id'].')';
        }
        $res = $this->ew_conn->query($sql);
        return $res;
    }

    /*
     * 判断如果所有的订单都是意向书审核不通过，则将该需求改为96（自荐信审核全部不通过）
     * $data['order_id']:订单id
     */
    public function changeContentIfAll($data)
    {
        $sql_select = 'select distinct status from ew_shopper_demand_order ';
        $sql_select.= ' where content_id = ';
        $sql_select.= ' (select content_id from ew_shopper_demand_order where id='.$data['order_id'].') ';
        $res_select = $this->ew_conn->query($sql_select)->result_array();
        if(count($res_select) == 1 && $res_select[0]['status'] == 98){//如果所有的订单都是98
            $sql_upd = 'update ew_shopper_demand_content set status=96';
            $sql_upd.= ' where id = ';
            $sql_upd.= ' (select content_id from ew_shopper_demand_order where id='.$data['order_id'].') ';
            $res_upd = $this->ew_conn->query($sql_upd);
        }else{
            $res_upd = 'non';
        }
        return $res_upd;
    }


    /*
     * 订单失败的时候将order_step_end改为status的下一步的值
     * 此方法获取status下一步的值
     */
    private function getStepEnd($now_status){
        switch ($now_status) {
            case 1:
                $order_step_end = 11;
                break;
            case 11:
                $order_step_end = 21;
                break;
            case 16:
                $order_step_end = 26;
                break;
            case 21:
                $order_step_end = 31;
                break;
            case 26:
                $order_step_end = 36;
                break;
            case 31:
            case 36:
                $order_step_end = 41;
                break;
            case 41:
                $order_step_end = 51;
                break;
            case 51:
                $order_step_end = 61;
                break;
            default:
                $order_step_end = 0;
                break;
        }
        return $order_step_end;
    }

    /*
     * 修改弃单原因的审核状态
     * $data['reason_status']:弃单原因审核状态
     * $data['order_ids']:当前弃单原因对应的订单id（字符串，逗号分隔）
     */
    public function changeBidReasonStatus($data)
    {
        $sql = 'update ew_shopper_demand_order set ';
        $sql.= ' reason_status='.$data['reason_status'].',';
        $sql = trim($sql,',');
        $sql.= ' where id in ('.$data['order_ids'].')';
        $res = $this->ew_conn->query($sql);
        return $res;
    }

    /*
     * 关闭需求
     * $data['bid_ids']:需求ids（字符串，逗号分隔）
     * $data['close_reason']:关闭原因
     */
    public function closeDemand($data)
    {
        $sql_demand = 'update ew_shopper_demand_content set';
        $sql_demand.= ' status=99,';
        $sql_demand.= ' reason="'.$data['close_reason'].'"';
        $sql_demand.= ' where id in ('.$data['bid_ids'].')';
        $res_demand = $this->ew_conn->query($sql_demand);

        $sql_order = 'update ew_shopper_demand_order set';
        $sql_order.= ' order_step_end=status,';
        $sql_order.= ' status=99,';
        $sql_order.= ' time_99=NOW()';
        $sql_order.= ' where content_id in ('.$data['bid_ids'].')';
        $res_order = $this->ew_conn->query($sql_order);
        if($res_demand == true && $res_order == true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /*
     * 商家招投标管理获取列表数据
     *
     * 
     */
    public function shopperContentList($inputs,$yj){
		
        $shopper_alias = isset($inputs['shopper_alias']) ? $inputs['shopper_alias'] : '';//策划、主持人、场布、摄影、化妆   shopper_alias
        $mode = isset($inputs['mode']) ? $inputs['mode'] : '';//查找商家0
        $wed_from = isset($inputs['wed_from']) ? $inputs['wed_from'] : '';//婚礼日期1
        $wed_to = isset($inputs['wed_to']) ? $inputs['wed_to'] : '';//婚礼日期2
        $status = isset($inputs['status']) ? $inputs['status'] : '';//交易状态
        $remander_id = isset($inputs['remander_id']) ? $inputs['remander_id'] : '';//交易提示0
        $condition = isset($inputs['condition']) ? $inputs['condition'] : '';//条件查询
        $condition_text = isset($inputs['condition_text']) ? $inputs['condition_text'] : '';//条件查询域
        $lowe_amount = isset($inputs['lowe_amount']) ? $inputs['lowe_amount'] : '';//婚礼预算最小值0
        $high_amount = isset($inputs['high_amount']) ? $inputs['high_amount'] : '';//婚礼预算最大值0
        $timecon = isset($inputs['timecon']) ? $inputs['timecon'] : '';//时间条件0
        $start_time = isset($inputs['start_time']) ? $inputs['start_time'] : '';//开始时间域
        $time_end = isset($inputs['time_end']) ? $inputs['time_end'] : '';//结束时间域
        $country = isset($inputs['country']) ? $inputs['country'] : '';//国家0
        $province = isset($inputs['province']) ? $inputs['province'] : '';//城市
        $city = isset($inputs['city']) ? $inputs['city'] : '';//地区
        $where = "";
        //$where = "1=1 ";
        //根据$yj 判断查询状态
        if($yj==2){
            $where=" 1=1 ";
        }
        
        if(!empty($shopper_alias)){//策划、主持人、场布、摄影、化妆   shopper_alias
            $shopper_alias = substr($shopper_alias,0,-2);
            $where.=" and A.shopper_alias = "."'$shopper_alias'";      
        }

        //地区条件拼装
        if(!empty($country)&&!empty($province)&&!empty($city)){
            $where.=" and A.wed_location="."'$country".","."$province".","."$city'";
        }
        if(!empty($country)&&!empty($province)&&empty($city)){
            $where.=" and A.wed_location="."'$country".","."$province'";
        }
        if(!empty($country)&&empty($province)&&empty($city)){
            $where.=" and A.wed_location like '%".$country."%'";
        }
        if(!empty($mode)){//查找商家
            $where.=" and A.mode = ".$mode;
        }
        if(!empty($remander_id)){//交易提示
            $where.=" and A.remander_id = ".$remander_id;
        }
        //婚礼日期
        if(!empty($wed_from)){//婚礼日期1不为空
            $where.=" and A.wed_date >="."'$wed_from'";
        }
        if(!empty($wed_to)){//婚礼日期2不为空
            $where.=" and A.wed_date <="."'$wed_to'";
        }
        //婚礼预算
        if(!empty($high_amount)&&!empty($lowe_amount)){
            $where.=" and A.high_amount >="."'$lowe_amount'"." and A.lowe_amount <="."'$high_amount'";
        }
        if(!empty($high_amount)&&empty($lowe_amount)){//婚礼预算最大值不为空
            $where.=" and  A.lowe_amount <="."'$high_amount'";
        }
        if(!empty($lowe_amount)&&empty($high_amount)){//婚礼预算最小值不为空
            $where.=" and A.high_amount >="."'$lowe_amount'";
        }
        
        //添加时间
        if ($timecon=="create_time") {
            if(!empty($start_time)){
                $where.=" and A.create_time >="."'$start_time'";
            }
            if(!empty($time_end)){
                $where.=" and A.create_time <="."'$time_end'";
            }
        }
        //审核时间
        if ($timecon=="time_11") {
            if(!empty($start_time)){
                $where.=" and B.time_11 >="."'$start_time'";
            }
            if(!empty($time_end)){
                $where.=" and B.time_11 <="."'$time_end'";
            }
        }
        //响应时间
        if ($timecon=="time_21") {
            if(!empty($start_time)){
                $where.=" and B.time_21 >="."'$start_time'";
            }
            if(!empty($time_end)){
                $where.=" and B.time_21 <="."'$time_end'";
            }
        }
        //中标时间
        if ($timecon=="time_41") {
            if(!empty($start_time)){
               $where.=" and B.time_41 >="."'$start_time'";
            }
            if(!empty($time_end)){
                $where.=" and B.time_41 <="."'$time_end'";
            }
        }
        //完成时间
        if ($timecon=="time_51") {
            if(!empty($start_time)){
                $where.=" and B.time_51 >="."'$start_time'";
            }
            if(!empty($time_end)){
                $where.=" and B.time_51 <="."'$time_end'";
            }
        }
        //评价时间
        if ($timecon=="time_61") {
            if(!empty($start_time)){
                $where.=" and B.time_61 >="."'$start_time'";
            }
            if(!empty($time_end)){
                $where.=" and B.time_61 <="."'$time_end'";
            }
        }
        //关闭时间
        if ($timecon=="time_99") {
            if(!empty($start_time)){
                $where.=" and B.time_99 >="."'$start_time'";
            }
            if(!empty($time_end)){
                $where.=" and B.time_99 <="."'$time_end'";
            }
        }
        //条件查询
        if($condition=="客户姓名"){
            if(!empty($condition_text)){
                $where.=" and C.nickname like '%".$condition_text."%'";
            }
        }
        if($condition=="手机号码"){
            if(!empty($condition_text)){
                $where.=" and C.phone like '%".$condition_text."%'";
            }
        }
        if($condition=="交易编号"){
            if(!empty($condition_text)){
                $where.=" and A.demand_id like '%".$condition_text."%'";
            }
        }
        //交易状态
        if(!empty($status)){
            if ($status=="1") {//待分配商家
                $where.=" and A.status ="."'$status'";
            }
            if ($status=="4") {//待审核
                $where.=" and A.status ="."'$status'";
            }
            if ($status=="11") {//待商家响应
                $where.=" and B.status ="."'max(11,16)'";
            }
            if ($status=="21") {//商家投标中
                $where.=" and B.status ="."'max(21,26)'";
            }
            if ($status=="36") {//待选中标商家
                $where.=" and B.status ="."'max(31,36)'";
            }
            if ($status=="98") {//招投标完成
                $where.=" and B.status ="."'min(41,98)'";
            }
            if ($status=="99") {//已关闭
                $where.=" and A.status ="."'99'";
            }
        }
           
        if($yj==1){
            $where=" 1=1 "."$where"." and( A.status ="."'1' or A.status ="."'4')";
            //$where.="1=1 and (A.status ="."'1' or A.status ="."'4')";
        }
        
        //分页
        $page = $inputs['page'] ? $inputs['page'] : 1;
        $page = $page < 1 ? 1 : $page;
        $pagesize = $inputs['pagesize'];
        $offset = ($page-1)*$pagesize;

        $sql = "select DISTINCT A.id,A.status,A.remander_id,A.demand_id,A.wed_date,A.shopper_alias,A.create_time,A.lowe_amount,A.high_amount,C.nickname,C.phone FROM ew_shopper_demand_content as A left JOIN ew_shopper_demand_order as B ON A.id = B.content_id left JOIN ew_users as C ON A.uid = C.uid where ".$where." order by A.id desc limit ".$offset.",".$pagesize."";
        $lista=$this->ew_conn->query($sql);
        $list = $lista->result_array();
        //获取条数
        $count  = "select count(DISTINCT A.id) FROM ew_shopper_demand_content as A left JOIN ew_shopper_demand_order as B ON A.id = B.content_id left JOIN ew_users as C ON A.uid = C.uid where ".$where;


        $totala=$this->ew_conn->query($count);
        $totalaa = $totala->result_array();
        $total = $totalaa[0]['count(DISTINCT A.id)'];
        //取交易提示的数据
        $brr = $this->erp_conn->where('func_name','交易提示')->select('id,func_name')->get('ew_erp_sys_func')->row_array();
        $crr = $this->erp_conn->where('mark_id', $brr['id'])->select('id,name')->get('ew_erp_sys_trademarksetting')->result_array();

        $infos = array();
        $newArr = array();

        foreach ($list  as $key => $value) {
            $infos[$key]['id']           = $value['id']; //用户商家表需求id
            $infos[$key]['remander_id']  = $value['remander_id'] ? $value['remander_id'] : ''; //交易提示
            $infos[$key]['demand_id']    = $value['demand_id'] ? $value['demand_id'] : ''; //交易编号
            $infos[$key]['wed_date']     = $value['wed_date'] ? $value['wed_date'] : ''; //婚礼时间
            $infos[$key]['create_time']  = $value['create_time'] ? $value['create_time'] : ''; //添加时间
            $infos[$key]['lowe_amount']  = $value['lowe_amount'] ? $value['lowe_amount'] : ''; //婚礼预算最小值
            $infos[$key]['high_amount']  = $value['high_amount'] ? $value['high_amount'] : ''; //婚礼预算最大值
            $infos[$key]['nickname']     = $value['nickname'] ? $value['nickname'] : ''; //客户姓名
            $infos[$key]['phone']        = $value['phone'] ? $value['phone'] : ''; //手机号
            $infos[$key]['status']       = $value['status'] ? $value['status'] : ''; //需求状态
            $infos[$key]['shopper_alias'] = $value['shopper_alias'] ? $value['shopper_alias'] : ''; //需求状态
            foreach ($crr as $v){ //判断交易状态
                $newArr[$v['id']]['id'] = $v['id'];
                $newArr[$v['id']]['name'] = $v['name'];
                if($infos[$key]['remander_id'] == $newArr[$v['id']]['id']){
                    $infos[$key]['remander_id']=$newArr[$v['id']]['name'];
                }
           }
        }
        $result = array(
            'total' => $total,
            'rows' => $infos
        );
        //print_r($infos);die;
        return success($result);
    }
    
     //添加内部便签
     public function noteAdd($inputs){
		$rows = $this->erp_conn->insert('ew_bid_inner_note', $inputs);
        //echo $this->erp_conn->last_query();die;
        if ($rows==1) {
            return 1;
        }else{
            return 0;
        }
     }
     /*内部便签列表
      * @param $id          integer  需求id
      * @param $page        integer  当前页码数
      * @param $pagesize    integer  每页显示条数
      * @param $offset      integer  页面偏移量
     */
     public function noteList($id,$page,$pagesize,$offset){
            $list = $this->erp_conn->from('ew_bid_inner_note as A')
            ->join('ew_erp_sys_user as B', 'A.service_uid=B.id','left')
            ->select('A.n_id,A.note_content,A.demand_id,A.create_time,A.service_uid,B.username')
            ->where('A.demand_id',$id)->order_by("A.n_id", "desc")->limit($pagesize)->offset($offset)
            ->get()->result_array();
            //条数
           $count = $this->erp_conn->where("demand_id",$id)->count_all_results("ew_bid_inner_note");
           //return $this->erp_conn->last_query();
        
        return array('total' => $count, 'rows' => $list);

     }

     //添加内部便签
     public function commAdd($inputs){
        $rows = $this->erp_conn->insert('ew_bid_communicate_record', $inputs);
        //echo $this->erp_conn->last_query();die;
        if ($rows==1) {
            return 1;
        }else{
            return 0;
        }
     }

     /*沟通记录列表
      * @param $id          integer  当前的需求id
      * @param $page        integer  分页 页码 默认是1
      * @param $pagesize    integer  每页显示条数
      * @param $offset      integer  页面偏移量
     */
     public function commList($id,$page,$pagesize,$offset){
            $list = $this->erp_conn->from('ew_bid_communicate_record as A')
            ->join('ew_erp_sys_user as B','A.service_uid=B.id','left')
            ->select('A.c_id,A.start_time,A.demand_id,A.client,A.title,A.content,A.service_uid,B.username')
            ->where('A.demand_id',$id)
            ->order_by("A.start_time", "desc")
            ->limit($pagesize)
            ->offset($offset)
            ->get()->result_array();
            $infos = array();
            foreach ($list  as $key => $value) {
               $infos[$key]['id']           = $value['c_id'];
               $infos[$key]['dmid']         = $value['demand_id'];
               $infos[$key]['title']        = $value['title'];
               $infos[$key]['client']       = $value['client'];
               $infos[$key]['content']      = $value['content'];
               $infos[$key]['start_time']   = $value['start_time'];
               $infos[$key]['service_uid']  = $value['service_uid'];
               $infos[$key]['text']         = $value['username'];
            }
            $total = $this->erp_conn->where('demand_id',$id)->count_all_results("ew_bid_communicate_record");
        return array('total' => $total, 'rows' => $infos);

     }
     /*修改沟通记录数据
      *  @param $id        当前的需求id
      *  @param $inputs   修改数据的默认数据
    */
     public function commEdit($inputs,$id){
        $rows = $this->erp_conn
        ->where('c_id',$id)
        ->update('ew_bid_communicate_record', $inputs);
        if ($rows) {
            return 1;
        }else{
            return 0;
        }
     }
     
     /*
     *获取当前需求先的收支记录数据
      * @param $id          integer  当前的需求id
      * @param $page        integer  分页 页码 默认是1
      * @param $pagesize    integer  每页显示条数
      * @param $offset      integer  页面偏移量
     */
     public function payList($id,$page,$pagesize,$offset){
          $list = $this->erp_conn->from('ew_bid_payment_record as A')
            ->join('ew_erp_sys_basesetting as B','A.pay_set_id=B.id','left')
            ->select('A.p_id,A.demand_id,A.service_id,A.fund_type,A.pay_set_id,A.pay_amount,A.pay_man,A.comments,A.start_time,A.serial_number,A.inorout,B.name')
            ->where('A.demand_id',$id)
            ->order_by("A.start_time", "desc")
            ->limit($pagesize)
            ->offset($offset)
            ->get()->result_array();

           //return $this->erp_conn->last_query();die;
            $infos = array();
            foreach ($list as $key => $value) {
                $infos[$key]['id']             = $value['p_id'];
                $infos[$key]['demand_id']      = $value['demand_id']; 
                $infos[$key]['service_id']     = $value['service_id'];
                $infos[$key]['fund_type']      = $value['fund_type'];
                $infos[$key]['pay_set_id']     = $value['name'];
                $infos[$key]['pay_amount']     = $value['pay_amount'];
                $infos[$key]['pay_man']        = $value['pay_man'];
                $infos[$key]['comments']       = $value['comments'];
                $infos[$key]['serial_number']  = $value['serial_number'];
                $infos[$key]['inorout']        = $value['inorout'];
                $infos[$key]["start_time"]     = $value['start_time'];
                if ($infos[$key]['inorout']==1) {
                     $infos[$key]['inorout'] = "收";
                }elseif ($infos[$key]['inorout']==2) {
                     $infos[$key]['inorout'] = "支";
                } 
            }
            $total = $this->erp_conn->where('ew_bid_payment_record.demand_id',$id)->count_all_results("ew_bid_payment_record");
            return array('total' => $total, 'rows' => $infos);
     }
     /*
       *添加支付记录
     */
     public function payAdd($inputs){
        $rows = $this->erp_conn->insert('ew_bid_payment_record', $inputs);
        if ($rows==1) {
            return 1;
        }else{
            return 0;
        }

     }
    
    /**
     * 
     * 添加日志
     * @param $uid         integer  操作人id
     * @param $demand_id   integer  需求id
     * @param $order_id    integer  订单id
     * @param $demand_code varcher  需求号码
     * @param $order_code  varcher  商家id
     * @param $comment     varcher  备注
     * @param $action      varcher  操作动作 
     * 
     * **/
      public function demandlog($demand_id, $order_id,$order_code,$demand_code,$action='',$comment='',$o_id)
    {       
            $uid = $this->session->userdata('admin_id');
            $arr = $this->erp_conn->where('id',$uid)->get("ew_erp_sys_user");
            $list = $arr->row_array();
         
            $data = array();
            $data['operater'] = $list['username']; //操作人
            $data['time'] = date('Y-m-d H:i:s');  //创建时间
            $data['demand_id'] = $demand_id; //需求id
            $data['order_id'] = $order_id;   //商家id
            $data['demand_code'] = $demand_code; //需求号码
            $data['order_code'] = $order_code;  //订单号码
            $data['action'] = $action;   //操作动作
            $data['comment'] = $comment; //备注
            $data['o_id'] = $o_id; //订单id

            $this->erp_conn->insert('ew_bid_order_log', $data);
            //echo $this->erp_conn->last_query();
    }

       /*
       *获取日志列表数据
       */
        public function logList($id,$page,$pagesize,$offset){
           $arr = $this->erp_conn->where('demand_id',$id)->order_by('id','desc')->limit($pagesize)->offset($offset)->get('ew_bid_order_log')->result_array();
           //return $this->erp_conn->last_query();
           $total = $this->erp_conn->where('demand_id',$id)->count_all_results("ew_bid_order_log");
        return array('total' => $total, 'rows' => $arr);
    }

    /*添加日志
     *@param $u_ids          
     *@param $id             需求id
     *@param $flag           1:分配商家 2：弃单原因 3：审核需求
     */
    public function getShoppersLog($u_ids,$id,$flag){
        if($flag == 1){
        //添加分配商家日志
           $u_ids = $u_ids;
           $id = $id;
           $u_id = explode(',', $u_ids);
           $data_log = $this->ew_conn->where_in('id',$id)->select('id,demand_id')->from('ew_shopper_demand_content')->get()->row_array();
            foreach($u_id as $v){
                  $this->demandlog($data_log['id'],$v,'',$data_log['demand_id'],'分配商家','商家招投标管理-分配商家',0);
            }
        }elseif($flag==3) {
            //添加审核需求日志
            $u_id = explode(',', $u_ids);
            $brr = $this->ew_conn->where_in('id',$u_ids)->select('id,demand_id')->from('ew_shopper_demand_content')->get();
            $data_log = $brr->row_array();
            foreach($u_id as $v){
                     $this->bid->demandlog($data_log['id'],0,'',$data_log['demand_id'],'审核需求','商家招投标管理-审核需求',0);
              }
            
        }elseif ($flag==4) {
            //移除商家日志  $uid:order_id
             $u_id = explode(',', $u_ids);
             $brr = $this->ew_conn->where('id',$id)->select('id,demand_id')->from('ew_shopper_demand_content')->get();
             $data_log = $brr->row_array();
             foreach($u_id as $v){
                $this->bid->demandlog($data_log['id'],0,'',$data_log['demand_id'],'移除商家订单','商家招投标管理-移除商家订单',$v);
            }
            
        }elseif ($flag==5) {
            # 修改意向书日志
            //echo $u_ids;die;
          //  $data = $this->ew_conn->where('id',$u_ids)->select('id,demand_id')->from('ew_shopper_demand_content')->get()->row_array();
            $did = $u_ids;
            $brr = "select c.id,o.content_id,c.demand_id from ew_shopper_demand_order as o left join ew_shopper_demand_content as c on o.content_id = c.id where o.id=$did";
            $crr = $this->ew_conn->query($brr);
            $data = $crr->row_array();
           // echo $this->ew_conn->last_query();die;
            //print_r($data);die;
            $this->bid->demandlog($data['id'],0,'',$data['demand_id'],'修改意向书','商家招投标管理-修改意向书',0);

        }elseif ($flag==6) {
            # 修改弃单原因日志
            $did = $u_ids;
            $brr = "select c.id,o.content_id,c.demand_id from ew_shopper_demand_order as o left join ew_shopper_demand_content as c on o.content_id = c.id where o.id=$did";
            $crr = $this->ew_conn->query($brr);
            $data = $crr->row_array();
            $this->bid->demandlog($data['id'],0,'',$data['demand_id'],'修改弃单原因','商家招投标管理-修改弃单原因',$did);
        }elseif($flag == 2) {
                //添加弃单原因审核不通过日志
              $id = $u_ids;
              $u_id = explode(',', $u_ids); 
              $brr = "select o.content_id,o.id,c.demand_id from ew_shopper_demand_order as o left join ew_shopper_demand_content as c on o.content_id = c.id where o.id in('".$id."')";
              $crr = $this->ew_conn->query($brr);
              $data_log = $crr->row_array();
               foreach($u_id as $v){
                     $this->bid->demandlog($data_log['content_id'],0,'',$data_log['demand_id'],'弃单原因审核不通过','商家招投标管理-弃单原因审核',$v);
              }
       }elseif($flag == 7) {
                //添加弃单原因审核通过日志
              $id = $u_ids;
              $u_id = explode(',', $u_ids); 
              $brr = "select o.content_id,o.id,c.demand_id from ew_shopper_demand_order as o left join ew_shopper_demand_content as c on o.content_id = c.id where o.id in('".$id."')";
              $crr = $this->ew_conn->query($brr);
              $data_log = $crr->row_array();
               foreach($u_id as $v){
                     $this->bid->demandlog($data_log['content_id'],0,'',$data_log['demand_id'],'弃单原因审核通过','商家招投标管理-弃单原因审核',$v);
              }
       }elseif($flag == 9){
               //添加意向书审核通过日志
              $id = $u_ids;
              $u_id = explode(',', $u_ids); 
              $brr = "select o.content_id,o.id,c.demand_id from ew_shopper_demand_order as o left join ew_shopper_demand_content as c on o.content_id = c.id where o.id in('".$id."')";
              $crr = $this->ew_conn->query($brr);
              $data_log = $crr->row_array();
               foreach($u_id as $v){
                     $this->bid->demandlog($data_log['content_id'],0,'',$data_log['demand_id'],'意向书审核通过','商家招投标管理-意向书审核',$v);
              }
       }
       elseif($flag == 10){
               //添加意向书审核不通过日志
              $id = $u_ids;
              $u_id = explode(',', $u_ids); 
              $brr = "select o.content_id,o.id,c.demand_id from ew_shopper_demand_order as o left join ew_shopper_demand_content as c on o.content_id = c.id where o.id in('".$id."')";
              $crr = $this->ew_conn->query($brr);
              $data_log = $crr->row_array();
               foreach($u_id as $v){
                     $this->bid->demandlog($data_log['content_id'],0,'',$data_log['demand_id'],'意向书审核不通过','商家招投标管理-意向书审核',$v);
              }
       }
    }



    //商家招投标管理 内部便签、沟通记录、收支记录日志
    public  function ngsLog($dmid)
    {
        $data = $this->ew_conn->where('id',$dmid)->select('id,demand_id')->from('ew_shopper_demand_content')->get()->row_array();
        return $data;
    }

}
?>