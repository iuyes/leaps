<?php
/**
 * 测试使用的数据模型
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-23
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id$
 */
class admin_log_model extends Model {

	public function __construct() {
		$this->setting = 'default';//要加载的数据库配置
		$this->table_name = 'test';//要操作的数据表
		parent::__construct ();
	}
}