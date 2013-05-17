<?php
/**
 * Loader类
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-17
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: Loader.php 558 2013-05-17 06:37:38Z 85825770@qq.com $
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
		if (! $initialize) return self::get_instance ( $classname, false );
		return Factory::get_instance ( $classname );
	}
}