<?php
/**
 * 应用程序创建类
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Command_Application extends Base_Application {
	public function _initialize(){

	}

	public function run() {
		parent::run ();
	}

	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 * @see Base_Application::execute()
	 */
	protected function execute() {
		$router = new Api_Router ();
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
	public static function controller($controller = null) {
		$controller = ! is_null ( $controller ) ? trim ( $controller ) : CONTROLLER;
		$classname = $controller . 'Controller';
		import ( $classname, WEKIT_PATH . 'command' . DIRECTORY_SEPARATOR );
		if (class_exists ( $classname, false )) {
			return new $classname ();
		} else {
			throw new Exception ( 'Unable to create instance for ' . $classname . ' , class is not exist.' );
		}
	}
}