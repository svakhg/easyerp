<?php
/**
 * 公用函数文件
 */

/**
 * 阿里云图片地址
 */
function get_oss_image($filename)
{
    $oss_url = config_item('img_url');
    if(empty($filename))
        return $oss_url;
    return $oss_url . $filename;
}

/**
 * 根据图片获取oss样式
 * 100w_100h_1e_1c 代表将原图缩放到一定比例后进行裁切
 * 100w_100h_0e_1c 代表不缩放直接裁切
 * x代表图片缩放倍数
 */
function get_oss_image_style($filename, $w, $h = '', $e = '', $c = '', $x = '')
{
    $oss_url = config_item('img_url');
    $style = "@{$w}w";
    '' !== $h && $style .= "_{$h}h";
    '' !== $e && $style .= "_{$e}e";
    '' !== $c && $style .= "_{$c}c";
    '' !== $x && $style .= "_{$x}x";
    return $oss_url . $filename . $style . config_item('oss_img_ext');
}

/**
 * oss资源地址
 */
function get_oss_res($filename)
{
    $oss_url = config_item('res_url');
    if(empty($filename))
        return $oss_url;
    return $oss_url . $filename;
}

/**
* 获取sina生成的短连接
*/
function get_short_url($url_long)
{
    // 处理url，url必须已http://开头
    $url_long = preg_replace('/^http:\/\//', '', trim($url_long));
    if(empty($url_long))
    {
        return array('status' => 'fail', 'long' => $url_long);
    }
    $url_long = urlencode('http://'.$url_long);


    $ch = curl_init();

    $header = array(
        'Content-Type: application/json',
    );

    // 生成请求连接
    $resquest_url = 'http://api.t.sina.com.cn/short_url/shorten.json?source=1262546948&url_long='.$url_long;
    $result = array();

    // 设置请求地址
    curl_setopt($ch, CURLOPT_URL, $resquest_url);

    // 添加请求Head内容
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    // 是否输出返回头部信息
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // 将执行结果返回
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // 设置超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);

    if(curl_errno($ch))
    {
        $result = array(
            'status' => 'fail',
            'errno' => curl_errno($ch),
            'error' => curl_error($ch)
        );
    }
    curl_close($ch);

    if(empty($result))
    {
        $response = json_decode($response, true);
        if(isset($response[0]['url_short']))
        {
            $result = array(
                'status' => 'succ',
                'short' => $response[0]['url_short'],
                'long' => $response[0]['url_long']
            );
        }
        else
        {
            $result = array(
                'status' => 'fail',
                'error' => isset($response['error']) ? $response['error'] : ''
            );
        }
    }
    return $result;
}



    if(!function_exists('floatcmp'))
    {
        /**
         * 浮点数大小比较
         *
         * @param $f1
         * @param $f2
         * @param int $precision 精度
         *
         * @return int
         *
         * $f1 > $f2: 1
         * $f1 = $f2: 0
         * $f1 < $f2: -1
         *
         */
        function floatcmp($f1, $f2, $precision = 10)
        {
            $e = pow(10, $precision);
            $i1 = intval($f1 * $e);
            $i2  = intval($f2 * $e);

            return $i1 == $i2 ? 0 : ($i1 > $i2 ? 1 : -1);
        }
    }