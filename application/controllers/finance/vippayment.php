<?php
class vippayment extends App_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('finance/vippayment_model', 'vippayment_model');
        $this->load->helper('ew_filter');
    }

    /**
     * 付费信息列表
     */
    public function index()
    {
        //获取分站列表
        $this->_data['sitelists'] = $this->vippayment_model->getSiteLists();
        
        $this->load->view('finance/vippayment_view', $this->_data);
    }

    public function search()
    {
        $params = $this->input->get();
        $params = ew_filter_quote_html($params);
        //获取分页参数
        $pagesize = intval(DD($params, 'pagesize', 20));
        $page = intval(DD($params, 'page', 1));
        $page = $page > 0 ? $page : 1;

        //搜索
        $where = "";
        if(isset($params['create_time_start']) && !empty($params['create_time_start'])) {
            $start = strtotime($params['create_time_start']);
            $where .= " and `pay_time` >= {$start}";
        }
        if(isset($params['create_time_end']) && !empty($params['create_time_end'])) {
            $end = strtotime($params['create_time_end']);
            $where .= " and `pay_time` <= {$end}";
        }

        if(isset($params['pay_status']) && !empty($params['pay_status'])) {
            $where .= " and `pay_status` = {$params['pay_status']}";
        }

        if(isset($params['pay_type']) && !empty($params['pay_type'])) {
            $where .= " and `pay_type` = {$params['pay_type']}";
        }

        if(isset($params['package']) && !empty($params['package'])) {
            $where .= " and `package` = {$params['package']}";
        }

        if(isset($params['site']) && !empty($params['site'])) {
            $resultarray = $this->ew_conn->select("uid")->where('site_id', $params['site'])->get('user_shopers')->result_array();
            if($resultarray) {
                $rastr = '';
                foreach($resultarray as $ra) {
                    $rastr .= $ra['uid'] . ',';
                }
                $rastr = rtrim($rastr, ',');
                $where .= " and `uid` in ({$rastr})";
            }
            else {
                $temp = -1;
                $where .= " and `uid` = {$temp}";
            }
        }

        if(isset($params['phone']) && !empty($params['phone'])) {
            $resultarray2 = $this->ew_conn->select("uid")->like('phone', $params['phone'])->get('users')->result_array();
            if($resultarray2) {
                $rastr2 = '';
                foreach($resultarray2 as $ra) {
                    $rastr2 .= $ra['uid'] . ',';
                }
                $rastr2 = rtrim($rastr2, ',');
                $where .= " and `uid` in ({$rastr2})";
            }
        }

        if(isset($params['numbers']) && !empty($params['numbers'])) {
            $where .= " and `numbers` like '%{$params['numbers']}%'";
        }

        $where = ltrim($where,' and');
        if(!$where){
            $where = array();
        }

        $list = $this->vippayment_model->getLists($where, $page, $pagesize);

        $info = array(
            'total' => $list['total'],
            'rows' => $list['Lists']
        );
        //echo '<pre>';
        //var_dump($where);
        //exit;
        return success($info);
    }


    /**
     * 添加付费信息
     */
    public function addVippayment()
    {
        $data = $this->input->post();
        $data = ew_filter_quote_html($data);
        $phone = $data['phone'];
        $type = $data['shopperType'];

        $info = $this->ew_conn->select("uid, dostatus")->where('phone', $phone)->where('is_company', $type)->get('users')->row_array();
        if(!isset($info['uid'])) {
            return failure('该商家非易结注册商家，请核对后重新提交');
        }
        else {
            $data['uid'] = $info['uid'];

            if($info['dostatus'] != 2 && $info['dostatus'] != 100) {
                return failure('只有只可登录和正常状态商家可成为付费会员，请确认该商家状态');
            }

            $data_vip = $this->ew_conn->where('uid',$data['uid'])->select('serves')->get('user_shopers')->row_array();
            $serves = isset($data_vip['serves']) ? $data_vip['serves'] : '';
            if(strpos($serves, '1435') === false) {
                return failure('该商家服务项非婚礼策划，请核对后重新提交');
            }
        }
        $data['pay_time'] = strtotime($data['pay_time']);
        $data['valid_until'] = strtotime($data['valid_until']);

//        $maxpay = $this->ew_conn->where('uid',$data['uid'])->select_max('valid_until')->get('shoper_order')->row_array();
//        $basecounter = $data['pay_time'];
//        if(isset($maxpay['valid_until'])) {
//            $basecounter = $maxpay['valid_until'];
//        }
//
//        $data['valid_until'] = strtotime('+' . $data['package'] . ' month', $basecounter);
//        $data['valid_until'] = strtotime('+14 day', $data['valid_until']);
        $data['create_time'] = time();
        $data['record_uid'] = $this->session->userdata("admin_id");
        $data['pay_type'] =  1;
        $data['pay_status'] =  2;
        $data['numbers'] = date('Ymdhis').rand('100000','999999');

        unset($data['phone']);
        unset($data['shopperType']);
        $this->ew_conn->trans_start();
        $res = $this->vippayment_model->addVippayment($data);

        //修改会员付费状态
        $this->ew_conn->where('uid', $data['uid'])->where('vip_status != ', 2)->update('user_shopers', array('vip_status' => 2));

        $this->ew_conn->trans_complete();
        if($res){
            return success('添加成功');
        }else{
            return failure('添加失败');
        }
    }


    /**
     * 修改商家
     */
    public function editVippayment()
    {

    }

}

