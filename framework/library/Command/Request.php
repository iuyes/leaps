<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Command_Request {
	/**
	 * 解析cli参数
	 *
	 * @return string
	 */
	public static function parse_cli_args() {
		$args = array_slice ( $_SERVER ['argv'], 1 );
		return $args ? '/' . implode ( '/', $args ) : '';
	}

	/**
	 * 获得用户请求的数据
	 *
	 * 返回$_GET,$_POST的值,未设置则返回$defaultValue
	 *
	 * @param string $key 获取的参数name,默认为null将获得$_GET和$_POST两个数组的所有值
	 * @param mixed $defaultValue 当获取值失败的时候返回缺省值,默认值为null
	 * @return mixed
	 */
	public static function get_request($key = null, $default = null) {
		if (is_null ( $key )) return array_merge ( $_POST, $_GET );
		if (isset ( $_GET [$key] )) return $_GET [$key];
		if (isset ( $_POST [$key] )) return $_POST [$key];
		return $default;
	}
}