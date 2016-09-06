<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *帖子板块模型
 */
class vippayment_model extends MY_Model
{
    const TBL = 'shoper_order';
    const TB_USERS = 'users';
    const TB_USERSHOPERS = 'user_shopers';
    const TB_ADMINUSERS = 'admin_users';

    /**
     * 分页每页条数
     */
    const PAGESIZE = 10;

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     *获取列表
     */
    public function getLists($where = "" , $page = 1, $pagesize = self::PAGESIZE){
        //$this->load->model('common_model', 'common');
        $offset = ($page - 1) * $pagesize;
        $db = $this->ew_conn->where($where);
        $dbobj = clone $db;
        $total = $dbobj -> count_all_results(self::TBL);
        $result = $db->order_by('create_time desc')->order_by('id desc ')->limit($pagesize)->offset($offset)->get(self::TBL)->result_array();
        //var_dump($where);var_dump($result);die;
        foreach ($result as $key => &$value) {
            //获取BD昵称
            $data1 = $this->ew_conn->where('id',$value['bd_uid'])->select('nickname')->get(self::TB_ADMINUSERS)->row_array();
            $value['bd_nickname'] = isset($data1['nickname']) ? $data1['nickname'] : '';
            //获取录入人昵称
            $data2 = $this->erp_conn->where('id',$value['record_uid'])->select('username')->get('erp_sys_user')->row_array();
            $value['record_nickname'] = isset($data2['username']) ? $data2['username'] : '';
            //获取商家信息
            $data_vip = $this->ew_conn->where('uid',$value['uid'])->select('site_id, serves')->get(self::TB_USERSHOPERS)->row_array();
            $value['site_id'] = isset($data_vip['site_id']) ? $data_vip['site_id'] : '';
            $value['serves'] = isset($data_vip['serves']) ? $data_vip['serves'] : '';
            $value['site'] = '';
            if($value['site_id'])
            {
                $result2 = $this->ew_conn->where('id', $value['site_id'])->select('name')->get('sites')->row_array();
                $value['site'] = isset($result2['name']) ? $result2['name'] : '';
            }

            $value['pay_time'] = date('Y-m-d H:i:s' , $value['pay_time']);
            $value['create_time'] = date('Y-m-d H:i:s' , $value['create_time']);
            $value['valid_until'] = date('Y-m-d H:i:s' , $value['valid_until']);

            $ids = trim($value['serves'], ',');
            if(empty($ids)){
                $value['serves_name'] = '';
            }
            else {
                $sql = " SELECT option_name  FROM (`ew_options`) WHERE `option_type` = 1 AND `id` IN (" . $ids . ")";
                $value['serves_name'] = $this->ew_conn->query($sql)->result_array();
                $value['serves_name'] = $value['serves_name'] ? array_flatten($value['serves_name'], 'option_name') : array();
                $value['serves_name'] = implode('/', $value['serves_name']);
            }

            $user_info = $this->ew_conn->select("username, nickname, phone")->where('uid', $value['uid'])->get('users')->row_array();
            $value['nickname'] = isset($user_info['nickname']) ? $user_info['nickname'] : '';
            $value['phone'] = isset($user_info['phone']) ? $user_info['phone'] : '';
        }
        if($result){
            return array(
                'total' => $total,
                'Lists' => $result
            );
        }else{
            return array(
                'total' => 0,
                'Lists' => array()
            );
        }
    }
    /**
     *修改帖子状态
     */
    public function editPostStatus($id,$status){
        $result = $this->ew_conn->where('id',$id)->update(self::TBL,array('status'=>$status));
        return $result;
    }
    /**
     *添加帖子
     *@param array $data 帖子文本信息
     *@param array $images 帖子图片信息
     */
    public function addVippayment($data){
        $result = $this->ew_conn->insert(self::TBL, $data);
        if($result){
            //更新商家状态
        }
        return $result;
    }
    /**
     *删除帖子
     */
    public function deletePost($id, $status, $reason){
        $this->ew_conn->trans_start();
        if($status == 3) {
            $result = $this->ew_conn->where("id", $id)->update(self::TBL, array("status" => $status, "reason" => $reason));
        }
        if($status == 4) {
            $result = $this->ew_conn->where("id", $id)->update(self::TBL, array("status" => $status, "reason" => $reason, "is_del" => 2));
        }
        $this->ew_conn->where('post_id',$id)->update(self::TB_JUDGE, array("is_del" => 2));
        $this->ew_conn->trans_complete();

        $result = (! empty($result)) ? $result : array();
        return $result;
    }
    /**
     *修改帖子
     */
    public function editPost($id,$data,$images=null){
        if(empty($id) || empty($data))
        {
            return array();
        }

        $result = $this->ew_conn->where("id", $id)->update(self::TBL, $data);
        if($result){
            if(!is_null($images)){
                $this->ew_conn->delete(self::TB_POST_IMAGE, array("post_id" => $id));
                $info = array();
                foreach ($images as $key => $value) {
                    $info[$key]['post_id'] = $id;
                    $info[$key]['url'] = $value;
                }
                $res = $this->ew_conn->insert_batch(self::TB_POST_IMAGE,$info);
                return $res;
            }else{
                return $result;
            }
        }
        return $result;
    }
    /**
     * 获取帖子详情
     */
    public function getPostDetail($id){
        // 载入公共函数
        //$this->load->helper('functions');
        $postDetail = $this->ew_conn->where('id',$id)->get(self::TBL)->row_array();
        if(!empty($postDetail)){
            //获取用户昵称
            $data = $this->ew_conn->where('uid',$postDetail['uid'])->select('nickname')->get(self::TB_USERS)->row_array();
            $postDetail['nickname'] = isset($data['nickname']) ? $data['nickname'] : '';
            //获取板块名称
            //$topicData = $this->db->where('id',$postDetail['topic_id'])->select('title')->get(self::TB_TOPIC)->row_array();
            //$postDetail['topic_title'] = $topicData['title'];
            //获取图片列表
            //if($postDetail['is_image'] == 1){
            $imagesData = $this->ew_conn->where('post_id',$postDetail['id'])->get(self::TB_POST_IMAGE)->result_array();
            if(!empty($imagesData)) {
                foreach ($imagesData as $key => $value) {
                    $postDetail['images']['url'][] = get_oss_image($value['url']);
                    $postDetail['images']['path'][] = $value['url'];
                }
            }
            else{
                $postDetail['images'] = array();
            }
            //创建时间格式化
            $postDetail['create_time'] = date('Y-m-d H:i:s',$postDetail['create_time']);
            //增加该帖子下评论详情列表
            $postDetail['judgeList'] = $this->ew_conn->where('post_id',$postDetail['id'])->order_by('floor asc')->get(self::TB_JUDGE)->result_array();
        }else{
            return array();
        }
        return $postDetail;
    }

    public function getSiteLists(){
        return $this->ew_conn->select('id, name')->get('sites')->result_array();
    }
}