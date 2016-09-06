<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 返回的结果 helper
 *
 */
if ( ! function_exists('success'))
{
	function success($info)
    {
        if (is_array($info)) {
            $desc = $info;
        } elseif ($info == '') {
            $desc = true;
        } else {
            $desc = $info;
        }
        $result = array('result' => 'succ', 'info' => $desc);
        $CI = & get_instance();
        return $CI->output->set_content_type('application/json')->set_output(json_encode($result));
    }
}

if (! function_exists('failure'))
{
    function failure($info)
    {
        $desc = $info == '' ? false : $info;
        $result = array('result' => 'fail', 'info' => $desc);
        $CI = & get_instance();
        return $CI->output->set_content_type('application/json')->set_output(json_encode($result));
    }
}
