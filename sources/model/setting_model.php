<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
defined('IN_YUNCMS') or exit('No permission resources.');
class setting_model extends Model {

	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'setting';
		parent::__construct();
	}
}