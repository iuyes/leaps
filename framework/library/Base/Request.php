<?php
/**
 * Request.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Base_Request {

	/**
	 * 访问的端口号
	 *
	 * @var int
	 */
	protected static $_port = null;

	/**
	 * 请求路径信息
	 *
	 * @var string
	 */
	protected static $_host_info = null;

	/**
	 * 客户端IP
	 *
	 * @var string
	 */
	protected static $_client_ip = null;

	/**
	 * 语言
	 *
	 * @var string
	 */
	protected static $_language = null;

	/**
	 * 路径信息
	 *
	 * @var string
	 */
	protected static $_path_info = null;

	/**
	 * 请求参数信息
	 *
	 * @var array
	 */
	protected static $_attribute = array ();

	/**
	 * 请求脚本url
	 *
	 * @var string
	 */
	private static $_script_url = null;

	/**
	 * 请求参数uri
	 *
	 * @var string
	 */
	private static $_request_uri = null;

	/**
	 * 基础路径信息
	 *
	 * @var string
	 */
	private static $_base_url = null;

	/**
	 * 设置属性数据
	 *
	 * @param string|array|object $data 需要设置的数据
	 * @param string $key 设置的数据保存用的key,默认为空,当数组和object类型的时候将会执行array_merge操作
	 * @return void
	 */
	public static function set_attribute($data, $key = '') {
		if ($key) {
			self::$_attribute [$key] = $data;
			return;
		}
		if (is_object ( $data )) $data = get_object_vars ( $data );
		if (is_array ( $data )) self::$_attribute = array_merge ( self::$_attribute, $data );
	}

	/**
	 * 根据名称获得服务器和执行环境信息
	 *
	 * 主要获取的依次顺序为：_attribute、$_GET、$_POST、$_COOKIE、$_REQUEST、$_ENV、$_SERVER
	 *
	 * @param string $name 获取数据的key值
	 * @param string $defaultValue 设置缺省值,当获取值失败的时候返回缺省值,默认该值为空字串
	 * @return string object array
	 */
	public static function get_attribute($key, $defaultValue = '') {
		if (isset ( self::$_attribute [$key] ))
			return self::$_attribute [$key];
		else if (isset ( $_GET [$key] ))
			return $_GET [$key];
		else if (isset ( $_POST [$key] ))
			return $_POST [$key];
		else if (isset ( $_COOKIE [$key] ))
			return $_COOKIE [$key];
		else if (isset ( $_REQUEST [$key] ))
			return $_REQUEST [$key];
		else if (isset ( $_ENV [$key] ))
			return $_ENV [$key];
		else if (isset ( $_SERVER [$key] ))
			return $_SERVER [$key];
		else
			return $defaultValue;
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
	public static function get_request($key = null, $default_value = null) {
		if (! $key) return array_merge ( $_POST, $_GET );
		if (isset ( $_GET [$key] )) return $_GET [$key];
		if (isset ( $_POST [$key] )) return $_POST [$key];
		return $default_value;
	}

	/**
	 * 获取请求的表单数据
	 *
	 * 从$_POST获得值
	 *
	 * @param string $name 获取的变量名,默认为null,当为null的时候返回$_POST数组
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认为null
	 * @return mixed
	 */
	public static function get_post($name = null, $default_value = null) {
		if ($name === null) return $_POST;
		return isset ( $_POST [$name] ) ? $_POST [$name] : $default_value;
	}

	/**
	 * 获得$_GET值
	 *
	 * @param string $name 待获取的变量名,默认为空字串,当该值为null的时候将返回$_GET数组
	 * @param string $defaultValue 当获取的变量不存在的时候返回该缺省值,默认值为null
	 * @return mixed
	 */
	public static function get_get($name = '', $default_value = null) {
		if ($name === null) return $_GET;
		return (isset ( $_GET [$name] )) ? $_GET [$name] : $default_value;
	}

	/**
	 * 返回cookie的值
	 *
	 * 如果$name=null则返回所有Cookie值
	 *
	 * @param string $name 获取的变量名,如果该值为null则返回$_COOKIE数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public static function get_cookie($name = null, $defaultValue = null) {
		if ($name === null) return $_COOKIE;
		return (isset ( $_COOKIE [$name] )) ? $_COOKIE [$name] : $defaultValue;
	}

	/**
	 * 返回session的值
	 *
	 * 如果$name=null则返回所有SESSION值
	 *
	 * @param string $name 获取的变量名,如果该值为null则返回$_SESSION数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public static function get_session($name = null, $defaultValue = null) {
		if ($name === null) return $_SESSION;
		return (isset ( $_SESSION [$name] )) ? $_SESSION [$name] : $defaultValue;
	}

	/**
	 * 返回Server的值
	 *
	 * 如果$name为空则返回所有Server的值
	 *
	 * @param string $name 获取的变量名,如果该值为null则返回$_SERVER数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public static function get_server($name = null, $default_value = null) {
		if ($name === null) return $_SERVER;
		return (isset ( $_SERVER [$name] )) ? $_SERVER [$name] : $default_value;
	}

	/**
	 * 返回ENV的值
	 *
	 * 如果$name为null则返回所有$_ENV的值
	 *
	 * @param string $name 获取的变量名,如果该值为null则返回$_ENV数组,默认为null
	 * @param string $defaultValue 当获取变量失败的时候返回该值,默认该值为null
	 * @return mixed
	 */
	public static function get_env($name = null, $defaultValue = null) {
		if ($name === null) return $_ENV;
		return (isset ( $_ENV [$name] )) ? $_ENV [$name] : $defaultValue;
	}

	/**
	 * 获取请求链接协议
	 *
	 * 如果是安全链接请求则返回https否则返回http
	 *
	 * @return string
	 */
	public static function get_scheme() {
		return (self::get_server ( 'HTTPS' ) == 'on') ? 'https' : 'http';
	}

	/**
	 * 返回请求页面时通信协议的名称和版本
	 *
	 * @return string
	 */
	public static function get_protocol() {
		return self::get_server ( 'SERVER_PROTOCOL', 'HTTP/1.0' );
	}

	/**
	 * 返回访问IP
	 *
	 * 如果获取请求IP失败,则返回0.0.0.0
	 *
	 * @return string
	 */
	public static function get_client_ip() {
		if (! self::$_client_ip) self::_get_client_ip ();
		return self::$_client_ip;
	}

	/**
	 * 获得请求的方法
	 *
	 * 将返回POST\GET\DELETE等HTTP请求方式
	 *
	 * @return string
	 */
	public static function get_request_method() {
		return strtoupper ( self::get_server ( 'REQUEST_METHOD' ) );
	}

	/**
	 * 获得请求类型
	 *
	 * 如果是web请求将返回web
	 *
	 * @return string
	 */
	public static function get_request_type() {
		return 'web';
	}

	/**
	 * 返回该请求是否为ajax请求
	 *
	 * 如果是ajax请求将返回true,否则返回false
	 *
	 * @return boolean
	 */
	public static function is_ajax() {
		return ! strcasecmp ( self::get_server ( 'HTTP_X_REQUESTED_WITH' ), 'XMLHttpRequest' );
	}

	/**
	 * 请求是否使用的是HTTPS安全链接
	 *
	 * 如果是安全请求则返回true否则返回false
	 *
	 * @return boolean
	 */
	public static function is_secure() {
		return ! strcasecmp ( self::get_server ( 'HTTPS' ), 'on' );
	}

	/**
	 * 返回请求是否为GET请求类型
	 *
	 * 如果请求是GET方式请求则返回true，否则返回false
	 *
	 * @return boolean
	 */
	public static function is_get() {
		return ! strcasecmp ( self::get_request_method (), 'GET' );
	}

	/**
	 * 返回请求是否为POST请求类型
	 *
	 * 如果请求是POST方式请求则返回true,否则返回false
	 *
	 * @return boolean
	 */
	public static function is_post() {
		return ! strcasecmp ( self::get_request_method (), 'POST' );
	}

	/**
	 * 返回请求是否为PUT请求类型
	 *
	 * 如果请求是PUT方式请求则返回true,否则返回false
	 *
	 * @return boolean
	 */
	public static function is_put() {
		return ! strcasecmp ( self::get_request_method (), 'PUT' );
	}

	/**
	 * 返回请求是否为DELETE请求类型
	 *
	 * 如果请求是DELETE方式请求则返回true,否则返回false
	 *
	 * @return boolean
	 */
	public static function is_delete() {
		return ! strcasecmp ( self::get_request_method (), 'Delete' );
	}

	/**
	 * 初始化请求的资源标识符
	 *
	 * 这里的uri是去除协议名、主机名的
	 * <pre>Example:
	 * 请求： http://www.tintsoft.com/example/index.php?a=test
	 * 则返回: /example/index.php?a=test
	 * </pre>
	 *
	 * @return string
	 * @throws Base_Exception 当获取失败的时候抛出异常
	 */
	public static function get_request_uri() {
		if (! self::$_request_uri) self::_init_request_uri ();
		return self::$_request_uri;
	}

	/**
	 * 返回当前执行脚本的绝对路径
	 *
	 * <pre>Example:
	 * 请求: http://www.tintsoft.com/example/index.php?a=test
	 * 返回: /example/index.php
	 * </pre>
	 *
	 * @return string
	 * @throws Base_Exception 当获取失败的时候抛出异常
	 */
	public static function get_script_url() {
		if (! self::$_script_url) self::_init_script_url ();
		return self::$_script_url;
	}

	/**
	 * 返回执行脚本名称
	 *
	 * <pre>Example:
	 * 请求: http://www.tintsoft.com/example/index.php?a=test
	 * 返回: index.php
	 * </pre>
	 *
	 * @return string
	 * @throws Base_Exception 当获取失败的时候抛出异常
	 */
	public static function get_script() {
		if (($pos = strrpos ( self::get_script_url (), '/' )) === false) $pos = - 1;
		return substr ( self::get_script_url (), $pos + 1 );
	}

	/**
	 * 获取Http头信息
	 *
	 * @param string $header 头部名称
	 * @param string $default 获取失败将返回该值,默认为null
	 * @return string
	 */
	public static function get_header($header, $default = null) {
		$temp = strtoupper ( str_replace ( '-', '_', $header ) );
		if (substr ( $temp, 0, 5 ) != 'HTTP_') $temp = 'HTTP_' . $temp;
		if (($header = self::get_server ( $temp )) != null) return $header;
		if (function_exists ( 'apache_request_headers' )) {
			$headers = apache_request_headers ();
			if ($headers [$header]) return $headers [$header];
		}
		return $default;
	}

	/**
	 * 返回包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
	 *
	 * <pre>Example:
	 * 请求: http://www.tintsoft.com/example/index.php?a=test
	 * 返回: a=test
	 * </pre>
	 *
	 * @return string
	 * @throws Base_Exception
	 */
	public static function get_path_info() {
		if (! self::$_path_info) self::_init_path_info ();
		return self::$_path_info;
	}

	/**
	 * 获取基础URL
	 *
	 * 这里是去除了脚本文件以及访问参数信息的URL地址信息:
	 *
	 * <pre>Example:
	 * 请求: http://www.tintsoft.com/example/index.php?a=test
	 * 1]如果: $absolute = false：
	 * 返回： example
	 * 2]如果: $absolute = true:
	 * 返回： http://www.tintsoft.com/example
	 * </pre>
	 *
	 * @param boolean $absolute 是否返回主机信息
	 * @return string
	 * @throws Base_Exception 当返回信息失败的时候抛出异常
	 */
	public static function get_base_url($absolute = false) {
		if (self::$_base_url === null) self::$_base_url = rtrim ( dirname ( self::get_script_url () ), '\\/.' );
		return $absolute ? self::get_host_info () . self::$_base_url : self::$_base_url;
	}

	/**
	 * 获得主机信息，包含协议信息，主机名，访问端口信息
	 *
	 * <pre>Example:
	 * 请求: http://www.tintsoft.com/example/index.php?a=test
	 * 返回： http://www.tintsoft.com/
	 * </pre>
	 *
	 * @return string
	 * @throws Base_Exception 获取主机信息失败的时候抛出异常
	 */
	public static function get_host_info() {
		if (self::$_host_info === null) self::_init_host_info ();
		return self::$_host_info;
	}

	/**
	 * 返回当前运行脚本所在的服务器的主机名。
	 *
	 * 如果脚本运行于虚拟主机中
	 * 该名称是由那个虚拟主机所设置的值决定
	 *
	 * @return string
	 */
	public static function get_server_name() {
		return self::get_server ( 'SERVER_NAME', '' );
	}

	/**
	 * 返回服务端口号
	 *
	 * https链接的默认端口号为443
	 * http链接的默认端口号为80
	 *
	 * @return int
	 */
	public static function get_server_port() {
		if (! self::$_port) {
			$_default = self::is_secure () ? 443 : 80;
			self::set_server_port ( self::get_server ( 'SERVER_PORT', $_default ) );
		}
		return self::$_port;
	}

	/**
	 * 设置服务端口号
	 *
	 * https链接的默认端口号为443
	 * http链接的默认端口号为80
	 *
	 * @param int $port 设置的端口号
	 */
	public static function set_server_port($port) {
		self::$_port = ( int ) $port;
	}

	/**
	 * 返回浏览当前页面的用户的主机名
	 *
	 * DNS 反向解析不依赖于用户的 REMOTE_ADDR
	 *
	 * @return string
	 */
	public static function get_remote_host() {
		return self::get_server ( 'REMOTE_HOST' );
	}

	/**
	 * 返回浏览器发送Referer请求头
	 *
	 * 可以让服务器了解和追踪发出本次请求的起源URL地址
	 *
	 * @return string
	 */
	public static function get_url_referer() {
		return self::get_server ( 'HTTP_REFERER' );
	}

	/**
	 * 获得用户机器上连接到 Web 服务器所使用的端口号
	 *
	 * @return number
	 */
	public static function get_remote_port() {
		return self::get_server ( 'REMOTE_PORT' );
	}

	/**
	 * 返回User-Agent头字段用于指定浏览器或者其他客户端程序的类型和名字
	 *
	 * 如果客户机是一种无线手持终端，就返回一个WML文件；如果发现客户端是一种普通浏览器，
	 * 则返回通常的HTML文件
	 *
	 * @return string
	 */
	public static function get_user_agent() {
		return self::get_server ( 'HTTP_USER_AGENT', '' );
	}

	/**
	 * 返回当前请求头中 Accept: 项的内容，
	 *
	 * Accept头字段用于指出客户端程序能够处理的MIME类型，例如 text/html,image/*
	 *
	 * @return string
	 */
	public static function get_accept_types() {
		return self::get_server ( 'HTTP_ACCEPT', '' );
	}

	/**
	 * 返回客户端程序可以能够进行解码的数据编码方式
	 *
	 * 这里的编码方式通常指某种压缩方式
	 *
	 * @return string ''
	 */
	public static function get_accept_charset() {
		return self::get_server ( 'HTTP_ACCEPT_ENCODING', '' );
	}

	/**
	 * 返回客户端程序期望服务器返回哪个国家的语言文档
	 *
	 * Accept-Language: en-us,zh-cn
	 *
	 * @return string
	 */
	public static function get_accept_language() {
		if (! self::$_language) {
			$_language = explode ( ',', self::get_server ( 'HTTP_ACCEPT_LANGUAGE', '' ) );
			self::$_language = $_language [0] ? $_language [0] : 'zh-cn';
		}
		return self::$_language;
	}

	/**
	 * 返回访问IP
	 *
	 * 如果获取请求IP失败,则返回0.0.0.0
	 *
	 * @return string
	 */
	public static function _get_client_ip() {
		if (($ip = self::get_server ( 'HTTP_CLIENT_IP' )) != null) {
			self::$_client_ip = $ip;
		} elseif (($_ip = self::get_server ( 'HTTP_X_FORWARDED_FOR' )) != null) {
			$ip = strtok ( $_ip, ',' );
			do {
				$ip = ip2long ( $ip );
				if (! (($ip == 0) || ($ip == 0xFFFFFFFF) || ($ip == 0x7F000001) || (($ip >= 0x0A000000) && ($ip <= 0x0AFFFFFF)) || (($ip >= 0xC0A8FFFF) && ($ip <= 0xC0A80000)) || (($ip >= 0xAC1FFFFF) && ($ip <= 0xAC100000)))) {
					self::$_client_ip = long2ip ( $ip );
					return;
				}
			} while ( ($ip = strtok ( ',' )) );
		} elseif (($ip = self::get_server ( 'HTTP_PROXY_USER' )) != null) {
			$_client_ip = $ip;
		} elseif (($ip = self::get_server ( 'REMOTE_ADDR' )) != null) {
			self::$_client_ip = $ip;
		} else {
			self::$_client_ip = "0.0.0.0";
		}
	}

	/**
	 * 初始化请求的资源标识符
	 *
	 * <pre>这里的uri是去除协议名、主机名的
	 * Example:
	 * 请求： http://www.tintsoft.com/example/index.php?a=test
	 * 则返回: /example/index.php?a=test
	 * </pre>
	 *
	 * @throws Base_Exception 处理错误抛出异常
	 */
	private static function _init_request_uri() {
		if (($request_uri = self::get_server ( 'HTTP_X_REWRITE_URL' )) != null) {
			self::$_request_uri = $request_uri;
		} elseif (($request_uri = self::get_server ( 'REQUEST_URI' )) != null) {
			self::$_request_uri = $request_uri;
			if (strpos ( self::$_request_uri, self::get_server ( 'HTTP_HOST' ) ) !== false) self::$_request_uri = preg_replace ( '/^\w+:\/\/[^\/]+/', '', self::$_request_uri );
		} elseif (($request_uri = self::get_server ( 'ORIG_PATH_INFO' )) != null) {
			self::$_request_uri = $request_uri;
			if (($query = self::get_server ( 'QUERY_STRING' )) != null) self::$_request_uri .= '?' . $query;
		} else
			throw new Base_Exception ( '[web.Web_Request._init_request_uri] unable to determine the request URI.' );
	}

	/**
	 * 返回当前执行脚本的绝对路径
	 *
	 * <pre>Example:
	 * 请求: http://www.tintsoft.com/example/index.php?a=test
	 * 返回: /example/index.php
	 * </pre>
	 *
	 * @throws Base_Exception 当获取失败的时候抛出异常
	 */
	private static function _init_script_url() {
		if (($script_name = self::get_server ( 'SCRIPT_FILENAME' )) == null) {
			throw new Base_Exception ( '[web.Web_Request._initScriptUrl] determine the entry script URL failed!!!' );
		}
		$script_name = basename ( $script_name );
		if (($_script_name = self::get_server ( 'SCRIPT_NAME' )) != null && basename ( $_script_name ) === $script_name) {
			self::$_script_url = $_script_name;
		} elseif (($_scriptName = self::get_server ( 'PHP_SELF' )) != null && basename ( $_script_name ) === $script_name) {
			self::$_script_url = $_scriptName;
		} elseif (($_script_name = self::get_server ( 'ORIG_SCRIPT_NAME' )) != null && basename ( $_script_name ) === $script_name) {
			self::$_script_url = $_scriptName;
		} elseif (($pos = strpos ( self::get_server ( 'PHP_SELF' ), '/' . $script_name )) !== false) {
			self::$_script_url = substr ( self::get_server ( 'SCRIPT_NAME' ), 0, $pos ) . '/' . $script_name;
		} elseif (($_document_root = self::get_server ( 'DOCUMENT_ROOT' )) != null && ($_script_name = self::get_server ( 'SCRIPT_FILENAME' )) != null && strpos ( $_script_name, $_document_root ) === 0) {
			self::$_script_url = str_replace ( '\\', '/', str_replace ( $_document_root, '', $_script_name ) );
		} else
			throw new Base_Exception ( '[web.HttpRequest._initScriptUrl] determine the entry script URL failed!!' );
	}

	/**
	 * 获得主机信息，包含协议信息，主机名，访问端口信息
	 *
	 * <pre>Example:
	 * 请求: http://www.tintsoft.com/example/index.php?a=test
	 * 返回： http://www.tintsoft.com/
	 * </pre>
	 *
	 * @throws Base_Exception 获取主机信息失败的时候抛出异常
	 */
	private static function _init_host_info() {
		if (($http_host = self::get_server ( 'HTTP_HOST' )) != null)
			self::$_host_info = self::get_scheme () . '://' . $http_host;
		elseif (($http_host = self::get_server ( 'SERVER_NAME' )) != null) {
			self::$_host_info = self::get_scheme () . '://' . $http_host;
			if (($port = self::get_server_port ()) != null) self::$_host_info .= ':' . $port;
		} else
			throw new Base_Exception ( '[web.Web_Request._initHostInfo] determine the entry script URL failed!!' );
	}

	/**
	 * 返回包含由客户端提供的、跟在真实脚本名称之后并且在查询语句（query string）之前的路径信息
	 *
	 * <pre>Example:
	 * 请求: http://www.tintsoft.com/example/index.php?a=test
	 * 返回: a=test
	 * </pre>
	 *
	 * @throws Base_Exception
	 */
	private static function _init_path_info() {
		$request_uri = self::get_request_uri ();
		$script_url = self::get_script_url ();
		$base_url = self::get_base_url ();
		if (strpos ( $request_uri, $script_url ) === 0) {
			$path_info = substr ( $request_uri, strlen ( $script_url ) );
		} elseif ($base_url === '' || strpos ( $request_uri, $base_url ) === 0) {
			$path_info = substr ( $request_uri, strlen ( $base_url ) );
		} elseif (strpos ( $_SERVER ['PHP_SELF'], $script_url ) === 0) {
			$path_info = substr ( $_SERVER ['PHP_SELF'], strlen ( $script_url ) );
		} else
			throw new Base_Exception ( '[web.Web_Request._init_path_info] determine the entry path info failed!!' );
			// if (($pos = strpos ( $path_info, '?' )) !== false) $path_info =
			// substr ( $path_info, $pos + 1 );
		self::$_path_info = trim ( $path_info, '/' );
	}

	/**
	 * 解析cli参数
	 *
	 * @return string
	 */
	public static function parse_cli_args() {
		$args = array_slice ( $_SERVER ['argv'], 1 );
		return $args ? '/' . implode ( '/', $args ) : '';
	}
}