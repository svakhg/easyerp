<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 拆分url
 */
if ( ! function_exists('explode_url'))
{
	function explode_url($url_array)
    {
        $url_temp = $url_array;
        array_pop($url_array);
        $controller = implode('/', $url_array);
        $action = array_shift(array_reverse($url_temp));
        return array(0 => $controller, 1 => $action);
    }
}


if ( ! function_exists('array_pull'))
{
    /**
     * Get a value from the array, and remove it.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function array_pull(&$array, $key, $default = null)
    {
        $value = array_get($array, $key, $default);

        array_forget($array, $key);

        return $value;
    }
}


if ( ! function_exists('array_get'))
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_array($array) || ! array_key_exists($segment, $array))
            {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}


if ( ! function_exists('array_forget'))
{
    /**
     * Remove an array item from a given array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @return void
     */
    function array_forget(&$array, $key)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            if ( ! isset($array[$key]) || ! is_array($array[$key]))
            {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }
}

if ( ! function_exists('array_flatten'))
{
	/**
	*数组扁平化
	*
	*/
	function array_flatten( array $data,$content)
	{
	  $info = array();

	  foreach ($data as $key => $value) {
		if(!isset($value[$content])){
		  return false;
		}
		$info[] = $value[$content];
	  }
	  return $info;
	}
}


if ( ! function_exists('toHashmap'))
{
	/**
	 * 将一个二维数组转换为 HashMap，并返回结果
	 *
	 * 用法1：
	 * @code php
	 * $rows = array(
	 *     array('id' => 1, 'value' => '1-1'),
	 *     array('id' => 2, 'value' => '2-1'),
	 * );
	 * $hashmap = Helper_Array::hashMap($rows, 'id', 'value');
	 *
	 * dump($hashmap);
	 *   // 输出结果为
	 *   // array(
	 *   //   1 => '1-1',
	 *   //   2 => '2-1',
	 *   // )
	 * @endcode
	 *
	 * 如果省略 $value_field 参数，则转换结果每一项为包含该项所有数据的数组。
	 *
	 * 用法2：
	 * @code php
	 * $rows = array(
	 *     array('id' => 1, 'value' => '1-1'),
	 *     array('id' => 2, 'value' => '2-1'),
	 * );
	 * $hashmap = Helper_Array::hashMap($rows, 'id');
	 *
	 * dump($hashmap);
	 *   // 输出结果为
	 *   // array(
	 *   //   1 => array('id' => 1, 'value' => '1-1'),
	 *   //   2 => array('id' => 2, 'value' => '2-1'),
	 *   // )
	 * @endcode
	 *
	 * @param array $arr 数据源
	 * @param string $key_field 按照什么键的值进行转换
	 * @param string $value_field 对应的键值
	 *
	 * @return array 转换后的 HashMap 样式数组
	 */
	function toHashmap($arr, $key_field, $value_field = null)
	{
		$ret = array();
		if ($value_field)
		{
			foreach ($arr as $row)
			{
				$ret[$row[$key_field]] = $row[$value_field];
			}
		}
		else
		{
			foreach ($arr as $row)
			{
				$ret[$row[$key_field]] = $row;
			}
		}
		return $ret;
	}
}
	
