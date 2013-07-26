<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-28
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id$
 */
class badword_model extends Model {
	public $table_name = '';
	public function __construct() {
		$this->setting = 'default';
		$this->table_name = 'badword';
		parent::__construct ();
	}

	/**
	 * 敏感词处理接口
	 * 对传递的数据进行处理,并返回
	 */
	function replace_badword($str) {
		// 读取敏感词缓存
		$badword_cache = S ( 'common/badword' );
		if ($badword_cache) {
			foreach ( $badword_cache as $data ) {
				if ($data ['replaceword'] == '') {
					$replaceword_new = '*';
				} else {
					$replaceword_new = $data ['replaceword'];
				}
				$replaceword [] = ($data ['level'] == '1') ? $replaceword_new : '';
				$replace [] = $data ['badword'];
			}
			$str = str_replace ( $replace, $replaceword, $str );
		}
		return $str;
	}
}