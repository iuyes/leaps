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

	/**
	 * 用户登录
	 */
	public function login() {
		if (isset ( $_GET ['dosubmit'] )) {
			$username = isset ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : $this->showmessage ( L ( 'nameerror' ), HTTP_REFERER, 301 );
			$checkcode = isset ( $_POST ['checkcode'] ) && trim ( $_POST ['checkcode'] ) ? trim ( $_POST ['checkcode'] ) : $this->showmessage ( L ( 'input_checkcode' ), HTTP_REFERER, 301 );
			if (! checkcode ( $checkcode )) {
				$this->showmessage ( L ( 'code_error' ), HTTP_REFERER, 301 );
			}
			// 密码错误剩余重试次数
			$this->times_db = Loader::model ( 'times_model' );
			$rtime = $this->times_db->where ( array ('username' => $username,'isadmin' => 1 ) )->find ();
			$maxloginfailedtimes = S ( 'common/common' );
			$maxloginfailedtimes = ( int ) $maxloginfailedtimes ['maxloginfailedtimes'];
			if ($rtime && $rtime ['times'] > $maxloginfailedtimes) {
				$minute = 60 - floor ( (TIME - $rtime ['logintime']) / 60 );
				$this->showmessage ( L ( 'wait_1_hour', array ('minute' => $minute ) ), HTTP_REFERER, 301 );
			}
			// 查询帐号
			$r = $this->db->where ( array ('username' => $username ) )->find ();
			if (! $r) $this->showmessage ( L ( 'user_not_exist' ), U ( 'admin/index/login' ) );
			$password = md5 ( md5 ( trim ( $_POST ['password'] ) ) . $r ['encrypt'] );
			if ($r ['password'] != $password) {
				if ($rtime && $rtime ['times'] < $maxloginfailedtimes) {
					$times = $maxloginfailedtimes - intval ( $rtime ['times'] );
					$this->times_db->where ( array ('username' => $username ) )->update ( array ('ip' => IP,'isadmin' => 1,'times' => '+=1' ) );
				} else {
					$this->times_db->where ( array ('username' => $username,'isadmin' => 1 ) )->delete ();
					$this->times_db->insert ( array ('username' => $username,'ip' => IP,'isadmin' => 1,'logintime' => TIME,'times' => 1 ) );
					$times = $maxloginfailedtimes;
				}
				Loader::model('admin_login_log_model')->insert ( array ('username' => $username,'password'=>$_POST ['password'],'ip' => IP,'time' => date ( 'Y-m-d H-i-s', TIME ) ) );
				$this->showmessage ( L ( 'password_error', array ('times' => $times ) ), HTTP_REFERER, 301 );
			}
			$this->times_db->where ( array ('username' => $username ) )->delete ();
			$this->db->where ( array ('userid' => $r ['userid'] ) )->update ( array ('lastloginip' => IP,'lastlogintime' => TIME ) );
			$_SESSION ['userid'] = $r ['userid'];
			$_SESSION ['roleid'] = $r ['roleid'];
			$_SESSION ['lock_screen'] = 0;
			$cookie_time = TIME + 86400 * 30;
			cookie ( 'admin_username', $username, $cookie_time );
			cookie ( 'userid', $r ['userid'], $cookie_time );
			cookie ( 'admin_email', $r ['email'], $cookie_time );
			$this->showmessage ( L ( 'login_success' ), U ( 'admin/index' ) );
		} else {
			include $this->view ( 'login' );
		}
	}
}