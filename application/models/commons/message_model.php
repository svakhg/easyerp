<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model 发短信文案
 */

class Message_model extends MY_Model{

    //顾问工具-给客人推荐酒店短信
	public function tools_guest($data)
	{
		$content = '您好，我是易结婚礼顾问'.$data['follower'].'（'.$data['follower_mobile'].'），根据您的需求，已经为您推荐了n个场地：酒店名称1（详情：酒店1链接，电话：xxxx），酒店名称2（详情：酒店2链接，电话：xxxx）。我们已安排酒店工作人员专门接待您，您可以随时预约看店，也可以联系我为您预约。';
		return $content;	
	}
	//顾问工具-给销售客户信息
	public function tools_sales($data)
	{
       if(empty($data['wed_date'])){
           $data['wed_date'] = 'xxxx-xx-xx';
       }
        $content = '经理您好:我是易结婚礼顾问'.$data['follower'].'（'.$data['follower_mobile'].'），我们已将您的酒店推荐给易结客户'.$data['username'].'（电话：'.$data['mobile'].'，婚期：'.$data['wed_date'].'，预计桌数：n桌），客户对酒店很有兴趣，希望您方便的时候联系客户，邀请ta看店。';
		return $content;
	}
	//顾问工具-给客户挽救信息
	public function tools_save($data)
	{
		$content = '您好，我是刚刚跟您沟通过的婚礼顾问'.$data['follower'].'（电话：'.$data['follower_mobile'].'）。易结可以为您提供婚礼场地预定和婚礼策划这两项服务，您可以点击m.easywed.cn/opus 查看易结的婚礼案例。我们还提供了免费电子请柬( t.cn/RUD5JZv )供您使用，如您对易结的婚礼服务有兴趣，请与我联系。';
		return $content;
	}
	//已建单状态给客户发短信 (客户类型为 C2,C6)
    public function build_c2()
	{
		$content = '您好，非常感谢您选择易结！易结专注婚礼行业已有8年，我们根据新人所需、按照服务标准甄选出TOP10%的优秀策划师1对1提供婚礼定制服务。稍后，会有新人顾问一对一帮助您选择策划师，请保持电话畅通。了解更多关于易结，点击http://t.cn/RUDrhQN。';
		return $content;
	}
	//已建单状态给客户发短信 (客户类型为 C4、C7)
    public function build_c4()
	{
		$content = '您好，非常感谢您选择易结！易结有800+合作婚宴酒店，档期可优先选择，我们可以高效精准的为您匹配到符合您需求的婚宴场地。了解更多关于易结，点击http://t.cn/RUDrhQN。';
		return $content;
	}
    //已建单状态给客户发短信 (客户类型为 C5、C5&C6、)
    public function build_c5()
	{
		$content = '您好，非常感谢您选择易结！易结专注婚礼行业已有8年，我们根据新人所需、按照服务标准甄选出TOP10%的优秀策划师1对1提供婚礼策划服务。为了帮助您更好地筹备婚礼，稍后会有新人顾问与您联系，帮您推荐符合您需求的婚宴场地。了解更多关于易结，点击http://t.cn/RUDrhQN。';
		return $content;
	}

	
	
}