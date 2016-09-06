<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Detail
 * erp  详情处理（录入，详情，编辑） 逐步脱离从主站CURL方式获取数据
 */

class Detail extends MY_Model{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 录入一条需求的信息
     *
     */
    public function InsertOneDemand($data){

        if($data['base']['status'] != 3){//增加保存为草稿
            if(empty($data['wed_info']['type'])){
                return array('code' => 5, 'message' => '请选择类型（一站式或是单项）');
            }
            if(empty($data['base']['mode'])){
                return array('code' => 5, 'message' => '请选择找商家方式（招投标或是制定商家）');
            }
            if(empty($data['base']['cli_mobile'])){
                return array('code' => 5, 'message' => '请输入客户电话');
            }
        }
        $arr['type'] = $data['wed_info']['type'];
        $arr['mode'] = $data['base']['mode'];
        $arr['userinfo'] = $data['base'];
        $arr['userinfo']['nick_name'] = $data['base']['cli_name'];
        $arr['userinfo']['mobile'] = $data['base']['cli_mobile'];

        $arr['userlink']['mobile'] = $data['base']['cli_mobile'];
        $arr['userlink']['qq'] = $data['base']['cli_qq'];
        $arr['userlink']['weixin'] = $data['base']['cli_weixin'];

        if(isset($data['wedplanners'])){
            $arr['wedplanners'] = $data['wedplanners'];
        }
        if(isset($data['wedmaster'])){
            $arr['wedmaster'] = $data['wedmaster'];
        }
        if(isset($data['wedphotoer'])){
            $arr['wedphotoer'] = $data['wedphotoer'];
        }
        if(isset($data['wedvideo'])){
            $arr['wedvideo'] = $data['wedvideo'];
        }
        if(isset($data['makeup'])){
            $arr['makeup'] = $data['makeup'];
        }
        if(isset($data['sitelayout'])){
            $arr['sitelayout'] = $data['sitelayout'];
        }
        $arr['shopper_ids'] = isset($data['shopper_ids']) ? $data['shopper_ids'] : array();
        //传给model的验证码，此验证码直接跳过验证
        $arr['userinfo']['sms'] = '13800000000';
        $arr['wedinfo'] = $data['wed_info'];
        if($arr['type'] == 1){//一站式保存需求
            $res = $this->insertOneStopDemand($arr);
        }elseif($arr['type'] == 2){//单项式保存需求
            $res = Demandsforerp_Process::insertMonomialDemand($arr);
        }
        echo json_encode($res);exit;
    }


    /*
     * 录入一条一站式需求的信息
     * 1. 验证输入信息是否完成
     * 2. 根据手机号验证用户是否存在, 如果用户已经存在, 则查看用户是否发布过需求
     * 3. 用户不存在, 自动为用户注册
     * 4. 将需求插入数据库
     * 5. 如果用户指定了商家,将生成一个订单
     *
     * @param array $params 需求详细信息
     * @param int $appoint 是否指定商家
     *
     * @return int 需求ID
     * @throw Exception
     *
     */
    public function insertOneStopDemand($params)
    {
        print_r($params);die();
        //try {
        if($params['type'] != 1){
            return array('massage' => '需求类型错误', 'code' => 5);
        }
        $wedInfo = $params[ 'wedinfo' ];
        //self::_validWedinfo($params[ 'type' ], $wedInfo);

        // 一站式
        //self::_validateShopperQuestions(self::WEDPLANNERS_ALIAS, $params);

        $uid = Auth::id();
        $userInfo = $params[ 'userinfo' ];
        if(!$uid){
            $this->_validUserInfo($userInfo);
        }

        DB::beginTransaction();
        if(!$uid){
            // 检测并注册新用户
            $password = User::genPassword();
            $content = sprintf(Config::get('sms.template.register-in-demand'), $password);
            $user = User::autoRegister($userInfo[ 'nick_name' ], $userInfo[ 'mobile' ], $password, User::TYPE_USER, $content);

            // 如果用户存在,检查是否发不过需求
            //$demandObj = Demandsforerp_Content::where('uid', $user['uid'])->first();
            //if(!empty($demandObj)){
            //  return array('massage' => '您已经发布过需求', 'code' => 5);
            //}

            // 自动为用户登录
            try{
                User::login($userInfo['mobile'], $password);
            }catch(Exception $e){

            }

            $uid = $user['uid'];
        } else {
            $userSimpleInfo = User::find($uid)->toArray();
            $params['userinfo']['mobile'] = $userSimpleInfo['phone'];
            $params['userinfo']['nick_name'] = $userSimpleInfo['nickname'];
        }
        $params['uid'] = $uid;
        if($params['mode']==1){//招投标
            $params['order_status'] = 41;//新人一站式流程修改（一站式审核通过直接变为41）
        }elseif($params['mode']==2){//指定商家
            $params['order_status'] = 41;//路光卿让修改为招投标也是41（又改回来了，坑啊）
        }
        $params['status'] = 1;

        $result = Demandsforerp_Content::addGetID($params);
        $res_qa = Demandsforerp_QA::add(self::WEDPLANNERS_ALIAS, $result['id'], $params);

        $res_order['order'] = self::insertOneOrder($params,$result['id'],self::WEDPLANNERS_ALIAS);
        //需求和订单合并
        $res['onestop'][] = array_merge($result,$res_order);
        DB::commit();
        return $res;
        //} catch (Exception $e) {
        //    DB::rollBack();
        //    throw new Exception($e->getMessage(), $e->getCode());
        //}
    }

    /*
 * 验证联系方式
 *
 */
    public function _validUserInfo($userInfo, $userExists = 0)
    {
        if (empty($userInfo[ 'channel' ])) {
            return array('massage' => '获知渠道错误', 'code' => 5);
        }

        if(!$userExists){
            if (empty($userInfo[ 'nick_name' ])) {
                return array('massage' => '用户昵称错误', 'code' => 5);
            }
            if (strlen($userInfo[ 'mobile' ]) !== 11) {
                return array('massage' => '手机号错误', 'code' => 5);
            }
            if (empty($userInfo[ 'sms' ])) {// erp指定验证码'13800000000'
                return array('massage' => '验证码输入错误', 'code' => 5);
            }
            if($userInfo[ 'sms' ] != '13800000000' && Config::get('app.sms_code_validate')) {
                $code = Sms_Code::validate($userInfo['mobile'], $userInfo['sms']);
                if(!$code){
                    return array('massage' => '验证码不正确', 'code' => 4);
                }
            }
        }
    }

}