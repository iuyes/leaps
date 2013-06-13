<?php
/**
 * 后台首页
 * @author Tongle Xu <xutongle@gmail.com> 2013-6-13
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id$
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
class IndexController extends Admin_Controller {
	public function __construct() {
		parent::__construct ();
		$this->db = Loader::model ( 'admin_model' );
		$this->menu_db = Loader::model ( 'admin_menu_model' );
		$this->panel_db = Loader::model ( 'admin_panel_model' );
	}

	/**
	 * 后台首页
	 */
	public function init() {

		$userid = $_SESSION ['userid'];
		$admin_username = cookie ( 'admin_username' );
		$roles = S ( 'common/role' );
		$rolename = $roles [$_SESSION ['roleid']];
		$adminpanel = $this->panel_db->where ( array ('userid' => $userid ) )->order ( 'datetime' )->select ();
		include $this->view ( 'index' );
	}
}