<?php
/**
 * 应用程序创建类
 * @author Tongle Xu <xutongle@gmail.com> 2013-6-8
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id$
 */
class Admin_Application extends Base_Application {
	public function _initialize() {
		@header ( 'Content-Type: text/html; charset=' . CHARSET );
		@header ( 'X-Powered-By: PHP/' . PHP_VERSION . ' Leaps/' . LEAPS_VERSION );
		/* 协议 */
		define ( 'SITE_PROTOCOL', Web_Request::is_ssl () ? 'https://' : 'http://' );
		/* 主机名 */
		define ( 'SITE_HOST', Web_Request::get_host () );
		/* 基础URL */
		define ( 'SITE_URL', htmlspecialchars ( Web_Request::get_base_url ( true ) ) . '/' );
		/* 设置来源 */
		define ( 'HTTP_REFERER', Web_Request::get_referer () );
		//Web_Filter::input ();
	}

	public function run() {
		if (C ( 'config', 'gzip', true ) && function_exists ( 'ob_gzhandler' )) {
			ob_start ( 'ob_gzhandler' );
		} else {
			ob_start ();
		}
		parent::run ();
		ob_end_flush ();
	}

	protected function execute() {
		$router = new Admin_Router ();
		define ( 'APP', $router->get_app () ); // 应用名称
		define ( 'CONTROLLER', $router->get_controller () ); // 控制器名称
		define ( 'ACTION', $router->get_action () ); // 事件名称
		$controller = $this->controller ();
		if (method_exists ( $controller, ACTION ) && ! preg_match ( '/^[_]/i', ACTION )) {
			call_user_func ( array ($controller,ACTION ) );
		} else {
			throw new Base_Exception ( ACTION, 102 );
		}
	}

	public static function controller($controller = null, $app = null) {
		$app = ! is_null ( $app ) ? trim ( $app ) : APP;
		$controller = ! is_null ( $controller ) ? trim ( $controller ) : CONTROLLER;
		$classname = $controller . 'Controller';
		import ( $app . ':admin.' . $classname );
		if (class_exists ( $classname, false )) {
			return new $classname ();
		} else {
			throw new Base_Exception ( 'Unable to create instance for ' . $classname . ' , class is not exist.',100 );
		}
	}

	public function _errorHandle($errno, $errstr, $errfile, $errline) {
		restore_error_handler ();
		restore_exception_handler ();
		$trace = debug_backtrace ();
		unset ( $trace [0] ["function"], $trace [0] ["args"] );
		Core::debug ()->error ( $this->_get_errorname ( $errno ) . ': ' . $errstr . ' in ' . $errfile . ' on line ' . $errline );
		$this->showErrorMessage ( $this->_get_errorname ( $errno ) . ': ' . $errstr, $errfile, $errline, $trace, $errno );
	}

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

	protected function showErrorMessage($message, $file, $line, $trace, $errorcode) {
		$log = $message . "\r\n" . $file . ":" . $line . "\r\n";
		list ( $fileLines, $trace ) = Utility::crash ( $file, $line, $trace );
		foreach ( $trace as $key => $value ) {
			$log .= $value . "\r\n";
		}
		log_message ( 'error', $log, TRUE );
		if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') return;
		if (IS_DEBUG) {
			ob_start ();
			include (FW_PATH . 'errors/error_php.php');
			$buffer = ob_get_contents ();
			ob_end_clean ();
			die ( $buffer );
		} else {//否则定向到错误页面
			$error_page = C ( 'config', 'error_page' );
			if (! empty ( $error_page )) {//如果错误页面不为空就重定向到配置文件中设置的地址
				redirect($error_page);
			} else {
				if (C('config','show_error_msg')){
					$e['message'] = $message;
				} else {
					$e['message'] = C('config','error_message');
				}
			}
			Utility::show_error($e['message']);
		}
	}
}