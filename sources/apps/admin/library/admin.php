<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::session ();
define ( 'IN_ADMIN', true );
/**
 * 后台基类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-11-1
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: admin.php 275 2012-11-08 07:40:09Z xutongle $
 */
class admin {

	public function __construct($issuper = 0) {
		self::check_admin ($issuper);
		Loader::helper('admin:global');
	}

	/**
	 * 判断用户是否已经登陆
	 */
	final public function check_admin($issuper = 0) {
		if (APP == 'admin' && CONTROLLER == 'Index' && in_array ( ACTION, array ('login' ) )) {
			return true;
		} else {
			if (! isset ( $_SESSION ['userid'] ) || ! $_SESSION ['userid']) showmessage ( L ( 'admin_login' ), U ( 'admin/index/login' ) );
			if ($issuper) {
				$r = Loader::model ( 'admin_model' )->getby_userid($_SESSION ['userid']);
				if ($r['issuper'] != 1) {
					showmessage(L('eaccess'));
				}
			}
		}
	}

	/**
	 * 加载后台模板
	 *
	 * @param string $file 文件名
	 * @param string $application 模型名
	 */
	final public static function admin_tpl($file, $application = '') {
		$application = empty ( $application ) ? APP : $application;
		if (empty ( $application )) return false;
		return APPS_PATH . $application . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $file . '.tpl.php';
	}
}