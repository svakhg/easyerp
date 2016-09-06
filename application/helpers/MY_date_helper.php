<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 重写覆盖timespan
 */
if ( ! function_exists('timespan'))
{
    function timespan($seconds = 1, $time = '')
    {
        $CI =& get_instance();
        $CI->lang->load('date');

        if ( ! is_numeric($seconds))
        {
            $seconds = 1;
        }

        if ( ! is_numeric($time))
        {
            $time = time();
        }

        if ($time <= $seconds)
        {
            $seconds = 1;
        }
        else
        {
            $seconds = $time - $seconds;
        }

        $str = '';
        $years = floor($seconds / 31536000);

        if ($years > 0)
        {
            $str .= $years.' '.$CI->lang->line((($years	> 1) ? 'date_years' : 'date_year')).', ';
        }

        $seconds -= $years * 31536000;
        $months = floor($seconds / 2628000);

        if ($years > 0 OR $months > 0)
        {
            if ($months > 0)
            {
                $str .= $months.' '.$CI->lang->line((($months	> 1) ? 'date_months' : 'date_month')).', ';
            }

            $seconds -= $months * 2628000;
        }

//        $weeks = floor($seconds / 604800);
//
//        if ($years > 0 OR $months > 0 OR $weeks > 0)
//        {
//            if ($weeks > 0)
//            {
//                $str .= $weeks.' '.$CI->lang->line((($weeks	> 1) ? 'date_weeks' : 'date_week')).', ';
//            }
//
//            $seconds -= $weeks * 604800;
//        }

        $days = floor($seconds / 86400);

        if ($months > 0 OR $days > 0)
        {
            if ($days > 0)
            {
                $str .= $days.' '.$CI->lang->line((($days	> 1) ? 'date_days' : 'date_day')).' ';
            }

            $seconds -= $days * 86400;
        }

        $hours = floor($seconds / 3600);

        if ($days > 0 OR $hours > 0)
        {
            if ($hours > 0)
            {
                $str .= $hours.' '.$CI->lang->line((($hours	> 1) ? 'date_hours' : 'date_hour')).' ';
            }

            $seconds -= $hours * 3600;
        }

        $minutes = floor($seconds / 60);

        if ($days > 0 OR $hours > 0 OR $minutes > 0)
        {
            if ($minutes > 0)
            {
                $str .= $minutes.' '.$CI->lang->line((($minutes	> 1) ? 'date_minutes' : 'date_minute')).', ';
            }

            $seconds -= $minutes * 60;
        }

        if ($str == '')
        {
            $str .= $seconds.' '.$CI->lang->line((($seconds	> 1) ? 'date_seconds' : 'date_second')).', ';
        }

        return substr(trim($str), 0, -1);
    }
}

/**
 * 获取指定时间距今的时间，格式化 **小时**分
 * @param string $str_time 指定的时间
 * @return string
 */
if ( ! function_exists('compare_to_now'))
{
    function compare_to_now($str_time)
    {
        if($str_time == '0000-00-00 00:00:00'){
            return 'Unkown';
        }
        $from_now = time() - strtotime($str_time);
        return timespan(1,$from_now);
        $hour = intval($from_now/3600);
//        $minutes_timestr = $from_now % 3600;
//
//        if(($minutes_timestr % 60) != 0){
//            $minutes = intval($minutes_timestr / 60);
//            $minutes = $minutes < 10 ? '0'.$minutes : $minutes;
//        }else{
//            $hour = $hour + 1;
//        }
//        return $hour."小时".$minutes."分";
    }
}


