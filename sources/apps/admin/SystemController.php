<?php
/**
 * 系统设置
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::lib ( 'admin:admin', false );
class SystemController extends admin {

	private $db;
	public function __construct() {
		parent::__construct ();
		$this->db = Loader::model('setting_model');
	}

	/**
	 * 配置信息
	 */
	public function init() {
		$show_validator = $show_header = true;
		$config = C ( 'config' ); // 加载框架配置
		$system = K('system');
		$v = array('system'=>'1');
		K('system',$v);
		include $this->admin_tpl ( 'setting' );
	}

	/**
	 * 保存配置信息
	 */
	public function save() {

		showmessage ( L ( 'setting_succ' ), HTTP_REFERER );
	}

	/**
	 * 设置缓存
	 */
	private function setcache() {
		$result = $this->db->where ( array ('application' => 'admin' ) )->find ();
		$setting = string2array ( $result ['setting'] );
		S ( 'common/common', $setting );
	}
}