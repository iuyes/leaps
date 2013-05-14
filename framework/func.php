<?php
/**
 * 系统核心函数库
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
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
 * 从格式话存储单位返回字节
 *
 * @param string $val 格式化存储单位
 */
function format_byte($val) {
	$val = trim ( $val );
	$last = strtolower ( $val {strlen ( $val ) - 1} );
	switch ($last) {
		case 'g' :
			$val *= 1024;
		case 'm' :
			$val *= 1024;
		case 'k' :
			$val *= 1024;
	}
	return $val;
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
	return Base_Config::get ( $file, $key, $default );
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
 * @param string $name
 *        	字符串
 * @param integer $type
 *        	转换类型
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
 * @param mixed $mix
 *        	变量
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
 * 程序执行时间
 *
 * @return int
 */
function execute_time() {
	$etime = microtime ( true );
	return number_format ( ($etime - START_TIME), 6 );
}

/**
 * 显示运行时间、数据库操作、缓存次数、内存使用信息
 *
 * @return string
 */
function show_time() {
	if (! C ( 'config', 'show_time' )) return;
	$show_time = '';
	// 显示运行时间
	$show_time = 'Process: ' . execute_time () . ' seconds ';
	if (class_exists ( 'Core_DB', false )) $show_time .= ' | DB :' . N ( 'db_query' ) . ' queries ';
	$show_time .= ' | Cache :' . N ( 'cache_read' ) . ' gets ' . N ( 'cache_write' ) . ' writes ';
	// 显示内存开销
	$startMem = array_sum ( explode ( ' ', START_MEMORY ) );
	$endMem = array_sum ( explode ( ' ', memory_get_usage () ) );
	$show_time .= ' | UseMem:' . number_format ( ($endMem - $startMem) / 1024 ) . ' kb';
	if (IS_CLI) return "\r\n" . $show_time . "\r\n";
	return $show_time;
}
function show_trace() {
	if (! Web_Request::is_ajax () && C ( 'config', 'show_trace' )) {
		$trace_page_tabs = array ('BASE' => '基本','FILE' => '文件','INFO' => '流程','ERR|NOTIC' => '错误','SQL' => 'SQL','DEBUG' => '调试' ); // 页面Trace可定制的选项卡

		// 系统默认显示信息
		$files = get_included_files ();
		$info = array ();
		foreach ( $files as $key => $file ) {
			$info [] = $file . ' ( ' . number_format ( filesize ( $file ) / 1024, 2 ) . ' KB )';
		}
		$trace = array ();
		$base = array ('请求信息' => date ( 'Y-m-d H:i:s', $_SERVER ['REQUEST_TIME'] ) . ' ' . $_SERVER ['SERVER_PROTOCOL'] . ' ' . $_SERVER ['REQUEST_METHOD'] . ' : ' . $_SERVER ['REQUEST_URI'],'运行时间' => show_time (),
				'内存开销' => MEMORY_LIMIT_ON ? number_format ( (memory_get_usage () - START_MEMORY) / 1024, 2 ) . ' kb' : '不支持','查询信息' => N ( 'db_query' ) . ' queries ' . N ( 'db_write' ) . ' writes ','文件加载' => count ( get_included_files () ),
				'缓存信息' => N ( 'cache_read' ) . ' gets ' . N ( 'cache_write' ) . ' writes ','会话信息' => 'SESSION_ID=' . session_id () );
		$debug = trace ();
		foreach ( $trace_page_tabs as $name => $title ) {
			switch (strtoupper ( $name )) {
				case 'BASE' : // 基本信息
					$trace [$title] = $base;
					break;
				case 'FILE' : // 文件信息
					$trace [$title] = $info;
					break;
				default : // 调试信息
					$name = strtoupper ( $name );
					if (strpos ( $name, '|' )) { // 多组信息
						$array = explode ( '|', $name );
						$result = array ();
						foreach ( $array as $name ) {
							$result += isset ( $debug [$name] ) ? $debug [$name] : array ();
						}
						$trace [$title] = $result;
					} else {
						$trace [$title] = isset ( $debug [$name] ) ? $debug [$name] : '';
					}
			}
		}
	}
	include FW_PATH . 'errors' . DIRECTORY_SEPARATOR . 'trace.php';
}

/**
 * 添加和获取页面Trace记录
 *
 * @param string $value 变量
 * @param string $label 标签
 * @param string $level 日志级别
 * @param boolean $record 是否记录日志
 * @return void
 */
function trace($value = '[leaps]', $label = '', $level = 'DEBUG', $record = false) {
	static $_trace = array ();
	if ('[leaps]' === $value) { // 获取trace信息
		return $_trace;
	} else {
		$info = ($label ? $label . ':' : '') . print_r ( $value, true );
		if ('ERR' == $level && C ( 'config', 'trace_exception' )) { // 抛出异常
			throw new Exception ( $info );
		}
		$level = strtoupper ( $level );
		if (! isset ( $_trace [$level] )) {
			$_trace [$level] = array ();
		}
		$_trace [$level] [] = $info;
	}
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
	Core::log ()->write ( $level, $message, $php_error );
}
function set_status_header($code = 200, $text = '') {
	$stati = array (200 => 'OK',201 => 'Created',202 => 'Accepted',203 => 'Non-Authoritative Information',204 => 'No Content',205 => 'Reset Content',206 => 'Partial Content',300 => 'Multiple Choices',301 => 'Moved Permanently',302 => 'Found',304 => 'Not Modified',305 => 'Use Proxy',
			307 => 'Temporary Redirect',400 => 'Bad Request',401 => 'Unauthorized',403 => 'Forbidden',404 => 'Not Found',405 => 'Method Not Allowed',406 => 'Not Acceptable',407 => 'Proxy Authentication Required',408 => 'Request Timeout',409 => 'Conflict',410 => 'Gone',411 => 'Length Required',
			412 => 'Precondition Failed',413 => 'Request Entity Too Large',414 => 'Request-URI Too Long',415 => 'Unsupported Media Type',416 => 'Requested Range Not Satisfiable',417 => 'Expectation Failed',500 => 'Internal Server Error',501 => 'Not Implemented',502 => 'Bad Gateway',
			503 => 'Service Unavailable',504 => 'Gateway Timeout',505 => 'HTTP Version Not Supported' );
	if ($code == '' or ! is_numeric ( $code )) {
		Utility::show_error ( 'Status codes must be numeric', 500 );
	}
	if (isset ( $stati [$code] ) and $text == '') {
		$text = $stati [$code];
	}
	if ($text == '') {
		Utility::show_error ( 'No status text available.  Please check your status code number or supply your own message text.', 500 );
	}
	$server_protocol = (isset ( $_SERVER ['SERVER_PROTOCOL'] )) ? $_SERVER ['SERVER_PROTOCOL'] : FALSE;
	if (IS_CGI) {
		header ( "Status: {$code} {$text}", TRUE );
	} elseif ($server_protocol == 'HTTP/1.1' or $server_protocol == 'HTTP/1.0') {
		header ( $server_protocol . " {$code} {$text}", TRUE, $code );
	} else {
		header ( "HTTP/1.1 {$code} {$text}", TRUE, $code );
	}
}
/**
 * 加载视图
 *
 * @param string $template
 * @param string $application
 * @param string $application
 */
function V($template = 'index', $application = null, $style = null) {
	if ($style == null) $style = C ( 'template', 'name' );
	$compiledtplfile = View::instance ()->compile ( $template, $application, $style );
	return $compiledtplfile;
}