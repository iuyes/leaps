<?php
/**
 * Cookie 类
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Cookie {

	/**
	 * cookie的配置
	 *
	 * @var array
	 */
	protected static $config;

	/**
	 * 判断Cookie是否存在
	 *
	 * @param string $key 变量名
	 * @return boolean 成功则返回true，否则返回 false
	 */
	public static function is_set($key) {
		if (!self::$config) self::$config = C ( 'cookie');
		return isset ( $_COOKIE [self::$config ['prefix'] . $key] );
	}

	/**
	 * 设置Cookie
	 *
	 * @param string $name Cookie名称
	 * @param string $value Cookie值
	 * @param integer $expiration 过期时间（单位：秒）
	 * @return boolean
	 * @uses Cookie::salt
	 */
	public static function set($key, $value, $expiration = NULL, $path = '/', $domain = null) {
		if (headers_sent ()) return false;
		if (!self::$config) self::$config = C ( 'cookie');
		if ($value == '') {
			$expiration = TIME - 86400;
		} else if ($expiration === NULL) {
			$expiration = self::$config ['expiration'];
		} elseif ($expiration !== 0) {
			$expiration += TIME;
		}
		$_COOKIE [$key] = $value;
		$secure = $_SERVER ['HTTPS'] == 'on' ? 1 : 0;
		// Value加盐
		$value = Cookie::salt ( $key, $value ) . '~' . $value;
		return setcookie ( self::$config ['prefix'] . $key, base64_encode ( $value ), $expiration, $path, $domain, $secure);
	}

	/**
	 * 获取Cookie值
	 *
	 * @param string $key cookie name
	 * @param mixed $default default value to return
	 * @return string
	 */
	public static function get($key, $default = NULL) {
		if (!self::$config) self::$config = C ( 'cookie');
		if (! isset ( $_COOKIE [self::$config ['prefix'] . $key] )) return $default;
		$cookie = base64_decode ( $_COOKIE[self::$config ['prefix'] .$key]);
		$split = strlen ( self::salt ( $key, NULL ) );
		if (isset ( $cookie [$split] ) and $cookie [$split] === '~') {
			list ( $hash, $value ) = explode ( '~', $cookie, 2 );
			if (self::salt ( $key, $value ) === $hash) {
				return $value;
			}
			self::delete ( $key );
		}
		return $default;
	}

	/**
	 * 删除Cookie
	 *
	 * @param string $name Cookie名称
	 * @return boolean
	 * @uses Cookie::set
	 */
	public static function delete($key) {
		unset ( $_COOKIE [self::$config ['prefix'] . $key] );
		return self::set ( $key, '', - 86400 );
	}

	/**
	 * Cookie加盐
	 *
	 * @param string $name Cookie名称
	 * @param string $value Cookie值
	 * @return string
	 */
	public static function salt($name, $value) {
		if (! self::$config ['salt']) {
			throw new Exception ( 'A valid cookie salt is required. Please set Cookie::$salt.' );
		}
		$agent = isset ( $_SERVER ['HTTP_USER_AGENT'] ) ? strtolower ( $_SERVER ['HTTP_USER_AGENT'] ) : 'unknown';
		return sha1 ( $agent . $name . $value . self::$config ['salt'] );
	}
}