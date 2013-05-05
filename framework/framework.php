<?php
// Leaps Version
define ( 'LEAPS_VERSION', '1.2.0' );
// Leaps Release
define ( 'LEAPS_RELEASE', '20130331' );
define ( 'FW_PATH', dirname ( __FILE__ ) );
class Core {
	public static $_imports = array ();
	public static $_classes = array ();
	private static $_namespace = array ();
	private static $_includePaths = array ();

	/**
	 * 加载一个类或者加载一个包
	 * 如果加载的包中有子文件夹不进行循环加载
	 * 参数格式说明：'WIND:base.WFrontController'
	 * WIND 注册的应用名称，应用名称与路径信息用‘:’号分隔
	 * base.WFrontController 相对的路径信息
	 * 如果不填写应用名称 ，例如‘base.WFrontController’，那么加载路径则相对于默认的应用路径
	 * 加载一个类的参数方式：'WIND:base.WFrontController'
	 * 加载一个包的参数方式：'WIND:base.*'
	 *
	 * @param string $filePath | 文件路径信息 或者className
	 * @return string null
	 */
	public static function import($filePath) {
		if (! $filePath) return;
		if (isset ( self::$_imports [$filePath] )) return self::$_imports [$filePath];
		if (($pos = strrpos ( $filePath, '.' )) !== false)
			$fileName = substr ( $filePath, $pos + 1 );
		elseif (($pos = strrpos ( $filePath, ':' )) !== false)
			$fileName = substr ( $filePath, $pos + 1 );
		else
			$fileName = $filePath;
		$isPackage = $fileName === '*';
		if ($isPackage) {
			$filePath = substr ( $filePath, 0, $pos + 1 );
			$dirPath = self::getRealPath ( trim ( $filePath, '.' ), false );
			self::register ( $dirPath, '', true );
		} else
			self::_setImport ( $fileName, $filePath );
		return $fileName;
	}

	/**
	 * 将路径信息注册到命名空间,该方法不会覆盖已经定义过的命名空间
	 *
	 * @param string $path 需要注册的路径
	 * @param string $name 路径别名
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
	 * 解析路径信息，并返回路径的详情
	 *
	 * @param string $filePath 路径信息
	 * @param boolean $suffix 是否存在文件后缀true，false，default
	 * @return string array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealPath($filePath, $suffix = '', $absolut = false) {
		if (false !== strpos ( $filePath, DIRECTORY_SEPARATOR )) return realpath ( $filePath );
		if (false !== ($pos = strpos ( $filePath, ':' ))) {
			$namespace = self::getRootPath ( substr ( $filePath, 0, $pos ) );
			$filePath = substr ( $filePath, $pos + 1 );
		} else
			$namespace = $absolut ? self::getRootPath ( self::getAppName () ) : '';

		$filePath = str_replace ( '.', '/', $filePath );
		$namespace && $filePath = $namespace . $filePath;
		if ($suffix === '') return $filePath . '.' . self::$_extensions;
		if ($suffix === true && false !== ($pos = strrpos ( $filePath, '/' ))) {
			$filePath [$pos] = '.';
			return $filePath;
		}
		return $suffix ? $filePath . '.' . $suffix : $filePath;
	}

	/**
	 * 解析路径信息，并返回路径的详情
	 *
	 * @param string $filePath 路径信息
	 * @param boolean $absolut 是否返回绝对路径
	 * @return string array('isPackage','fileName','extension','realPath')
	 */
	public static function getRealDir($dirPath, $absolut = false) {
		if (false !== ($pos = strpos ( $dirPath, ':' ))) {
			$namespace = self::getRootPath ( substr ( $dirPath, 0, $pos ) );
			$dirPath = substr ( $dirPath, $pos + 1 );
		} else
			$namespace = $absolut ? self::getRootPath ( self::getAppName () ) : '';

		return ($namespace ? $namespace : '') . str_replace ( '.', '/', $dirPath );
	}

	/**
	 * 初始化框架
	 */
	public static function init() {
		self::register ( FW_PATH, 'FW', true );
		spl_autoload_register ( array ('Core','autoload' ) );
	}

	/**
	 *
	 * @param string $className
	 * @param string $classPath
	 * @return void
	 */
	private static function _setImport($className, $classPath) {
		self::$_imports [$classPath] = $className;
		if (! isset ( self::$_classes [$className] )) {
			$_classPath = self::getRealPath ( $classPath, false );
			self::$_classes [$className] = $_classPath;
		} else
			$_classPath = self::$_classes [$className];
		include $_classPath . '.php';
	}
}
Core::init ();
print_r();