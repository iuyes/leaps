<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Loader{
	private static $instances = array ();

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