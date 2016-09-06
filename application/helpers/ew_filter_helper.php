<?php
/**
 * author: easywed
 * createTime: 15/10/14 13:38
 * description:过滤数据公共类
 */

if(!function_exists('ew_filter_quote'))
{
    /**
     * 转义html代码,单双引号,及反斜杠
     * @param array $filter_data
     * @return array
     */
    function ew_filter_quote_html($filter_data = array())
    {
        if(!is_array($filter_data))return $filter_data;

        foreach($filter_data as $key => &$val)
        {
            $val = htmlspecialchars(mysql_real_escape_string(trim($val)));
        }

        return $filter_data;
    }
}

if(!function_exists('DD'))
{
    /**
     * 判断数组中是否有key为$key的值，没有则返回$default_val
     * @param $data_arr
     * @param $key
     * @param string $default_val
     * @return string
     */
    function DD($data_arr , $key , $default_val = '')
    {
        return isset($data_arr[$key]) ? $data_arr[$key] : $default_val;
    }
}

if(!function_exists('EE'))
{
    /**
     * 值为空则返回设置的默认值
     * @param $value
     * @param $default_val
     * @return mixed
     */
    function EE($value , $default_val)
    {
        return !empty($value) ? $value : $default_val;
    }
}