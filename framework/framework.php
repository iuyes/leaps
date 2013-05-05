<?php
// Leaps Version
define ( 'LEAPS_VERSION', '1.2.0' );
// Leaps Release
define ( 'LEAPS_RELEASE', '20130331' );
define ( 'FW_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
class Core {
	public static $_imports = array ();
	public static $_classes = array ();
	private static $_namespace = array ();
	private static $_includePaths = array ();

	/**
	 * 初始化框架
	 */
	public static function init() {
		if (version_compare ( PHP_VERSION, '5.2.0', '<' )) die ( 'require PHP > 5.2.0 !' );
		if (version_compare ( PHP_VERSION, '5.4.0', '<' )) {
			@ini_set ( 'magic_quotes_runtime', 0 );
			define ( 'MAGIC_QUOTES_GPC', get_magic_quotes_gpc () ? true : false );
		} else {
			define ( 'MAGIC_QUOTES_GPC', false );
		}
		if (function_exists ( "set_time_limit" ) == true and @ini_get ( "safe_mode" ) == 0) {
			@set_time_limit ( 300 );
		}
		define ( 'IS_WIN', strstr ( PHP_OS, 'WIN' ) ? true : false );
		/* 开始时间 */
		define ( 'START_TIME', microtime ( true ) );
		/* 开始占用内存 */
		define ( 'MEMORY_LIMIT_ON', function_exists ( 'memory_get_usage' ) );
		if (MEMORY_LIMIT_ON) define ( 'START_MEMORY', memory_get_usage () );
		self::register ( FW_PATH, 'FW', true );
		spl_autoload_register ( array ('Core','autoload' ) );
	}

	/**
	 * 将路径信息注册到命名空间,该方法不会覆盖已经定义过的命名空间
	 *
	 * @param string $path 需要注册的路径
	 * @param string $alias 路径别名
	 * @param boolean $includePath | 是否同时定义includePath
	 * @param boolean $reset | 是否覆盖已经存在的定义，默认false
	 * @return void
	 * @throws Exception
	 */
	public static function register($path, $alias = '', $includePath = false, $reset = false) {
		if (! $path) return;
		if (! empty ( $alias )) {
			$alias = strtolower ( $alias );
			if (! isset ( self::$_namespace [$alias] ) || $reset) self::$_namespace [$alias] = rtrim ( $path, '\\/' ) . DIRECTORY_SEPARATOR;
		}
		if ($includePath) {
			if (empty ( self::$_includePaths )) {
				self::$_includePaths = array_unique ( explode ( PATH_SEPARATOR, get_include_path () ) );
				if (($pos = array_search ( '.', self::$_includePaths, true )) !== false) unset ( self::$_includePaths [$pos] );
			}
			array_unshift ( self::$_includePaths, $path );
			if (set_include_path ( '.' . PATH_SEPARATOR . implode ( PATH_SEPARATOR, self::$_includePaths ) ) === false) {
				throw new Exception ( '[Core.register] set include path error.' );
			}
		}
	}

	/**
	 * 返回命名空间的路径信息
	 *
	 * @param string $namespace
	 * @return string Ambigous multitype:>
	 */
	public static function getRootPath($namespace) {
		$namespace = strtolower ( $namespace );
		return isset ( self::$_namespace [$namespace] ) ? self::$_namespace [$namespace] : '';
	}

	/**
	 * 类文件自动加载方法 callback
	 *
	 * @param string $className
	 * @param string $path
	 * @return null
	 */
	public static function autoLoad($className, $path = '') {
		if ($path)
			include $path . '.php';
		elseif (isset ( self::$_classes [$className] )) {
			include self::$_classes [$className] . '.php';
		} elseif (strpos ( $className, '_' ) !== false) {
			$className = str_replace ( '_', '.', $className );
			self::import ( 'LIB:' . $className );
		} else
			include $className . '.php';
	}

	/**
	 * 区分大小写的文件存在判断
	 *
	 * @param string $filename 文件地址
	 * @return boolean
	 */
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
print_r ();