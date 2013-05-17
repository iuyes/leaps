<?php
/**
 * 核心函数库
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-15
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: func.php 558 2013-05-17 06:37:38Z 85825770@qq.com $
 */
define ( 'CORE_FUNCTION', true );

/**
 * 返回经addslashes处理过的字符串或数组
 *
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string) {
	if (! is_array ( $string )) return addslashes ( $string );
	foreach ( $string as $key => $val )
		$string [$key] = new_addslashes ( $val );
	return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 *
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
	if (empty ( $string )) return $string;
	if (! is_array ( $string )) {
		return stripslashes ( $string );
	} else {
		foreach ( $string as $key => $val ) {
			$string [$key] = new_stripslashes ( $val );
		}
	}
	return $string;
}

/**
 * 返回经htmlspecialchars处理过的字符串或数组
 *
 * @param $obj 需要处理的字符串或数组
 * @return mixed
 */
function new_htmlspecialchars($string) {
	if (! is_array ( $string )) return htmlspecialchars ( $string );
	foreach ( $string as $key => $val )
		$string [$key] = new_htmlspecialchars ( $val );
	return $string;
}

/**
 * 错误日志接口
 *
 * @param string $level 日志级别
 * @param string $message 日志信息
 * @param boolean $php_error 是否是PHP错误
 */
function log_message($level = 'error', $message, $php_error = FALSE) {
	if (C ( 'log', 'log_threshold' ) == 0) return;
	Log::get_instance ()->write ( $level, $message, $php_error );
}

/**
 * 载入文件或类
 *
 * @param string $name 文件名称 或带路径的文件名称
 * @param string $folder 文件夹默认为空
 */
function import($name, $folder = '') {
	return Core::import ( $name, $folder );
}

/**
 * 加载配置文件
 *
 * @param string $file 文件名
 * @param string $key 配置项
 * @param string/bool $default 默认值
 */
function C($file, $key = null, $default = false) {
	return Config::get ( $file, $key, $default );
}

/**
 * 记录加载和运行时间
 *
 * @param string $start
 * @param string $end
 * @param int $dec
 */
function G($start, $end = '', $dec = 3) {
	static $_info = array ();
	if (! empty ( $end )) { // 统计时间
		if (! isset ( $_info [$end] )) $_info [$end] = microtime ( TRUE );
		return number_format ( ($_info [$end] - $_info [$start]), $dec );
	} else { // 记录时间
		$_info [$start] = microtime ( TRUE );
	}
}

/**
 * 设置和获取统计数据
 *
 * @param string $key 要统计的项
 * @param int $step 递加的值
 * @return int 如果递加的值为空返回目前该项统计到的次数
 */
function N($key, $step = 0) {
	static $_num = array ();
	if (! isset ( $_num [$key] )) {
		$_num [$key] = 0;
	}
	if (empty ( $step ))
		return $_num [$key];
	else
		$_num [$key] = $_num [$key] + ( int ) $step;
}

/**
 * 全局缓存读取、设置、删除，默认为文件缓存。
 *
 * @param string $key 缓存名称
 * @param string $value 缓存内容
 * @param int $expires 缓存有效期
 * @param string $options 缓存配置
 */
function S($key, $value = null, $expires = 0, $options = null) {
	if (is_null ( $value )) { // 获取缓存
		return Factory::cache ( $options )->get ( $key );
	} elseif ($value === '') { // 删除缓存
		return Factory::cache ( $options )->delete ( $key );
	} else {
		return Factory::cache ( $options )->set ( $key, $value, $expires );
	}
}

/**
 * 浏览器友好的变量输出
 *
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void string
 */
function dump($var, $echo = true, $label = null, $strict = true) {
	$label = ($label === null) ? '' : rtrim ( $label ) . ' ';
	if (! $strict) {
		if (ini_get ( 'html_errors' )) {
			$output = print_r ( $var, true );
			$output = '<pre>' . $label . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
		} else {
			$output = $label . print_r ( $var, true );
		}
	} else {
		ob_start ();
		var_dump ( $var );
		$output = ob_get_clean ();
		if (! extension_loaded ( 'xdebug' )) {
			$output = preg_replace ( '/\]\=\>\n(\s+)/m', '] => ', $output );
			$output = '<pre>' . $label . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
		}
	}
	if ($echo) {
		echo ($output);
		return null;
	} else
		return $output;
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 *
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type = 0) {
	if ($type) {
		return ucfirst ( preg_replace ( "/_([a-zA-Z])/e", "strtoupper('\\1')", $name ) );
	} else {
		return strtolower ( trim ( preg_replace ( "/[A-Z]/", "_\\0", $name ), "_" ) );
	}
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 *
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
	if (is_object ( $mix ) && function_exists ( 'spl_object_hash' )) {
		return spl_object_hash ( $mix );
	} elseif (is_resource ( $mix )) {
		$mix = get_resource_type ( $mix ) . strval ( $mix );
	} else {
		$mix = serialize ( $mix );
	}
	return md5 ( $mix );
}

/**
 * Cookie设置、获取、删除
 *
 * @param string $var Cookie名称
 * @param string $value Cookie值
 * @param int $time Cookie有效期
 * @return Ambigous <mixed, string, unknown>
 */
function cookie($var, $value = null, $time = 0) {
	if (is_null ( $value )) {
		return Cookie::get ( $var );
	} else if ($value == '') {
		return Cookie::delete ( $var );
	} else {
		return Cookie::set ( $var, $value, $time );
	}
}