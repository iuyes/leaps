<?php
/**
 *
 * Application.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Web_Application extends Base_Application {

	/**
	 * @see Base_Application::_initialize()
	 */
	public function _initialize() {
		@header ( 'Content-Type: text/html; charset=' . CHARSET );
		@header ( 'X-Powered-By: PHP/' . PHP_VERSION . ' Leaps/' . LEAPS_VERSION );
		/* 协议 */
		define ( 'SITE_PROTOCOL', Base_Request::is_ssl () ? 'https://' : 'http://' );
		/* 主机名 */
		define ( 'SITE_HOST', Base_Request::get_host () );
		/* 基础URL */
		define ( 'SITE_URL', htmlspecialchars ( Base_Request::get_base_url ( true ) ) . '/' );
		/* 设置来源 */
		define ( 'HTTP_REFERER', Base_Request::get_referer () );
		Web_Filter::input ();
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
		$router = new Web_Router ();
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

}