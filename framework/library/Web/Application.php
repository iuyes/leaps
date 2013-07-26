<?php
/**
 * Web_FrontController
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Web_Application {

	/**
	 * 构造方法
	 */
	public function __construct($config = array()) {
		$this->_initialize ();
		$this->_init_router ();
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Base_Application::_initialize()
	 */
	public function _initialize() {

	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Base_Application::_init_router()
	 */
	public function _init_router() {
		$router = new Web_Router ();
		define ( 'APP', $router->get_app () ); // 控制器名称
		define ( 'CONTROLLER', $router->get_controller () ); // 控制器名称
		define ( 'ACTION', $router->get_action () ); // 事件名称
	}

	/**
	 * 运行应用程序
	 *
	 * @see Base_Application::run()
	 */
	public function run() {
		$this->execute ();
	}

	/**
	 * 创建应用程序
	 *
	 * @param array $config
	 */
	protected function execute() {
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
			import ( $app . ':' . $classname );
		if (class_exists ( $classname, false )) {
			return new $classname ();
		} else {
			throw new Base_Exception ( 'Unable to create instance for ' . $classname . ' , class is not exist.', 100 );
		}
	}
}