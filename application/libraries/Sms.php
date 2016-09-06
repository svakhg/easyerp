<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * author: easywed
 * createTime: 15/10/20 20:17
 * description:发送短信类_百悟通
 */

class CI_Sms
{
    private $_ci;
    private $_sms_key = 'ToM0A2kFCc4TiGkk0y22FzV6jKQlOdC1';

    /**
     * 发送短信
     * @param array $mobile 发送手机号,数组格式,可批量
     * @param $msg_content 短信内容
     * @return array
     */
    public function send(array $mobile , $msg_content)
    {
        $this->_ci = &get_instance();
        $this->_ci->load->library('Curl');

        $sms_api = $this->_ci->config->item('ew_domain') . 'api/sms/erp-channel';

        $sms_sendtime = time();
        $sms_token = md5(implode(',' , $mobile) . $sms_sendtime . $this->_sms_key);
        $post_data = array(
            'phone' => implode(',' , $mobile),
            'message' => $msg_content,
            'sendtime' => $sms_sendtime,
            'token' => $sms_token
        );
        $respone = $this->_ci->curl->post($sms_api, $post_data);
        $resp = json_decode($respone,true);
        if($resp['error'] < 0)
        {
            return array(
                'code' => -1,
                'code_msg' => '发送失败'
            );
        }
        else
        {
            return array(
                'code' => 1,
                'code_msg' => '发送成功'
            );
        }
    }

    /**
     * 获取短信内容
     * @param string $key
     * @return mixed
     */
    public function get_sms_content($key = '')
    {
        $sms_content_arr = array(
            'send-demand' => '您有一条客户咨询，请您马上登录易结，前往订单管理中查看，48小时内接单有效。'
        );

        return $sms_content_arr[$key];
    }
	
	/**
     * 发送短信验证码
     * @param  $mobile 发送手机号
     * @param $msg_content 短信内容
     * @return array
     */
    public function sendCode($mobile)
    {
        $this->_ci = &get_instance();
        $this->_ci->load->library('Curl');

        $sms_api = $this->_ci->config->item('ew_domain') . 'erp/sms/login';
        $post_data = array(
            'phone' => $mobile,
        );
		
        $respone = $this->_ci->curl->post($sms_api, $post_data);
        $resp = json_decode($respone,true);
        if($resp['result'] == "fail")
        {
            return array(
                'code' => -1,
                'code_msg' => '发送失败'
            );
        }
        else
        {
            return array(
                'code' => 1,
                'code_msg' => '发送成功'
            );
        }
    }
	
	/**
	 * 校验验证码
	 * @param int $phone
	 * @param int $code
	 */
	public function validate($phone, $code)
	{
		if(empty($phone) || empty($code))
		{
			return false;
		}
		
		
		$this->_ci = &get_instance();
        $this->_ci->load->library('Curl');

        $sms_api = $this->_ci->config->item('ew_domain') . 'erp/sms/login-validate';
		
		$post_data = array(
			"phone" => $phone,
			"code" => $code,
		);
		$respone = $this->_ci->curl->post($sms_api, $post_data);
        $resp = json_decode($respone,true);
        if($resp['result'] == "fail")
        {
            return false;
        }
		return true;
	}
}