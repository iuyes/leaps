<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::lib ( 'admin:admin', false );
class IndexController extends admin {

	private $db;

	public function __construct() {
		parent::__construct ();
		$this->db = Loader::model ( 'admin_model' );
	}

	/**
	 * 后台首页
	 */
	public function init() {
		$userinfo = $this->db->getby_userid ( $_SESSION ['userid'] );
		include $this->admin_tpl ( 'index' );
	}

	/**
	 * 首页右侧
	 */
	public function right() {
		$mysql_version = $this->db->get_version (); // mysql版本
		$mysql_table_status = $this->db->get_table_status ();
		$mysql_table_size = $mysql_table_index_size = '';
		foreach ( $mysql_table_status as $table ) {
			$mysql_table_size += $table ['Data_length'];
			$mysql_table_index_size += $table ['Index_length'];
		}
		$mysql_table_size = sizecount ( $mysql_table_size );
		$mysql_table_index_size = sizecount ( $mysql_table_index_size );

		include $this->admin_tpl ( 'right' );
	}

	/**
	 * 后台登陆
	 */
	public function login() {
		if (isset ( $_GET ['dosubmit'] )) {
			$username = isset ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : showmessage ( L ( 'nameerror' ), HTTP_REFERER, 301 );
			$password = isset ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : showmessage ( L ( 'password_len_error' ), HTTP_REFERER, 301 );
			$checkcode = isset ( $_POST ['checkcode'] ) && trim ( $_POST ['checkcode'] ) ? trim ( $_POST ['checkcode'] ) : showmessage ( L ( 'input_checkcode' ), HTTP_REFERER, 301 );
			if (! checkcode ( $checkcode )) {
				showmessage ( L ( 'code_error' ), HTTP_REFERER, 301 );
			}
			// 查询帐号
			$r = $this->db->getby_username ( $username );
			if (! $r) showmessage ( L ( 'user_not_exist' ), U ( 'admin/index/login' ) );
			$password = password ( $password, $r ['encrypt'] );
			if ($r ['password'] != $password) {
				showmessage ( L ( 'password_error' ), HTTP_REFERER, 301 );
			}
			$this->db->where ( array ('userid' => $r ['userid'] ) )->update ( array ('lastloginip' => IP,'lastlogintime' => TIME ) );
			$_SESSION ['userid'] = $r ['userid'];
			$_SESSION ['lock_screen'] = 0;
			$cookie_time = TIME + 86400 * 30;
			cookie ( 'admin_username', $username, $cookie_time );
			cookie ( 'userid', $r ['userid'], $cookie_time );
			cookie ( 'admin_email', $r ['email'], $cookie_time );
			if ($r ['userid'] == 1) {
				showmessage ( 'Welcome you dad, welcome to enter god mode.', U ( 'admin/index' ) );
			} else {
				showmessage ( L ( 'login_success' ), U ( 'admin/index' ) );
			}
		} else {
			include $this->admin_tpl ( 'login' );
		}
	}

	/**
	 * 后台退出
	 */
	public function logout() {
		$_SESSION ['userid'] = 0;
		$_SESSION ['roleid'] = 0;
		cookie ( 'admin_username', null );
		cookie ( 'userid', null );
		showmessage ( L ( 'logout_succeeded' ), '?app=admin&controller=index&action=login' );
	}
}