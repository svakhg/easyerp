<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Curl {
/**
* get 方式获取访问指定地址
* @param  string url 要访问的地址
* @param  string cookie cookie的存放地址,没有则不发送cookie
* @return string curl_exec()获取的信息
* @author andy
**/
    public function get( $url, $cookie='' )
    {
        //增加签名验证
        $params_str = explode('?',$url);
        if(isset($params_str[1]))
        {
            parse_str($params_str[1],$params);
            $url = $url.'&sign='.self::sign($params);
        }
        // 初始化一个cURL会话
        $curl = curl_init($url);
        // 不显示header信息
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 使用自动跳转
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        if(!empty($cookie)) {
         // 包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。
         curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
        }
        // 自动设置Referer
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        // 执行一个curl会话
        $tmp = curl_exec($curl);
        // 关闭curl会话
        curl_close($curl);
        return $tmp;
    }
/**
* post 方式模拟请求指定地址
* @param  string url 请求的指定地址
* @param  array  params 请求所带的
* #patam  string cookie cookie存放地址
* @return string curl_exec()获取的信息
* @author andy
**/
    public function post( $url, $params, $cookie = '' )
    {
        //增加签名认证
        $params['sign'] = self::sign($params);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        //模拟用户使用的浏览器，在HTTP请求中包含一个”user-agent”头的字符串。
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        //发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
        curl_setopt($curl, CURLOPT_POST, 1);
        // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 使用自动跳转
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
        // 自动设置Referer
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        // Cookie地址
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
        // 全部数据使用HTTP协议中的"POST"操作来发送。要发送文件，
        // 在文件名前面加上@前缀并使用完整路径。这个参数可以通过urlencoded后的字符串
        // 类似'para1=val1¶2=val2&...'或使用一个以字段名为键值，字段数据为值的数组
        // 如果value是一个数组，Content-Type头将会被设置成multipart/form-data。
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
/**
* 远程下载
* @param string $remote 远程图片地址
* @param string $local 本地保存的地址
* @param string $cookie cookie地址 可选参数由
* 于某些网站是需要cookie才能下载网站上的图片的
* 所以需要加上cookie
* @return void
* @author andy
*/
    public function reutersload($remote, $local, $cookie= '') {
        $cp = curl_init($remote);
        $fp = fopen($local,"w");
        curl_setopt($cp, CURLOPT_FILE, $fp);
        curl_setopt($cp, CURLOPT_HEADER, 0);
        if($cookie != '') {
         curl_setopt($cp, CURLOPT_COOKIEFILE, $cookie);
        }
        curl_exec($cp);
        curl_close($cp);
        fclose($fp);
    }
/**
 * erp接口交互简易md5数据签名
 * sign参数不参与签名
 * 签名规则： 根据数组参数的key排序，然后取前三个参数的值，若不足三个参数，则取使用所有的参数值
 * 
 * 注意：在URL重写的情况下，在接收方获取参数时可能会有多余的controller action等参数，根据实际情况过滤该值。
 */
public static $_key = '$LKJsdfoilkj390ujIO$#lkjlkfsd*((*J';
    
    /**
     * md5加密
     */
    public static function sign(array $params)
    {
        if(empty($params) || !is_array($params))
        {
            return false;
        }
        
        $sortData = self::_sortParams($params);
        return md5(self::_signString($sortData));
    }
    
    /**
     * 签名认证 
     * @params  array   $params 带签名数组
     *          string  $sign   接受的签名字符串
     */
    public static function validate(array $params, $sign)
    {
        $sortData = self::_sortParams($params);
        if($sign !== md5(self::_signString($sortData)))
        {
            return false;
        }
        return true;
    }
    
    /**
     * 参数排序
     * @params  array   $params     待排序参数
     */
    private static function _sortParams(array $params)
    {
        foreach ($params as $key => $val)
        {
            $key !== 'sign' && $data[$key] = $val;
        }
        ksort($data);
        return $data;
    }
    
    /**
     * 生成待签名字符串
     * @params  array   $params _sortParams排序过后的数组
     */
    private static function _signString(array $params)
    {
        if(empty($params))
        {
            return false;
        }
        $str = '';
        $num = 0;
        foreach ($params as $val)
        {
            if(!$num >= 3)
            {
                break;
            }
            if('' != $val)
            {
                $str .= $val;
                $num += 1;
            }
        }
        return ($str . self::$_key);
    }
}