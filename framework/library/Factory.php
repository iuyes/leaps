<?php
/**
 * 工厂类
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Factory {
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
	 * 加载队列
	 *
	 * @param string $setting 配置
	 */
	public static function queue($setting = 'default') {
		if (! isset ( self::$instances ['queue'] [$setting] )) {
			$options = C ( 'queue', $setting );
			$class = 'Queue_Driver_' . ucfirst ( $options ['driver'] );
			self::$instances ['queue'] [$setting] = new $class ( $options );
		}
		return self::$instances ['queue'] [$setting];
	}

	public static function db($db_config = '') {
		if (! empty ( $db_config ) && is_string ( $db_config )) { // 不为空并且是字符串
			$i_name = $db_config;
			$db_config = C ( 'database', $db_config );
		} elseif (is_array ( $db_config )) { // 数组配置
			$db_config = array_change_key_case ( $db_config );
			$i_name = '.config_' . md5 ( serialize ( $db_config ) );
		} elseif (empty ( $db_config )) { // 如果配置为空，读取默认配置文件设置
			$db_config = C ( 'database', 'default' );
			$i_name = 'default';
		}
		if (! isset ( self::$instances ['db'] [$i_name] )) {
			if (! is_array ( $db_config ) || empty ( $db_config ['driver'] )) throw new Exception ( 'No database configuration.' );
			$class = 'Database_Driver_' . ucfirst ( strtolower ( $db_config ['driver'] ) );
			if (class_exists ( $class )) { // 检查驱动类
				self::$instances ['db'] [$i_name] = new $class ( $db_config );
				self::$instances ['db'] [$i_name]->open ();
			} else {
				throw new Exception ( 'No database driver' . ': ' . $class ); // 类没有定义
			}
		}
		return self::$instances ['db'] [$i_name];
	}
}