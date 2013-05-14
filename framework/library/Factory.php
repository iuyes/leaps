<?php
/**
 * 工厂类
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-14
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id$
 */
class Factory{

	private static $instances = array ();

	/**
	 * 加载缓存
	 *
	 * @param string $setting 配置项
	 */
	public static function cache($options = null) {
		if (! empty ( $options ) && is_string ( $options )) { // 不为空并且是字符串
			$i_name = $options;
			$options = C ( 'cache', $options );
		} elseif (is_array ( $options )) { // 数组配置
			$options = array_change_key_case ( $options );
			$i_name = '.config_' . md5 ( serialize ( $options ) );
		} else { // 加载默认配置
			$options = C ( 'cache', 'default' );
			$i_name = 'default';
		}
		if (! isset ( self::$instances ['cache'] [$i_name] ) || ! is_object ( self::$instances ['cache'] [$i_name] )) {
			$class = 'Cache_Driver_' . $options ['driver'];
			self::$instances ['cache'] [$i_name] = new $class ( $options );
			self::$instances ['cache'] [$i_name]->set_config ( $options );
		}
		return self::$instances ['cache'] [$i_name];
	}

	/**
	 * 加载Session
	 */
	public static function session() {
		if (! isset ( self::$instances ['session'] )) {
			if (function_exists ( 'ini_set' )) @ini_set ( 'session.gc_maxlifetime', C ( 'session', 'maxlifetime' ) );
			session_cache_expire ( C ( 'session', 'cache_expire' ) );
			session_set_cookie_params ( C ( 'session', 'cookie_lifetime' ), C ( 'session', 'cookie_path' ), C ( 'session', 'cookie_domain' ) );
			Session_Abstract::get_instance ( C ( 'session' ) );
			if (isset ( $_GET ['sid'] ) && ! empty ( $_GET ['sid'] )) session_id ( trim ( $_GET ['sid'] ) );
			session_start ();
			define ( 'SID', session_id () );
			self::$instances ['session'] = true;
		}
		return self::$instances ['session'];
	}
}