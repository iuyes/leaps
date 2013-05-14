<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-26
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: IndexController.php 6 2013-01-16 03:57:50Z xutongle $
 */
class IndexController extends Web_Controller {

	public $db;

	public function __construct() {
		parent::__construct ();
		$thi->db = Loader::model('admin_model');
		print_r($this->db);
	}

	/**
	 * 首页
	 */
	public function init() {
		$this->db->select();
		include V ( 'index' );
	}
}