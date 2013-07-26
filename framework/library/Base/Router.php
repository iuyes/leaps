<?php
/**
 * 路由解析抽象类
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
abstract class Base_Router {

	protected $_config = array ();
	protected $pattern = '';
	protected $reverse = '';
	protected $params = array ();
	protected $_pathinfo = '';

	public $app;
	public $controller;
	public $action;

	/**
	 * 构造方法
	 */
	public function __construct() {
		$this->_initialize ();
		$this->match ();
		$this->set_params();
	}

	/**
	 * 初始化配置
	 */
	abstract public function _initialize();

	/**
	 * 解析路由参数
	 */
	public function match() {
		if (! $this->_pathinfo || ! preg_match ( $this->pattern, $this->_pathinfo, $matches ) || strpos ($this->_pathinfo, '.php' ) !== false) return;
		list ( , $_args ) = explode ( '?', $this->_pathinfo . '?', 2 );
		$_args = trim ( $_args, '?' );
		$_args = Url::url_to_args ( $_args, true, $this->separator );
		$params = array ();
		foreach ( $this->params as $k => $v ) {
			if (isset ( $matches [$v] )) $params [$k] = trim ( $matches [$v], '-/' );
			unset ( $_args [$k] );
		}
		$_args && $params = array_merge ( $params, $_args );
		if (defined ( 'SUB_DOMAIN' )) $params ['app'] = isset ( $this->_config ['application'] ) ? $this->_config ['application'] : SUB_DOMAIN; // 设定应用名称;
		$_GET = array_merge ( $_GET, $params );
		return ;
	}

	/**
	 * 将路由解析到的url参数信息保存早系统变量中
	 *
	 * @return void
	 */
	protected function set_params() {
		if(isset($this->params['app'])) $this->set_app(Base_Request::get_request('app', $this->_config ['application']));
		$this->set_controller(Base_Request::get_request('controller', $this->_config ['controller']));
		$this->set_action(Base_Request::get_request('action', $this->_config ['action']));
		// 合并配置文件到变量
		if (isset ( $this->_config ['data'] ['POST'] ) && is_array ( $this->_config ['data'] ['POST'] )) {
			foreach ( $this->_config ['data'] ['POST'] as $_key => $_value ) {
				if (! isset ( $_POST [$_key] )) $_POST [$_key] = $_value;
			}
		}
		if (isset ( $this->_config ['data'] ['GET'] ) && is_array ( $this->_config ['data'] ['GET'] )) {
			foreach ( $this->_config ['data'] ['GET'] as $_key => $_value ) {
				if (! isset ( $_GET [$_key] )) $_GET [$_key] = $_value;
			}
		}
		if (isset ( $_GET ['page'] )) $_GET ['page'] = max ( intval ( $_GET ['page'] ), 1 );
		return;
	}

	/**
	 *
	 * @param string
	 */
	public function set_app($app) {
		if (! preg_match ( "/^[a-zA-Z0-9_]+$/", $app )) {
			throw new Exception ( "app 参数非法" );
		}
		$this->app = $app;
	}

	/**
	 *
	 * @return string
	 */
	public function get_app() {
		return $this->app;
	}

	/**
	 * 设置controller
	 *
	 * @param string $controller
	 * @return void
	 */
	public function set_controller($controller) {
		if (! preg_match ( "/^[a-zA-Z0-9_]+$/", $controller)) {
			throw new Exception ( "controller 参数非法" );
		}
		$this->controller = ucfirst ($controller);
	}

	/**
	 * 返回controller
	 *
	 * @return string
	 */
	public function get_controller() {
		return $this->controller;
	}

	/**
	 * 设置action
	 *
	 * @param string $action
	 * @return void
	 */
	public function set_action($action) {
		if (! preg_match ( "/^[a-zA-Z0-9_]+$/", $action )) {
			throw new Exception ( "action 参数非法" );
		}
		$this->action = $action;
	}

	/**
	 * 返回action
	 *
	 * @return string
	 */
	public function get_action() {
		return $this->action;
	}
}