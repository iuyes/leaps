<?php
/**
 * 管理员管理
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::lib ( 'admin:admin', false );
class AdminController extends admin {

	private $db;
	public function __construct() {
		parent::__construct ( 1 );
		$this->db = Loader::model ( 'admin_model' );
	}

	/**
	 * 管理员管理列表
	 */
	public function init() {
		$userid = $_SESSION ['userid'];
		$admin_username = cookie ( 'admin_username' );
		$page = isset ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$infos = $this->db->order ( 'userid DESC' )->listinfo ( $page, 20 );
		$pages = $this->db->pages;
		include $this->admin_tpl ( 'admin_list' );
	}

	/**
	 * 添加管理员
	 */
	public function add() {
		if (isset ( $_POST ['dosubmit'] )) {
			$username = isset ( $_POST ['username'] ) && trim ( $_POST ['username'] ) ? trim ( $_POST ['username'] ) : showmessage ( L ( 'nameerror' ), HTTP_REFERER );
			$password = isset ( $_POST ['password'] ) && trim ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : showmessage ( L ( 'password_can_not_be_empty' ), HTTP_REFERER );
			$issuper = isset ( $_POST ['issuper'] ) && intval ( $_POST ['issuper'] ) ? intval ( $_POST ['issuper'] ) : 0;
			if ($this->db->getby_username ( $username )) {
				showmessage ( L ( 'user_already_exist' ), HTTP_REFERER );
			} else {
				if (strlen ( $username ) > 20 || strlen ( $username ) < 6) {
					showmessage ( L ( 'username' ) . L ( 'between_6_to_20' ), HTTP_REFERER );
				}
				if (strlen ( $password ) > 20 || strlen ( $password ) < 6) {
					showmessage ( L ( 'password_len_error' ), HTTP_REFERER );
				}
				$encrypt = random ( 6 );
				$password = password ( $password, $encrypt );
				$realname = isset ( $_POST ['realname'] ) && trim ( $_POST ['realname'] ) ? trim ( $_POST ['realname'] ) : '';
				$email = isset ( $_POST ['email'] ) && trim ( $_POST ['email'] ) ? trim ( $_POST ['email'] ) : '';
				$mobile = isset ( $_POST ['mobile'] ) && trim ( $_POST ['mobile'] ) ? trim ( $_POST ['mobile'] ) : '';
				if ($this->db->insert ( array ('username' => $username,'password' => $password,'encrypt' => $encrypt,'issuper' => $issuper,'realname' => $realname,'email' => $email,'mobile' => $mobile ) )) {
					showmessage ( L ( 'add_admin' ) . L ( 'operation_success' ), '?app=admin&controller=admin&action=init' );
				} else {
					showmessage ( L ( 'database_error' ), HTTP_REFERER );
				}
			}
		} else {
			include $this->admin_tpl ( 'admin_add' );
		}
	}

	/**
	 * 修改管理员
	 */
	public function edit() {
		$userid = isset ( $_GET ['userid'] ) && intval ( $_GET ['userid'] ) ? intval ( $_GET ['userid'] ) : showmessage ( L ( 'illegal_parameters' ), HTTP_REFERER );
		if (isset ( $_POST ['dosubmit'] )) {
			$password = isset ( $_POST ['password'] ) && trim ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : '';
			$issuper = isset ( $_POST ['issuper'] ) && intval ( $_POST ['issuper'] ) ? intval ( $_POST ['issuper'] ) : 0;
			$update = array ('issuper' => $issuper );
			if ($password) {
				if (strlen ( $password ) > 20 || strlen ( $password ) < 6) {
					showmessage ( L ( 'password_len_error' ), HTTP_REFERER );
				}
				$update ['encrypt'] = random ( 6 );
				$update ['password'] = password ( $password, $encrypt );
			}
			$update ['realname'] = isset ( $_POST ['realname'] ) && trim ( $_POST ['realname'] ) ? trim ( $_POST ['realname'] ) : '';
			$update ['email'] = isset ( $_POST ['email'] ) && trim ( $_POST ['email'] ) ? trim ( $_POST ['email'] ) : '';
			$update ['mobile'] = isset ( $_POST ['mobile'] ) && trim ( $_POST ['mobile'] ) ? trim ( $_POST ['mobile'] ) : '';
			$this->db->where ( array ('userid' => $userid ) )->update ( $update );
			showmessage ( L ( 'operation_success' ), '?app=admin&controller=admin&action=init' );
		} else {
			$userinfo = $this->db->getby_userid ( $userid );
			include $this->admin_tpl ( 'admin_edit' );
		}
	}

	/**
	 * 删除管理员
	 */
	public function delete() {
		$userid = isset ( $_GET ['userid'] ) && intval ( $_GET ['userid'] ) ? intval ( $_GET ['userid'] ) : showmessage ( L ( 'illegal_parameters' ), HTTP_REFERER );
		$r = $this->db->getby_userid ( $userid );
		if ($r) {
			if ($r ['issuper']) {
				$super_num = $this->db->where ( array ('issuper' => 1 ) )->count ();
				if ($super_num <= 1) {
					showmessage ( L ( 'least_there_is_a_super_administrator' ), HTTP_REFERER );
				}
			}
			$this->db->where ( array ('userid' => $userid ) )->delete ();
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			showmessage ( L ( 'User_name_could_not_find' ), HTTP_REFERER );
		}
	}

	/**
	 * AJAX检测用户名是否可用
	 */
	public function ajax_username() {
		$username = isset ( $_GET ['username'] ) && trim ( $_GET ['username'] ) ? trim ( $_GET ['username'] ) : exit ( '0' );
		if ($this->db->getby_username ( $username )) {
			exit ( '0' );
		} else {
			exit ( '1' );
		}
	}

}