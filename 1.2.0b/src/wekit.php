<?php
/**
 * 应用入口文件
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
define('IN_YUNCMS', true);
define ( 'WEKIT_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
define ( 'BASE_PATH', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . DIRECTORY_SEPARATOR );
define ( 'CUSTOM_PATH', BASE_PATH );
define ( 'DATA_PATH', BASE_PATH . 'data' . DIRECTORY_SEPARATOR );
define ( 'APPS_PATH', WEKIT_PATH . 'apps' . DIRECTORY_SEPARATOR );
defined ( 'IS_DEBUG' ) || define ( 'IS_DEBUG', true );
require WEKIT_PATH . '../framework/kernel.php';
class Wekit {
	/**
	 * 初始化应用
	 * @param string $type
	 * @param array $config
	 */
	public static function init($type = 'Web', $config = array()){
		return Core::application ( $type, $config );
	}

	/**
	 * 创建并运行应用程序
	 *
	 * @param string $type 应用模式
	 */
	public static function run($type = 'Web', $config = array()) {
		$application = self::init($type, $config);
		$application->run ();
	}
}