<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-14
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: Abstract.php 604 2013-06-07 07:01:12Z 85825770@qq.com $
 */
abstract class Session_Abstract {
	public function __construct($options = array()) {
	}

	public static function &get_instance($options = array()) {
		if (function_exists ( 'ini_set' )) @ini_set ( 'session.gc_maxlifetime', $options ['maxlifetime'] );
		session_cache_expire ( $options ['cache_expire'] );
		session_set_cookie_params ( $options ['cookie_lifetime'], $options ['cookie_path'], $options ['cookie_domain'] );
		$class = 'Session_Driver_' . ucfirst ( $options ['driver'] );
		$return = new $class ( $options );
		return $return;
	}

	public function register() {
		session_set_save_handler ( array ($this,'open' ), array ($this,'close' ), array ($this,'read' ), array ($this,'write' ), array ($this,'destroy' ), array ($this,'gc' ) );
	}
}