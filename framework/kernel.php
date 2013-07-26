<?php
/**
 * 框架入口
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
if (PHP_VERSION < '5.2.0') die ( 'require PHP > 5.2.0 !' );
if (PHP_VERSION < '5.3.0') set_magic_quotes_runtime ( 0 );
/**
 * Gets the framework entrance.
 */
define ( 'IN_LEAPS', true );
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
defined ( 'IS_DEBUG' ) || define ( 'IS_DEBUG', false );
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
 * This constant defines whether the application should be in command mode or
 * not.
 */
define ( 'IS_CLI', PHP_SAPI == 'cli' ? true : false );

/**
 * This constant defines whether the application parameters transferred meaning
 * or not,
 * Recommended system shut down automatically escape function.
 */
define ( 'MAGIC_QUOTES_GPC', function_exists ( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc () );
define ( 'MEMORY_LIMIT_ON', function_exists ( 'memory_get_usage' ) );
if (MEMORY_LIMIT_ON) define ( 'START_MEMORY', memory_get_usage () );
if (function_exists ( "set_time_limit" ) == true and @ini_get ( "safe_mode" ) == 0) {
	@set_time_limit ( 300 );
}
/**
 * 核心类
 *
 * @author "XuTongle"
 *
 */
class Core {
	public static $Debug = null;
	public static $_imports = array ();
	private static $_extensions = '.php';
	private static $_namespace = array ();
	private static $_includePaths = array ();
	private static $instances = array ();
	private static $_frontController = null;

	/**
	 * 创建应用程序
	 *
	 * @param string $type
	 */
	public static function application($type = 'Web', $config = array()) {
		if (self::$_frontController === null) {
			$_className = $type . '_Application';
			self::$_frontController = new $_className ( $config );
		}
		return self::$_frontController;
	}

	/**
	 * 框架初始化
	 */
	public static function init() {
		spl_autoload_register ( 'Core::autoLoad' );
		if (! defined ( 'CORE_FUNCTION' ) && ! @include (FW_PATH . 'func.php')) exit ( 'func.php is missing' );
		if (! defined ( 'CUSTOM_FUNCTION' ) && ! @include (FW_PATH . 'custom.php')) exit ( 'custom.php is missing' );
		if (! IS_DEBUG)
			error_reporting ( 0 ); // 线上
		else
			error_reporting ( E_ALL );
		set_error_handler ( 'Core::_errorHandle' , error_reporting () );
		set_exception_handler ( 'Core::_exceptionHandle' );
		register_shutdown_function ('Core::_shutdownHandle' );
		if (function_exists ( 'date_default_timezone_set' )) {
			@date_default_timezone_set ( C ( 'config', 'timezone', 'Etc/GMT-8' ) );
		}
		define ( 'TIME', time () );
		define ( 'IP', Base_Request::get_client_ip () );
		define ( 'CHARSET', C ( 'config', 'charset', 'UTF-8' ) );
		define ( 'PHP_FILE', Base_Request::get_script_url () );
		define ( 'SCRIPT_NAME', basename ( PHP_FILE ) );
		define ( 'WEB_PATH', substr ( PHP_FILE, 0, strrpos ( PHP_FILE, '/' ) ) . '/' );
		mb_internal_encoding ( CHARSET );
		if (MAGIC_QUOTES_GPC) {
			$_POST = String::stripslashes ( $_POST );
			$_GET = String::stripslashes ( $_GET );
			$_COOKIE = String::stripslashes ( $_COOKIE );
			$_REQUEST = String::stripslashes ( $_REQUEST );
		}
		if (isset ( $_GET ['page'] )) {
			$_GET ['page'] = max ( intval ( $_GET ['page'] ), 1 );
		}
		if(!IS_CLI){
			/* 协议 */
			define ( 'SITE_PROTOCOL', Base_Request::get_scheme () . '://' );
			/* 主机名 */
			define ( 'SITE_HOST', Base_Request::get_server_name () );
			/* 基础URL */
			define ( 'SITE_URL', htmlspecialchars ( Base_Request::get_base_url ( true ) ) . '/' );
			/* 设置来源 */
			define ( 'HTTP_REFERER', Base_Request::get_url_referer () );
			define ( 'REQUEST_METHOD', Base_Request::get_request_method () );
			define ( 'IS_GET', Base_Request::is_get () );
			define ( 'IS_POST', Base_Request::is_post () );
			define ( 'IS_PUT', Base_Request::is_put () );
			define ( 'IS_DELETE', Base_Request::is_delete () );
			define ( 'IS_AJAX', Base_Request::is_ajax () );
			@header ( 'Content-Type: text/html; charset=' . CHARSET );
			@header ( 'X-Powered-By: PHP/' . PHP_VERSION . ' Leaps/' . LEAPS_VERSION );
			// 页面压缩输出支持
			if (C ( 'config', 'gzip', true ) && function_exists ( 'ob_gzhandler' )) {
				ob_start ( 'ob_gzhandler' );
			} else {
				ob_start ();
			}
		}
	}

	/**
	 * 类文件自动加载方法 callback
	 *
	 * @param string $className
	 * @param string $path
	 * @return null
	 */
	public static function autoLoad($className, $path = '') {
		if (! empty ( $path )) { // 手动加载
			if (self::_file_exists ( $path )) {
				include $path;
			} else {
				throw new Exception ( 'Unable to load the file ' . $path . ' , file is not exist.' );
			}
		} else {
			$_classPath = self::getRealPath ( str_replace ( '_', '.', $className ), FW_PATH . 'library' . DIRECTORY_SEPARATOR );
			self::autoLoad ( $className, $_classPath );
		}
	}

	/**
	 * 载入文件
	 *
	 * @param string $name 文件名或带路径的文件名
	 * @param string $folder 文件夹默认为空
	 * @throws Exception
	 * @return boolean
	 */
	public static function import($filePath, $folder = '') {
		if (! $filePath) return;
		if (isset ( self::$_imports [$filePath] )) return self::$_imports [$filePath];
		if (($pos = strrpos ( $filePath, '.' )) !== false)
			$fileName = substr ( $filePath, $pos + 1 );
		elseif (($pos = strrpos ( $filePath, ':' )) !== false)
			$fileName = substr ( $filePath, $pos + 1 );
		else
			$fileName = $filePath;
		self::$_imports [$filePath] = $fileName;
		$_classPath = self::getRealPath ( $filePath, $folder );
		self::autoLoad ( $fileName, $_classPath );
		return $fileName;
	}

	/**
	 * 获取类实例
	 *
	 * @param string $className 类名
	 * @param array $args 参数
	 * @throws Base_Exception
	 * @return unknown mixed
	 */
	public static function get_instance($className, $args = array()) {
		try {
			$key = empty ( $args ) ? $className : $className . to_guid_string ( $args );
			if (empty ( $args )) {
				self::$instances [$key] = new $className ();
			} else {
				$reflection = new ReflectionClass ( $className );
				self::$instances [$key] = call_user_func_array ( array ($reflection,'newInstance' ), ( array ) $args );
			}
			return self::$instances [$key];
		} catch ( Exception $e ) {
			throw new Base_Exception ( '[Core.get_instance] create instance \'' . $className . '\' fail.' . $e->getMessage (), Base_Exception::ERROR_CLASS_NOT_EXIST );
		}
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 *
	 * @param string $filePath 路径信息
	 * @param boolean $base 基路径
	 * @return string $filePath
	 */
	public static function getRealPath($filePath, $base = '') {
		if (false !== strpos ( $filePath, DIRECTORY_SEPARATOR )) return realpath ( $filePath );
		if (false !== ($pos = strpos ( $filePath, ':' ))) {
			$namespace = ! empty ( $base ) ? $base : APPS_PATH . substr ( $filePath, 0, $pos ) . DIRECTORY_SEPARATOR;
			$filePath = substr ( $filePath, $pos + 1 );
		} else
			$namespace = ! empty ( $base ) ? $base : FW_PATH;

		$filePath = str_replace ( '.', DIRECTORY_SEPARATOR, $filePath );
		$namespace && $filePath = $namespace . $filePath;
		return $filePath . self::$_extensions;
	}

	/**
	 * 获取debug对象
	 * 可安全用于生产环境，在生产环境下将忽略所有debug信息
	 *
	 * @return Debug
	 */
	public static function debug() {
		if (null === self::$Debug) {
			if (! IS_CLI && IS_DEBUG && class_exists ( 'Base_Debug', true )) {
				self::$Debug = Base_Debug::instance ();
			} else {
				self::$Debug = new Base_NoDebug ();
			}
		}
		return self::$Debug;
	}

	/**
	 * 异常处理句柄
	 *
	 * @param Exception $exception 异常句柄
	 */
	public static function _exceptionHandle($exception) {
		restore_error_handler ();
		restore_exception_handler ();
		$trace = $exception->getTrace ();
		if (@$trace [0] ['file'] == '') {
			unset ( $trace [0] );
			$trace = array_values ( $trace );
		}
		$file = @$trace [0] ['file'];
		$line = @$trace [0] ['line'];
		if (IS_CLI) {
			printf ( Base_Error::get_name ( $exception ) . ': ' . $exception->getMessage () . ' in ' . $file . ' on line ' . $line );
		} else {
			if (C ( 'config', 'firephp', false ) && class_exists ( 'FB' )) { // FirePHP调试
				fb ( $exception );
			}
			if (! IS_AJAX) { // Ajax请求不显示错误页面
				Base_Error::halt ($exception->getMessage (), $file, $line, $trace, $exception->getCode () );
			}
		}
	}

	/**
	 * 自定义错误处理
	 *
	 * @param int $errno 错误类型
	 * @param string $errstr 错误信息
	 * @param string $errfile 错误文件
	 * @param int $errline 错误行数
	 * @return void
	 */
	public static function _errorHandle($errno, $errstr, $errfile, $errline) {
		//一直报 Fatal error: Exception thrown without a stack frame in Unknown on line 0 百思不得其解
		//throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		if (0 == error_reporting() || $errno == E_STRICT) return;
		//记录日志
		log_message ( 'error', 'Severity: ' . Base_Error::get_name ( $errno ) . '  --> ' . $errstr . ' ' . $errfile . ' ' . $errline, TRUE );
		restore_error_handler ();
		restore_exception_handler ();
		$trace = debug_backtrace ();
		unset ( $trace [0] ["function"], $trace [0] ["args"] );
		if (IS_CLI) {
			printf ( Base_Error::get_name ( $errno ) . ': ' . $errstr . ' in ' . $errfile . ' on line ' . $errline );
		} else {
			Core::debug ()->error ( Base_Error::get_name ( $errno ) . ': ' . $errstr . ' in ' . $errfile . ' on line ' . $errline );
			if (! IS_AJAX) { // Ajax请求不显示错误页面
				Base_Error::halt ( Base_Error::get_name ( $errno ) . ':' . $errstr, $errfile, $errline, $trace );
			}
		}
	}

	/**
	 * 致命错误捕捉
	 */
	public static function _shutdownHandle() {
		if (($errno = error_get_last ()) && $errno ['type']) {
			Core::_errorHandle ( $errno['type'], $errno ['message'], $errno ['file'], $errno ['line'] );
		}
	}

	/**
	 * 区分大小写的文件存在判断
	 *
	 * @param string $filename 文件地址
	 * @return boolean
	 */
	public static function _file_exists($filename) {
		if (is_file ( $filename )) {
			if (IS_WIN) {
				if (basename ( realpath ( $filename ) ) != basename ( $filename )) return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Generates a version string based on the variables defined above.
	 *
	 * @return string
	 */
	public static function version() {
		return 'Leaps Framework ' . LEAPS_VERSION . ' (' . LEAPS_RELEASE . ')';
	}
}
Core::init ();