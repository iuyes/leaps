<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-14
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id$
 */
class Loader {
	private static $instances = array ();


	/**
	 * 加载模型(单例）
	 *
	 * @param $model
	 */
	public static function model($model, $initialize = true) {
		if (! isset ( self::$instances ['model'] [$model] )) {
			import ( $model, WEKIT_PATH . 'model' . DIRECTORY_SEPARATOR );
			if ($initialize)
				self::$instances ['model'] [$model] = new $model ();
			//if (self::$instances ['controller'] [$model] instanceof SplSubject) {
			//	$plugin_dir = SOURCE_PATH . 'plugins' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR. $model . DIRECTORY_SEPARATOR;
			//	self::$instances ['controller'] [$model]->attach ( new Observer ( $plugin_dir ) );
			//}
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