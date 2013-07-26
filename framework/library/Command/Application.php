<?php
/**
 * Application.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Command_Application {

	/**
	 * 构造方法
	 */
	public function __construct($config = array()) {
		$router = new Command_Router ();
		define ( 'CONTROLLER', $router->get_controller () ); // 控制器名称
		define ( 'ACTION', $router->get_action () ); // 事件名称
	}
	public function run() {
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