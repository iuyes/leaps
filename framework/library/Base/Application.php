<?php
/**
 * 应用程序创建基类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-16
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: Application.php 553 2013-05-17 03:44:41Z 85825770@qq.com $
 */
abstract class Base_Application {

	/**
	 * 构造方法
	 */
	public function __construct() {
		$this->_init_env ();
		$this->_init_config ();
		$this->_init_input ();
		$this->_initialize ();
	}

	/**
	 * 初始化应用相关
	 */
	abstract public function _initialize();

	/**
	 * 创建并初始化应用配置
	 *
	 * @return Web_Application
	 */
	abstract protected function execute();

	/**
	 * 运行应用程序
	 */
	public function run() {
		$this->execute ();
	}

	/**
	 * 初始化环境变量
	 */
	private function _init_env() {
		error_reporting ( E_ERROR );
		if (PHP_VERSION < '5.3.0') {
			set_magic_quotes_runtime ( 0 );
		}
		define ( 'MAGIC_QUOTES_GPC', function_exists ( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc () );
		define ( 'START_TIME', microtime ( true ) );
		define ( 'MEMORY_LIMIT_ON', function_exists ( 'memory_get_usage' ) );
		if (MEMORY_LIMIT_ON) define ( 'START_MEMORY', memory_get_usage () );
		if (function_exists ( "set_time_limit" ) == true and @ini_get ( "safe_mode" ) == 0) {
			@set_time_limit ( 300 );
		}
		if (function_exists ( 'ini_get' )) {
			$memorylimit = @ini_get ( 'memory_limit' );
			if ($memorylimit && $this->_format_byte ( $memorylimit ) < 33554432 && function_exists ( 'ini_set' )) @ini_set ( 'memory_limit', '128m' );
		}
	}

	/**
	 * 初始化配置信息
	 *
	 * @param array $config
	 */
	private function _init_config() {
		$this->_set_timezone ( C ( 'config', 'timezone', 'Etc/GMT-8' ) );
		define ( 'TIME', time () );
		define ( 'IP', $this->_get_client_ip () );
		define ( 'CHARSET', C ( 'config', 'charset', 'UTF-8' ) );
		define ( 'PHP_FILE', $this->_get_script_url () );
		define ( 'SCRIPT_NAME', basename ( PHP_FILE ) );
		define ( 'WEB_PATH', substr ( PHP_FILE, 0, strrpos ( PHP_FILE, '/' ) ) . '/' );
	}

	/**
	 * 初始化输入变量
	 */
	private function _init_input() {
		if (MAGIC_QUOTES_GPC) {
			$_POST = new_stripslashes ( $_POST );
			$_GET = new_stripslashes ( $_GET );
			$_COOKIE = new_stripslashes ( $_COOKIE );
			$_REQUEST = new_stripslashes ( $_REQUEST );
		}
		if (isset ( $_GET ['page'] )) {
			$_GET ['page'] = max ( intval ( $_GET ['page'] ), 1 );
		}
		set_error_handler ( array ($this,'_errorHandle' ), error_reporting () );
		if (IS_DEBUG) {
			set_exception_handler ( array ($this,'_exceptionHandle' ) );
			register_shutdown_function ( array ($this,'_shutdownHandle' ) );
		}
	}

	/**
	 * 异常处理句柄
	 *
	 * @param Exception $exception 异常句柄
	 */
	public function _exceptionHandle($exception) {
		restore_error_handler ();
		restore_exception_handler ();
		$trace = $exception->getTrace ();
		if (@$trace [0] ['file'] == '') {
			unset ( $trace [0] );
			$trace = array_values ( $trace );
		}
		$file = @$trace [0] ['file'];
		$line = @$trace [0] ['line'];
		if (C ( 'config', 'firephp', false ) && class_exists ( 'FB' )) { // FirePHP调试
			fb ( $exception );
		}
		$this->showErrorMessage ( $exception->getMessage (), $file, $line, $trace, $exception->getCode () );
	}

	/**
	 * 错误处理句柄
	 *
	 * @param int $errno 错误句柄
	 * @param string $errstr 错误信息
	 * @param string $errfile 错误所在文件
	 * @param int $errline 错误所在行
	 */
	public function _errorHandle($errno, $errstr, $errfile, $errline) {
		if ($errno & IS_DEBUG) {
			restore_error_handler ();
			restore_exception_handler ();
			$trace = debug_backtrace ();
			unset ( $trace [0] ["function"], $trace [0] ["args"] );
			Core::debug ()->error ( $this->_get_errorname ( $errno ) . ': ' . $errstr . ' in ' . $errfile . ' on line ' . $errline );
			$this->showErrorMessage ( $this->_get_errorname ( $errno ) . ': ' . $errstr, $errfile, $errline, $trace, $errno );
		}
	}

	/**
	 * 致命错误捕捉
	 */
	public function _shutdownHandle() {
		if (($error = error_get_last ()) && $error ['type'] & IS_DEBUG) {
			$this->_errorHandle ( $error ['type'], $error ['message'], $error ['file'], $error ['line'] );
			// discuz_error::system_error($error['message'], false, true,
		// false);
		}
	}

	/**
	 * 错误处理
	 *
	 * @param string $message
	 * @param string $file 异常文件
	 * @param int $line 错误发生的行
	 * @param array $trace
	 * @param int $errorcode 错误代码
	 * @throws Exception
	 */
	protected function showErrorMessage($message, $file, $line, $trace, $errorcode) {
		if (IS_DEBUG) {
			$log = $message . "\r\n" . $file . ":" . $line . "\r\n";
			list ( $fileLines, $trace ) = Utility::crash ( $file, $line, $trace );
			foreach ( $trace as $key => $value ) {
				$log .= $value . "\r\n";
			}
			Core::debug ()->error ( $message . ' in ' . $file . ' on line ' . $line );
			log_message ( 'error', $log, TRUE );
		}
	}

	/**
	 * 返回友好的错误类型名
	 *
	 * @param int $type
	 * @return string unknown
	 */
	protected function _get_errorname($type) {
		switch ($type) {
			case E_ERROR :
				return 'E_ERROR';
			case E_WARNING :
				return 'E_WARNING';
			case E_PARSE :
				return 'E_PARSE';
			case E_NOTICE :
				return 'E_NOTICE';
			case E_CORE_ERROR :
				return 'E_CORE_ERROR';
			case E_CORE_WARNING :
				return 'E_CORE_WARNING';
			case E_CORE_ERROR :
				return 'E_COMPILE_ERROR';
			case E_CORE_WARNING :
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR :
				return 'E_USER_ERROR';
			case E_USER_WARNING :
				return 'E_USER_WARNING';
			case E_USER_NOTICE :
				return 'E_USER_NOTICE';
			case E_STRICT :
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR :
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED :
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED :
				return 'E_USER_DEPRECATED';
		}
		return $type;
	}

	/**
	 * 获取客户端IP
	 *
	 * @return string
	 */
	private function _get_client_ip() {
		$ip = '0.0.0.0';
		if (isset ( $_SERVER ['HTTP_CLIENT_IP'] ) && preg_match ( '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER ['HTTP_CLIENT_IP'] )) {
			$ip = $_SERVER ['HTTP_CLIENT_IP'];
		} elseif (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) and preg_match_all ( '#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER ['HTTP_X_FORWARDED_FOR'], $matches )) {
			foreach ( $matches [0] as $xip ) {
				if (! preg_match ( '#^(10|172\.16|192\.168)\.#', $xip )) {
					$ip = $xip;
					break;
				}
			}
		} elseif (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], 'unknown' )) {
			$ip = $_SERVER ['REMOTE_ADDR'];
		}
		return $ip;
	}

