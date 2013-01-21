<?php
/**
 * 管理员表
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
defined('IN_YUNCMS') or exit('No permission resources.');
class admin_model extends Model {

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'admin';
		parent::__construct();
	}

	public function get_table_status() {
		$datalist = $this->db->query("SHOW TABLE STATUS LIKE '$this->prefix%'");
		return $datalist;
	}
}