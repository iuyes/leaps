<?php
/**
 * 应用程序创建类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-24
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: Application.php 2 2013-01-14 07:14:05Z xutongle $
 */
class Core_Application {

	protected static $instance = null;

	public function __construct() {
		$this->_init_env ();
		$this->_init_input ();
	}

	/**
	 * 获取自身实例
	 */
	public static function &get_instance() {
		if (null === self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}

	private function _init_env() {

	}

	/**
	 * 初始化缓存流 暂不使用
	 */
	public static function _init_cache() {
		// 注册缓存流
		if (! in_array ( "cache", stream_get_wrappers () )) {
			stream_wrapper_register ( "cache", "Cache_Wrapper" );
		}
	}

	/**
	 * 处理用户输入
	 */
	private function _init_input() {

	}

	public static function execute($app = null, $controller = null, $action = null) {
		Core_Router::get_instance ( $app, $controller, $action );
		$app = ! is_null ( $app ) ? trim ( $app ) : APP;
		$controller = ! is_null ( $controller ) ? trim ( $controller ) : CONTROLLER;
		$action = ! is_null ( $action ) ? trim ( $action ) : ACTION;
		$controller = Loader::controller ( $controller, $app );
		if (method_exists ( $controller, $action ) && ! preg_match ( '/^[_]/i', $action )) {
			call_user_func ( array ($controller,$action ) );
		} else {
			throw_exception ( 'You are visiting the action is to protect the private action' );
		}
	}

	public static function execute_api($controller = null, $action = null) {
		Core_Router::get_instance ( null, $controller, $action );
		$controller = ! is_null ( $controller ) ? trim ( $controller ) : CONTROLLER;
		$action = ! is_null ( $action ) ? trim ( $action ) : ACTION;
		$classname = $controller . 'Controller';
		import ( $classname, SOURCE_PATH . 'api' . DIRECTORY_SEPARATOR );
		if (class_exists ( $classname, false )) {
			$controller_object = new $classname ();
		} else {
			throw_exception ( 'Unable to create instance for ' . $classname . ' , class is not exist.' );
		}
		if (method_exists ( $controller_object, $action ) && ! preg_match ( '/^[_]/i', $action )) {
			call_user_func ( array ($controller_object,$action ) );
		} else {
			throw_exception ( 'You are visiting the action is to protect the private action' );
		}
	}

	public static function execute_cli($controller = null, $action = null) {
		Core_Router::get_instance ( null, $controller, $action );
		$controller = ! is_null ( $controller ) ? trim ( $controller ) : CONTROLLER;
		$action = ! is_null ( $action ) ? trim ( $action ) : ACTION;
		$classname = $controller . 'Controller';
		import ( $classname, SOURCE_PATH . 'cli' . DIRECTORY_SEPARATOR );
		if (class_exists ( $classname, false )) {
			$controller_object = new $classname ();
		} else {
			throw_exception ( 'Unable to create instance for ' . $classname . ' , class is not exist.' );
		}
		if (method_exists ( $controller_object, $action ) && ! preg_match ( '/^[_]/i', $action )) {
			call_user_func ( array ($controller_object,$action ) );
		} else {
			throw_exception ( 'You are visiting the action is to protect the private action' );
		}
		if (C ( 'config', 'show_time' )) echo show_time ();
	}

}