	/**
	 * 获取脚本路径
	 *
	 * @throws Exception
	 * @return Ambigous <string, unknown>
	 */
	private function _get_script_url() {
		$scriptName = basename ( $_SERVER ['SCRIPT_FILENAME'] );
		if (basename ( $_SERVER ['SCRIPT_NAME'] ) === $scriptName) {
			$PHP_SELF = $_SERVER ['SCRIPT_NAME'];
		} else if (basename ( $_SERVER ['PHP_SELF'] ) === $scriptName) {
			$PHP_SELF = $_SERVER ['PHP_SELF'];
		} else if (isset ( $_SERVER ['ORIG_SCRIPT_NAME'] ) && basename ( $_SERVER ['ORIG_SCRIPT_NAME'] ) === $scriptName) {
			$PHP_SELF = $_SERVER ['ORIG_SCRIPT_NAME'];
		} else if (($pos = strpos ( $_SERVER ['PHP_SELF'], '/' . $scriptName )) !== false) {
			$PHP_SELF = substr ( $_SERVER ['SCRIPT_NAME'], 0, $pos ) . '/' . $scriptName;
		} else if (isset ( $_SERVER ['DOCUMENT_ROOT'] ) && strpos ( $_SERVER ['SCRIPT_FILENAME'], $_SERVER ['DOCUMENT_ROOT'] ) === 0) {
			$PHP_SELF = str_replace ( '\\', '/', str_replace ( $_SERVER ['DOCUMENT_ROOT'], '', $_SERVER ['SCRIPT_FILENAME'] ) );
			$PHP_SELF [0] != '/' && $PHP_SELF = '/' . $PHP_SELF;
		} else {
			throw new Exception ( 'Request tainting, Please try again.' );
		}
		return htmlspecialchars ( $PHP_SELF );
	}

	/**
	 * 设置时区
	 *
	 * @param string $timezone
	 */
	private function _set_timezone($timezone = 'Etc/GMT-8') {
		if (function_exists ( 'date_default_timezone_set' )) {
			@date_default_timezone_set ( $timezone );
		}
	}

	/**
	 * 从格式话存储单位返回字节
	 *
	 * @param string $val 格式化存储单位
	 */
	private function _format_byte($val) {
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
}
