<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
Loader::lib ( 'admin:admin', false );
class CacheController extends admin {
	private $db;

	public function __construct() {
		parent::__construct ();
		$this->db = Loader::model('setting_model');
	}

	/**
	 * 首页
	 */
	public function init() {
		include $this->admin_tpl('cache');
	}

	public function ajax_clear() {
		$return['filesize'] = '2';
		$return['filemtime'] = date('Y-m-d H:i:s', TIME);
		exit(json_encode($return));
	}
}