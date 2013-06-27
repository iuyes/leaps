<?php
/**
 *
 *
 *
 *
 *
 * wekit.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
define ( 'IN_LEAPS', true );
define ( 'BASE_PATH', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) . DIRECTORY_SEPARATOR );
define ( 'WEKIT_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
define ( 'APPS_PATH', WEKIT_PATH . 'apps' . DIRECTORY_SEPARATOR );
define ( 'DATA_PATH', WEKIT_PATH . 'data' . DIRECTORY_SEPARATOR );
defined ( 'IS_DEBUG' ) || define ( 'IS_DEBUG', 0 );

require WEKIT_PATH . '../framework/kernel.php';

/**
 *
 * @author "XuTongle"
 *
 */
class Wekit {
	public static function build_app_dir() {
		if (! is_dir ( APPS_PATH )) mkdir ( APPS_PATH, 0755, true );
		if (is_writeable ( WEKIT_PATH )) {
			$dirs = array (DATA_PATH,DATA_PATH . 'cache/',DATA_PATH . 'session/',DATA_PATH . 'logs/',WEKIT_PATH . 'apps/',WEKIT_PATH . 'model/',WEKIT_PATH . 'api/',WEKIT_PATH . 'command/',WEKIT_PATH . 'config/',WEKIT_PATH . 'languages/' );
			foreach ( $dirs as $dir ) {
				if (! is_dir ( $dir )) mkdir ( $dir, 0755, true );
			}
		} else {
			header ( 'Content-Type:text/html; charset=utf-8' );
			exit ( '项目目录不可写，目录无法自动生成！' );
		}
	}
}