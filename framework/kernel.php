<?php
/**
 * 核心入口文件
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
/**
 * Gets the framework version.
 */
define ( 'LEAPS_VERSION', '2.0.0' );
/**
 * Gets the framework release.
 */
define ( 'LEAPS_RELEASE', '20130531S' );
/**
 * Gets the application start timestamp.
 */
defined ( 'START_TIME' ) or define ( 'START_TIME', microtime ( true ) );
/**
 * Defines the Leaps framework installation path.
 */
defined ( 'FW_PATH' ) or define ( 'FW_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
/**
 * This constant defines whether the application should be in debug mode or not.
 * Defaults to false.
 */
defined ( 'IS_DEBUG' ) or define ( 'IS_DEBUG', false );
/**
 * This constant defines PHP file suffix.
 */
define( 'EXT', '.php' );
/**
 * This constant defines line break.
 */
define( 'CRLF', "\r\n" );
/**
 * This constant defines directory separator.
 */
define( 'DS', DIRECTORY_SEPARATOR );
/**
 * This constant defines whether the application should be in cig mode or not.
 */
define ( 'IS_CGI', substr ( PHP_SAPI, 0, 3 ) == 'cgi' ? true : false );
/**
 * This constant defines whether the application should be in Windows system or
 * not.
 */
define ( 'IS_WIN', strstr ( PHP_OS, 'WIN' ) ? true : false );
/**
 * This constant defines whether the application should be in command mode or not.
 */
define ( 'IS_CLI', PHP_SAPI == 'cli' ? true : false );
/**
 * This constant defines whether the application parameters transferred meaning or not,
 * Recommended system shut down automatically escape function.
 */
define ( 'MAGIC_QUOTES_GPC', function_exists ( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc () );
/**
 * 核心类
 *
 * @author "XuTongle"
 */
class Core {
	public static $_imports = array ();

	/**
	 * 创建应用程序
	 *
	 * @param string $type
	 * @param array 应用程序配置
	 */
	public static function application($type = 'Web', $config = array()) {
		$_className = $type . '_Application';
		return new $_className ( $config );
	}

	/**
	 * 基于给定的配置创建一个对象并初始化
	 *
	 * @param mixed $config 配置信息可以是字符串或数组
	 * @return mixed 创建的对象
	 * @throws Base_Exception if the configuration does not have a 'class'
	 *         element.
	 */
	public static function component($config) {
		if (is_string ( $config )) {
			$type = $config;
			$config = array ();
		} elseif (isset ( $config ['class'] )) {
			$type = $config ['class'];
			unset ( $config ['class'] );
		} else
			throw new Base_Exception ( 'Object configuration must be an array containing a "class" element.' );

		if (! class_exists ( $type, false )) $type = self::import ( $type, true );

		if (($n = func_num_args ()) > 1) {
			$args = func_get_args ();
			if ($n === 2)
				$object = new $type ( $args [1] );
			elseif ($n === 3)
				$object = new $type ( $args [1], $args [2] );
			elseif ($n === 4)
				$object = new $type ( $args [1], $args [2], $args [3] );
			else {
				unset ( $args [0] );
				$class = new ReflectionClass ( $type );
				$object = call_user_func_array ( array ($class,'newInstance' ), $args );
			}
		} else
			$object = new $type ();

		foreach ( $config as $key => $value )
			$object->$key = $value;

		return $object;
	}

	/**
	 * 核心初始化
	 */
	public static function init() {
		if (version_compare ( PHP_VERSION, '5.2.0', '<' )) die ( 'require PHP > 5.2.0 !' );
		spl_autoload_register ( array ('Core','autoload' ) );
		if (! defined ( 'CORE_FUNCTION' ) && ! @include (FW_PATH . 'func.php')) exit ( 'func.php is missing' );
		if (! defined ( 'CUSTOM_FUNCTION' ) && ! @include (FW_PATH . 'custom.php')) exit ( 'custom.php is missing' );
	}

	/**
	 * Class autoload loader.
	 * This method is provided to be invoked within an __autoload() magic
	 * method.
	 *
	 * @param string $className class name
	 * @return boolean whether the class has been loaded successfully
	 */
	public static function autoload($className) {
		if (strpos ( $className, '_' ) !== false) $className = str_replace ( '_', '.', $className );
		self::import ( $className, FW_PATH . 'library' . DIRECTORY_SEPARATOR );
		return class_exists ( $className, false ) || interface_exists ( $className, false );
	}

	/**
	 * 载入文件
	 *
	 * @param string $filePath 导入
	 * @param string $folder 从指定文件夹导入
	 * @throws Base_Exception
	 * @return boolean
	 */
	public static function import($filePath, $base = null) {
		if (! isset ( self::$_imports [$filePath] )) {
			$fileName = $filePath;
			if (($pos = strrpos ( $filePath, ':' )) !== false) { // 如果是：分割则导入应用下的文件
				$base = APPS_PATH . substr ( $filePath, 0, $pos ) . DIRECTORY_SEPARATOR;
				$fileName = substr ( $filePath, $pos + 1 );
			}
			if (strpos ( $fileName, '.' ) !== false) {
				$fileName = str_replace ( '.', DIRECTORY_SEPARATOR, $fileName );
			}
			$path = (! is_null ( $base ) ? $base : FW_PATH) . $fileName . '.php';
			if (is_file ( $path ) && self::_file_exists ( $path )) {
				self::$_imports [$filePath] = include $path;
			} else {
				throw new Base_Exception ( 'Unable to load the file ' . $path . ' , file is not exist.' );
			}
		}
		return self::$_imports [$filePath];
	}

	private static function _file_exists($filename) {
		if (is_file ( $filename )) {
			if (IS_WIN) {
				if (basename ( realpath ( $filename ) ) != basename ( $filename )) return false;
			}
			return true;
		}
		return false;
	}
}
Core::init ();