<?php
/**
 * 引擎出入
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */

define('IN_YUNCMS', true);
define ( 'BASE_PATH', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . DIRECTORY_SEPARATOR );
define ( 'WEKIT_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
define ( 'APPS_PATH', WEKIT_PATH . 'apps' . DIRECTORY_SEPARATOR );
define ( 'DATA_PATH', WEKIT_PATH . 'data' . DIRECTORY_SEPARATOR );
define ( 'CONFIG_PATH', WEKIT_PATH . 'config' . DIRECTORY_SEPARATOR );
defined ( 'IS_DEBUG' ) || define ( 'IS_DEBUG', true );
require WEKIT_PATH . '../framework/kernel.php';
class Wekit {
	/**
	 * 创建并运行应用程序
	 *
	 * @param string $type 应用模式
	 */
	public static function run($type = 'Web', $config = array()) {
		$application = Core::application ( $type, $config );
		self::init();
		$application->run ();
	}

	/**
	 * 引擎初始化
	 */
	public static function init(){
		define ( 'JS_PATH', C ( 'system', 'js_path' ,'statics/js/') ); // CDN JS路径
		define ( 'CSS_PATH', C ( 'system', 'css_path' ,'statics/css/') ); // CDN CSS路径
		define ( 'IMG_PATH', C ( 'system', 'img_path' ,'statics/images/') ); // CDN IMG路径
		define ( 'SKIN_PATH', C ( 'system', 'skin_path' ,'statics/skins/') );//CDN IMG路径
	}
}
