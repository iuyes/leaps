<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
abstract class Base_Application {

	public function __construct() {
		$this->init ();
	}

	public function init() {

		if (! defined ( 'CORE_FUNCTION' ) && ! @include (FW_PATH . 'library/Core/Function.php')) exit ( 'Function.php is missing' );
		if (function_exists ( "set_time_limit" ) == true and @ini_get ( "safe_mode" ) == 0) {
			@set_time_limit ( 300 );
		}
		if (function_exists ( 'ini_get' )) {
			$memorylimit = @ini_get ( 'memory_limit' );
			if ($memorylimit && format_byte ( $memorylimit ) < 33554432 && function_exists ( 'ini_set' )) @ini_set ( 'memory_limit', '128m' );
		}
		if (function_exists ( 'date_default_timezone_set' )) {
			@date_default_timezone_set ( C ( 'config', 'timezone', 'Etc/GMT-8' ) ); // 默认Etc/GMT-8
		}
		define ( 'TIME', time () );
		define ( 'CHARSET', C ( 'config', 'charset', 'UTF-8' ) );
		if (C ( 'config', 'debug', false )) {
			define ( 'IS_DEBUG', true );
			error_reporting ( E_ALL );
		} else {
			define ( 'IS_DEBUG', false );
			error_reporting ( 0 );
		}
		// 访客IP
		define ( 'IP', Core_Request::get_client_ip () );
		// 前执行脚本的绝对路径
		define ( 'PHP_FILE', htmlspecialchars ( Core_Request::get_script_url () ) );
		/* 脚本名称 */
		define ( 'SCRIPT_NAME', Core_Request::get_script () );
		// 所在目录
		define ( 'WEB_PATH', substr ( PHP_FILE, 0, strrpos ( PHP_FILE, '/' ) ) . '/' );
		if (! IS_CLI) {
			/* 协议 */
			define ( 'SITE_PROTOCOL', Core_Request::is_ssl () ? 'https://' : 'http://' );
			/* 主机名 */
			define ( 'SITE_HOST', Core_Request::get_host () );
			/* 基础URL */
			define ( 'SITE_URL', htmlspecialchars ( Core_Request::get_base_url ( true ) ) . '/' );
			/* 设置来源 */
			define ( 'HTTP_REFERER', Core_Request::get_referer () );
		}
		set_error_handler ( 'Core::_error_handle' );
		register_shutdown_function ( 'Core::_shutdown_handle' );
		set_exception_handler ( 'Core::_exception_handle' );
		/* 临时文件存储目录，临时文件的生存周期等同于PHP请求，也就是当该PHP请求完成执行时，所有写入TmpFS的临时文件都会被销毁 */
		define ( 'TMP_PATH', '' );
		/* 开始时间 */
		define ( 'START_TIME', microtime ( true ) );
		/* 开始占用内存 */
		define ( 'MEMORY_LIMIT_ON', function_exists ( 'memory_get_usage' ) );
		if (MEMORY_LIMIT_ON) define ( 'START_MEMORY', memory_get_usage () );
		@header ( 'Content-Type: text/html; charset=' . CHARSET );
		@header ( 'X-Powered-By: PHP/' . PHP_VERSION . ' Leaps/' . LEAPS_VERSION );
		// 页面压缩输出支持
		if (C ( 'config', 'gzip', true ) && function_exists ( 'ob_gzhandler' )) {
			ob_start ( 'ob_gzhandler' );
		} else {
			ob_start ();
		}
		Core_Filter::input ();

	}

	abstract function run();
}