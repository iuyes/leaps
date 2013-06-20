<?php
/**
 *
 * wekit.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
define ( 'IN_YUNCMS', true );
define ( 'BASE_PATH', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . DIRECTORY_SEPARATOR );
define ( 'WEKIT_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
define ( 'APPS_PATH', WEKIT_PATH . 'apps' . DIRECTORY_SEPARATOR );
define ( 'DATA_PATH', WEKIT_PATH . 'data' . DIRECTORY_SEPARATOR );
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