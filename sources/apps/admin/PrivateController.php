<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::lib ( 'admin:admin', false );
class PrivateController extends admin {
	private $db;

	public function __construct() {
		parent::__construct ();
		$this->db = Loader::model ( 'admin_model' );
	}

	/**
	 * 编辑用户信息
	 */
	public function init() {
		$userid = $_SESSION ['userid'];
		if (isset ( $_POST ['dosubmit'] )) {
			$update = array ();
			$password = isset ( $_POST ['password'] ) && trim ( $_POST ['password'] ) ? trim ( $_POST ['password'] ) : showmessage ( L ( 'the_password_cannot_be_empty' ), HTTP_REFERER );
			$r = $this->db->where ( array ('userid' => $userid ) )->field ( 'password,encrypt' )->find ();
			if (password ( $password, $r ['encrypt'] ) !== $r ['password']) showmessage ( L ( 'old_password_wrong' ), HTTP_REFERER );
			if (isset ( $_POST ['newpassword'] ) && trim ( $_POST ['newpassword'] )) {
				$newpassword = trim ( $_POST ['newpassword'] );
				$newpassword2 = isset ( $_POST ['newpassword2'] ) && trim ( $_POST ['newpassword2'] ) ? trim ( $_POST ['newpassword2'] ) : '';
				if (strlen ( $newpassword ) > 20 || strlen ( $newpassword ) < 6) {
					showmessage ( L ( 'password_len_error' ), HTTP_REFERER );
				} elseif ($newpassword != $newpassword2) {
					showmessage ( L ( 'the_two_passwords_are_not_the_same_admin_zh' ), HTTP_REFERER );
				}
				$update ['encrypt'] = random ( 6 );
				$update ['password'] = password ( $newpassword, $update ['encrypt'] );
			}
			$update ['realname'] = isset ( $_POST ['realname'] ) && trim ( $_POST ['realname'] ) ? trim ( $_POST ['realname'] ) : '';
			$update ['email'] = isset ( $_POST ['email'] ) && trim ( $_POST ['email'] ) ? trim ( $_POST ['email'] ) : '';
			$update ['mobile'] = isset ( $_POST ['mobile'] ) && trim ( $_POST ['mobile'] ) ? trim ( $_POST ['mobile'] ) : '';
			$this->db->where ( array ('userid' => $userid ) )->update ( $update );
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$userinfo = $this->db->getby_userid ( $userid );
			include $this->admin_tpl ( 'admin_edit_info' );
		}
	}
}