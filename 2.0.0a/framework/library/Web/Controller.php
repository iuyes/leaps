<?php
/**
 * 前端控制器
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Web_Controller{

	/**
	 * 构造方法
	 */
	public function __construct() {
		$this->app = APP; // 当前的app
		$this->controller = CONTROLLER; // 当前控制器
		$this->action = ACTION; // 当前操作
		if (method_exists ( $this, '_initialize' )) $this->_initialize (); // 控制器初始化
	}

	/**
	 * Ajax方式返回数据到客户端
	 *
	 * @access protected
	 * @param mixed $data 要返回的数据
	 * @param String $type AJAX返回数据格式
	 * @return void
	 */
	protected function ajax_return($data, $type = '') {
		if (empty ( $type )) $type = C ( 'config', 'default_ajax_return' );
		switch (strtoupper ( $type )) {
			case 'JSON' :
				// 返回JSON数据格式到客户端 包含状态信息
				header ( 'Content-Type:application/json; charset=utf-8' );
				exit ( json_encode ( $data ) );
			case 'XML' :
				// 返回xml格式数据
				header ( 'Content-Type:text/xml; charset=utf-8' );
				exit ( Loader::lib ( 'Xml' )->serialize ( $data ) );
			case 'JSONP' :
				// 返回JSON数据格式到客户端 包含状态信息
				header ( 'Content-Type:application/json; charset=utf-8' );
				$handler = isset ( $_GET ['callback'] ) ? $_GET ['callback'] : C ( 'config', 'default_jsonp_callback' );
				exit ( $handler . '(' . json_encode ( $data ) . ');' );
			case 'EVAL' :
				// 返回可执行的js脚本
				header ( 'Content-Type:text/html; charset=utf-8' );
				exit ( $data );
			default :
				// 用于扩展其他返回格式数据
				header ( 'Content-Type:application/json; charset=utf-8' );
				exit ( json_encode ( $data ) );
		}
	}

	/**
	 * 析构方法
	 */
	public function __destruct() {

	}
}