<?php
/**
 * erp接口交互简易md5数据签名
 * sign参数不参与签名
 * 签名规则： 根据数组参数的key排序，然后取前三个参数的值，若不足三个参数，则取使用所有的参数值
 * 
 * 注意：在URL重写的情况下，在接收方获取参数时可能会有多余的controller action等参数，根据实际情况过滤该值。
 */
class ErpHash
{
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
