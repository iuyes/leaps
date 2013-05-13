<?php
/**
 * 应用程序创建类
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Web_Application extends Base_Application {

	public function _initialize(){
		@header ( 'Content-Type: text/html; charset=' . CHARSET );
		@header ( 'X-Powered-By: PHP/' . PHP_VERSION . ' Leaps/' . LEAPS_VERSION );
		// 访客IP
		define ( 'IP', Web_Request::get_client_ip () );
		// 前执行脚本的绝对路径
		define ( 'PHP_FILE', htmlspecialchars ( Web_Request::get_script_url () ) );
		/* 脚本名称 */
		define ( 'SCRIPT_NAME', Web_Request::get_script () );
		// 所在目录
		define ( 'WEB_PATH', substr ( PHP_FILE, 0, strrpos ( PHP_FILE, '/' ) ) . '/' );
		/* 协议 */
		define ( 'SITE_PROTOCOL', Web_Request::is_ssl () ? 'https://' : 'http://' );
		/* 主机名 */
		define ( 'SITE_HOST', Web_Request::get_host () );
		/* 基础URL */
		define ( 'SITE_URL', htmlspecialchars ( Web_Request::get_base_url ( true ) ) . '/' );
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
		// 页面压缩输出支持
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
	 * @see Base_Application::execute()
	 */
	protected function execute() {
		$router = new Web_Router ();
		define ( 'APP', $router->get_app () ); // 应用名称
		define ( 'CONTROLLER', $router->get_controller () ); // 控制器名称
		define ( 'ACTION', $router->get_action () ); // 事件名称
		$controller = $this->controller ( CONTROLLER, APP );
		if (method_exists ( $controller, ACTION ) && ! preg_match ( '/^[_]/i', ACTION )) {

			call_user_func ( array ($controller,ACTION ) );
		} else {
			throw new Base_Exception ( ACTION, 102 );
		}
	}

	public function controller($controller = null, $app = null) {
		$app = ! is_null ( $app ) ? trim ( $app ) : APP;
		$controller = ! is_null ( $controller ) ? trim ( $controller ) : CONTROLLER;
		$classname = $controller . 'Controller';
		$import = defined ( 'IN_ADMIN' ) ? $app . ':admin.' . $classname : $app . ':controller.' . $classname;
		import ( $import );
		if (class_exists ( $classname, false )) {
			return new $classname ();

		} else {
			throw new Base_Exception ( $classname, 100 );
		}
	}
}