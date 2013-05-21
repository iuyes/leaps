<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-10-31
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: Wincache.php 549 2013-05-17 03:41:34Z 85825770@qq.com $
 */
class Cache_Driver_Wincache extends Cache {

	/**
	 * 构造函数
	 *
	 * 如果没有安装wincache扩展,则抛出异常
	 *
	 * @throws cache_exception 当没有安装wincache扩展的时候抛出
	 */
	public function __construct() {
		if (! function_exists ( 'wincache_ucache_get' )) {
			throw new Exception ( 'The wincache extension must be loaded !' );
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Cache::set_value()
	 */
	protected function set_value($key, $value, $expire = 0) {
		return wincache_ucache_set ( $key, $value, $expire );
	}

	/**
	 * (non-PHPdoc)
	 * @see Cache::get_value()
	 */
	protected function get_value($key) {
		return wincache_ucache_get ( $key );
	}

	/**
	 * (non-PHPdoc)
	 * @see Cache::delete_value()
	 */
	protected function delete_value($key) {
		return wincache_ucache_delete ( $key );
	}

	/**
	 * (non-PHPdoc)
	 * @see Cache::clear()
	 */
	public function clear() {
		return wincache_ucache_clear ();
	}
}