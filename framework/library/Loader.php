<?php
/**
 *
 * Loader.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Loader{
	private static $instances = array ();

	/**
	 * 加载Session
	*/
	public static function session() {
		if (! isset ( self::$instances ['session'] )) {
			if (function_exists ( 'ini_set' )) @ini_set ( 'session.gc_maxlifetime', C ( 'session', 'maxlifetime' ) );
			session_cache_expire ( C ( 'session', 'cache_expire' ) );
			session_set_cookie_params ( C ( 'session', 'cookie_lifetime' ), C ( 'session', 'cookie_path' ), C ( 'session', 'cookie_domain' ) );
			Session_Abstract::get_instance ( C ( 'session' ) );
			if (isset ( $_GET ['SID'] ) && ! empty ( $_GET ['SID'] )) session_id ( trim ( $_GET ['SID'] ) );
			session_start ();
			define ( 'SID', session_id () );
			self::$instances ['session'] = true;
		}
		return self::$instances ['session'];
	}

	/**
	 * 加载模型
	 *
	 * @param $model
	 */
	public static function model($model, $initialize = true) {
		if (! isset ( self::$instances ['model'] [$model] )) {
			import ( $model, WEKIT_PATH . 'model' . DIRECTORY_SEPARATOR );
			if ($initialize)
				self::$instances ['model'] [$model] = new $model ();
			else
				return true;
		}
		return self::$instances ['model'] [$model];
	}

	/**
	 * 加载助手
	 *
	 * @param string $helper
	 */
	public static function helper($helper) {
		if (! isset ( self::$instances ['helper'] [$helper] )) {
			if (strpos ( $helper, ':' ) !== false) {
				list ( $app, $app_helper ) = explode ( ':', $helper );
				$import = $app . ':' . 'helper.' . $app_helper; // 构建加载应用类的相关字符
				import ( $import );
			} else {
				import ( 'helper.' . $helper );
			}
			self::$instances ['helper'] [$helper] = true;
		}
		return self::$instances ['helper'] [$helper];
	}

	/**
	 * 加载类库
	 *
	 * @param string $classname 类名
	 * @param bool $initialize 是否自动实例化
	 */
	public static function lib($classname, $initialize = true) {
		if (! $initialize) return Factory::get_instance ( $classname, false );
		return Factory::get_instance ( $classname );
	}
}