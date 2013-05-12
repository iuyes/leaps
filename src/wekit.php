<?php
/**
 * 系统入口类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-24
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: init.php 2 2013-01-14 07:14:05Z xutongle $
 */
define ( 'BASE_PATH', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . DIRECTORY_SEPARATOR );
define ( 'WEKIT_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
define ( 'DATA_PATH', BASE_PATH . 'data' . DIRECTORY_SEPARATOR );
define ( 'APPS_PATH', WEKIT_PATH . 'apps' . DIRECTORY_SEPARATOR );
//defined('IS_DEBUG') || define('IS_DEBUG', false);
require WEKIT_PATH . '../framework/kernel.php';
class Wekit {
	/**
	 * 创建并运行应用程序
	 * @param string $type 应用模式
	 */
	public static function run($type = 'Web') {
		$application = Core::application('Web');
		$application->run();
	}
}