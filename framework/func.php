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
 * 语言文件处理
 *
 * @param string $language
 * @param array $pars
 * @param string $applications
 * @return string
 */
function L($language = 'NO_LANG', $pars = array(), $applications = '') {
	return Base_Lang::instance ()->load ( $language, $pars, $applications );
}

/**
 * 队列操作
 *
 * @param string $name
 * @param array $data
 * @param string $setting
 */
function Q($name, $data = '', $setting = 'default') {
	$queue = Loader::queue ( $setting );
	if (empty ( $data )) {
		return $queue->get ( $name );
	}
	return $queue->put ( $name, $data );
}

/**
 * URL组装 支持不同URL模式
 *
 * @param string $url URL表达式，格式：'[应用/模块/操作]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param boolean $redirect 是否跳转，如果设置为true则表示跳转到该URL地址
 * @param boolean $domain 是否显示域名
 * @return string
 */
function U($url = '', $vars = '', $redirect = false, $domain = false) {
	// 解析URL
	$info = parse_url ( $url );
	$url = ! empty ( $info ['path'] ) ? $info ['path'] : ACTION;
	if (isset ( $info ['fragment'] )) { // 解析锚点
		$anchor = $info ['fragment'];
		if (false !== strpos ( $anchor, '?' )) { // 解析参数
			list ( $anchor, $info ['query'] ) = explode ( '?', $anchor, 2 );
		}
	}
	// 解析参数
	if (is_string ( $vars )) { // aaa=1&bbb=2 转换成数组
		parse_str ( $vars, $vars );
	} elseif (! is_array ( $vars )) {
		$vars = array ();
	}
	if (isset ( $info ['query'] )) { // 解析地址里面参数 合并到vars
		parse_str ( $info ['query'], $params );
		$vars = array_merge ( $params, $vars );
	}
	// URL组装
	if ($url) {
		$url = trim ( $url, '/' );
		$path = explode ( '/', $url );
		$var = array ();
		if (isset ( $path [2] )) $var ['action'] = $path [2];
		if (isset ( $path [1] )) $var ['controller'] = $path [1];
		$var ['app'] = isset ( $path [0] ) ? $path [0] : APP;
	}
	if (C ( 'config', 'url_model' ) == 0) { // 普通模式URL转换
		$url = PHP_FILE . '?' . http_build_query ( array_reverse ( $var ) );
		if (! empty ( $vars )) {
			$vars = urldecode ( http_build_query ( $vars ) );
			$url .= '&' . $vars;
		}
	} else if (C ( 'config', 'url_model' ) != 0) {
		$url = WEB_PATH . implode ( '/', array_reverse ( $var ) );
		if (! empty ( $vars )) { // 添加参数
			$params = http_build_query ( $vars );
			$url = $url . '?' . $params;
		}
	}
	if ($domain) {
		$url = SITE_PROTOCOL . SITE_HOST . $url;
	}
	if ($redirect) // 直接跳转URL
		redirect ( $url );
	else
		return $url;
}

/**
 * 加载视图
 *
 * @param string $template
 * @param string $$application
 * @param string $$application
 */
function V($template = 'index', $application = null, $style = null) {
	if ($style == null) $style = C ( 'template', 'name' );
	$compiledtplfile = View::instance ()->compile ( $template, $application, $style );
	return $compiledtplfile;
}

/**
 * 无模型文件实例化模型并设置到当前表
 */
function M($table) {
	static $model = array ();
	if (! isset ( $model [$table] )) {
		Loader::model ( 'get_model', false );
		$model [$table] = new get_model ( null, $table );
	}
	return $model [$table];
}

/**
 * URL重定向
 *
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time = 0, $msg = '') {
	// 多行URL地址支持
	$url = str_replace ( array ("\n","\r" ), '', $url );
	if (empty ( $msg )) $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (! headers_sent ()) { // redirect
		if (0 === $time) {
			header ( 'Location: ' . $url );
		} else {
			header ( "refresh:{$time};url={$url}" );
			echo ($msg);
		}
		exit ();
	} else {
		$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0) $str .= $msg;
		exit ( $str );
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
	Log::get_instance ()->write ( $level, $message, $php_error );
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

/**
 * 字符串加密、解密函数
 *
 * @param string $txt
 * @param string $operation
 * @param string $key
 * @return string
 */
function authcode($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
	$key_length = 4;
	$key = md5 ( $key != '' ? $key : C ( 'config', 'auth_key' ) );
	$fixedkey = md5 ( $key );
	$egiskeys = md5 ( substr ( $fixedkey, 16, 16 ) );
	$runtokey = $key_length ? ($operation == 'ENCODE' ? substr ( md5 ( microtime ( true ) ), - $key_length ) : substr ( $string, 0, $key_length )) : '';
	$keys = md5 ( substr ( $runtokey, 0, 16 ) . substr ( $fixedkey, 0, 16 ) . substr ( $runtokey, 16 ) . substr ( $fixedkey, 16 ) );
	$string = $operation == 'ENCODE' ? sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $egiskeys ), 0, 16 ) . $string : base64_decode ( substr ( $string, $key_length ) );

	$i = 0;
	$result = '';
	$string_length = strlen ( $string );
	for($i = 0; $i < $string_length; $i ++) {
		$result .= chr ( ord ( $string {$i} ) ^ ord ( $keys {$i % 32} ) );
	}
	if ($operation == 'ENCODE') {
		return $runtokey . str_replace ( '=', '', base64_encode ( $result ) );
	} else {
		if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $egiskeys ), 0, 16 )) {
			return substr ( $result, 26 );
		} else {
			return '';
		}
	}
}

/**
 * 将字符串转换为数组
 *
 * @param string $data
 * @return array
 */
function string2array($data) {
	$array = array ();
	if ($data == '') return $array;
	@eval ( "\$array = $data;" );
	return $array;
}

/**
 * 将数组转换为字符串
 *
 * @param array $data
 * @param bool $isformdata
 * @return string
 *
 */
function array2string($data, $isformdata = 1) {
	if ($data == '') return '';
	if ($isformdata) $data = new_stripslashes ( $data );
	return var_export ( $data, TRUE );
}

/**
 * 中文字符转拼音
 *
 * @param string $str
 * @param string $utf8
 */
function string_to_pinyin($str, $utf8 = true) {
	return Pinyin::instance()->output ( $str, $utf8 );
}

function ip_source($ip) {
	return IpSource::instance()->get ( $ip );
}

/**
 * 发送电子邮件
 *
 * @param srting $toemail 要发送到的邮箱多个逗号隔开
 * @param srting $subject 邮件标题
 * @param srting $message 邮件内容
 * @param srting $from
 */
function sendmail($toemail, $subject, $message, $from = '') {
	return Loader::lib ( 'Mail' )->send ( $toemail, $subject, $message, $from );
}

/**
 * 使用phpqrcode生成二维码
 *
 * @param string $value 二维码数据
 * @param string $level 纠错级别：L、M、Q、H
 * @param int $size 点的大小：1到10,用于手机端4就可以了
 */
function qrcode($value, $level = 'L', $size = 4) {
	Loader::lib ( 'QRcode.QRcode', false );
	return QRcode::png ( $value, false, $level, $size );
}