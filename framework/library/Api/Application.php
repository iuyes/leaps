<?php
/**
 * 应用程序创建类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-16
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: Application.php 562 2013-05-17 07:23:37Z 85825770@qq.com $
 */
class Api_Application extends Base_Application {

	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 *
	 * @see Base_Application::_initialize()
	 */
	public function _initialize() {
		@header ( 'Content-Type: text/html; charset=' . CHARSET );
		@header ( 'X-Powered-By: PHP/' . PHP_VERSION . ' Leaps/' . LEAPS_VERSION );
		/* 协议 */
		define ( 'SITE_PROTOCOL', Web_Request::is_ssl () ? 'https://' : 'http://' );
		/* 主机名 */
		define ( 'SITE_HOST', Web_Request::get_host () );
		/* 基础URL */
		define ( 'SITE_URL', htmlspecialchars (Web_Request::get_base_url ( true ) ) . '/' );
		/* 设置来源 */
		define ( 'HTTP_REFERER', Web_Request::get_referer () );
		Web_Filter::input ();
	}

	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 *
	 * @see Base_Application::run()
	 */
	public function run() {
		if (C ( 'config', 'gzip', true ) && function_exists ( 'ob_gzhandler' )) {
			ob_start ( 'ob_gzhandler' );
		} else {
			ob_start ();
		}
		parent::run ();
		ob_end_flush ();
	}

	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 *
	 * @see Base_Application::execute()
	 */
	protected function execute() {
		$router = new Web_Router ();
		define ( 'CONTROLLER', $router->get_controller () ); // 控制器名称
		define ( 'ACTION', $router->get_action () ); // 事件名称
		$controller = $this->controller ();
		if (method_exists ( $controller, ACTION ) && ! preg_match ( '/^[_]/i', ACTION )) {
			call_user_func ( array ($controller,ACTION ) );
		} else {
			throw new Base_Exception ( ACTION, 102 );
		}
	}

	/**
	 * 加载控制器
	 *
	 * @param string $controller
	 * @param string $app
	 */
	public static function controller($controller = null, $app = null) {
		$app = ! is_null ( $app ) ? trim ( $app ) : APP;
		$controller = ! is_null ( $controller ) ? trim ( $controller ) : CONTROLLER;
		$classname = $controller . 'Controller';
		import ( $classname, WEKIT_PATH . 'api' . DIRECTORY_SEPARATOR );
		if (class_exists ( $classname, false )) {
			return new $classname ();
		} else {
			throw new Exception ( 'Unable to create instance for ' . $classname . ' , class is not exist.' );
		}
	}

	protected function showErrorMessage($message, $file, $line, $trace, $errorcode) {
		parent::showErrorMessage ( $message, $file, $line, $trace, $errorcode );
		if (IS_DEBUG) {
			// 包含异常页面模板
			ob_start ();
			include (FW_PATH . 'errors/error_php.php');
			$buffer = ob_get_contents ();
			ob_end_clean ();
			die ( $buffer );
		}
	}
}