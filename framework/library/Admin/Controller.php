<?php
/**
 * 前端控制器
 * @author Tongle Xu <xutongle@gmail.com> 2013-6-8
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id$
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class Admin_Controller{

	public $userid;
	public $username;

	/**
	 * 构造方法
	 */
	public function __construct() {
		Loader::session ();
		if (method_exists ( $this, '_initialize' )) $this->_initialize ();
		self::check_admin ();
	}

	/**
	 * 判断用户是否已经登陆
	 */
	final public function check_admin() {
		if (APP == 'system' && CONTROLLER == 'Index' && in_array ( ACTION, array ('login' ) )) {
			return true;
		} else {
			if (! isset ( $_SESSION ['userid'] ) || ! isset ( $_SESSION ['roleid'] ) || ! $_SESSION ['userid'] || ! $_SESSION ['roleid']) {
				$this->showmessage ( L ( 'admin_login' ), U ( 'system/index/login' ) );
			}
		}
	}

	/**
	 * 加载后台模板
	 *
	 * @param string $file 文件名
	 * @param string $application 模型名
	 */
	final public function view($file, $application = '') {
		$application = empty ( $application ) ? APP : $application;
		if (empty ( $application )) return false;
		$path = APPS_PATH . $application . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $file . '.tpl.php';
		if(!file_exists($path)) throw new Exception('Oops! System file lost: '.$path);
		return $path;
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
	 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
	 * showmessage('登录成功', array('默认跳转地址'=>'http://www.yuncms.net'));
	 *
	 * @param string $msg 提示信息
	 * @param mixed(string/array) $url_forward 跳转地址
	 * @param int $ms 跳转等待时间
	 */
	public function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
		if ($ms == 301) {
			Factory::session ();
			$_SESSION ['msg'] = $msg;
			Header ( "HTTP/1.1 301 Moved Permanently" );
			Header ( "Location: $url_forward" );
			exit ();
		}
		include ($this->view ( 'showmessage', 'system' ));
		if (isset ( $_SESSION ['msg'] )) unset ( $_SESSION ['msg'] );
		exit ();
	}

	/**
	 * 析构方法
	 */
	public function __destruct() {

	}
}