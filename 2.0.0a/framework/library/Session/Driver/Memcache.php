<?php
/**
 * Session Memcache驱动
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-14
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: Memcache.php 552 2013-05-17 03:44:01Z 85825770@qq.com $
 */
class Session_Driver_Memcache extends Session_Driver_File {

	public function __construct($options = array()) {
		if (! $this->test ()) {
			throw new Exception ( "The memcache extension isn't available" );
		}
		ini_set ( 'session.save_handler', 'memcache' );
		ini_set ( 'session.save_path', $options ['memcache_servers'] );
	}

	public function test() {
		return extension_loaded ( 'memcache' );
	}
}