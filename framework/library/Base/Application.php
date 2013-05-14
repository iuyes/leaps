<?php
/**
 * 应用程序创建基类
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
abstract class Base_Application {

	/**
	 * 构造方法 初始化共通
	 */
	public function __construct() {
		if (IS_DEBUG)
			error_reporting ( E_ALL );
		else
			error_reporting ( 0 );
		set_error_handler(array($this, '_errorHandle'), error_reporting());
		set_exception_handler(array($this, '_exceptionHandle'));
		/* 开始时间 */
		define ( 'START_TIME', microtime ( true ) );
		/* 开始占用内存 */
		define ( 'MEMORY_LIMIT_ON', function_exists ( 'memory_get_usage' ) );
		if (version_compare ( PHP_VERSION, '5.4.0', '<' )) {
			@ini_set ( 'magic_quotes_runtime', 0 );
			define ( 'MAGIC_QUOTES_GPC', get_magic_quotes_gpc () ? true : false );
		} else {
			define ( 'MAGIC_QUOTES_GPC', false );
		}
		if (MEMORY_LIMIT_ON) define ( 'START_MEMORY', memory_get_usage () );
		if (function_exists ( "set_time_limit" ) == true and @ini_get ( "safe_mode" ) == 0) {
			@set_time_limit ( 300 );
		}
		if (function_exists ( 'ini_get' )) {
			$memorylimit = @ini_get ( 'memory_limit' );
			if ($memorylimit && format_byte ( $memorylimit ) < 33554432 && function_exists ( 'ini_set' )) @ini_set ( 'memory_limit', '128m' );
		}
		if (function_exists ( 'date_default_timezone_set' )) {
			@date_default_timezone_set ( C ( 'config', 'timezone', 'Etc/GMT-8' ) ); // 默认Etc/GMT-8
		}
		define ( 'TIME', time () );
		define ( 'CHARSET', C ( 'config', 'charset', 'UTF-8' ) );
		$this->_initialize();

	}

	/**
	 * 创建并初始化应用配置
	 *
	 * @return Web_Application
	 */
	abstract protected function execute();

	/**
	 * 初始化应用相关
	*/
	abstract public function _initialize();


	/**
	 * 运行应用程序
	*/
	public function run(){
		$this->execute();
		restore_error_handler();
		restore_exception_handler();
	}

	/**
	 * 异常处理句柄
	 *
	 * @param Exception $exception
	 */
	public function _exceptionHandle($exception) {
		restore_error_handler();
		restore_exception_handler();
		$trace = $exception->getTrace();
		if (@$trace[0]['file'] == '') {
			unset($trace[0]);
			$trace = array_values($trace);
		}
		$file = @$trace[0]['file'];
		$line = @$trace[0]['line'];
		$this->showErrorMessage($exception->getMessage(), $file, $line, $trace, $exception->getCode());
	}

	/**
	 * 错误处理句柄
	 *
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public function _errorHandle($errno, $errstr, $errfile, $errline) {
		restore_error_handler();
		restore_exception_handler();
		$trace = debug_backtrace();
		unset($trace[0]["function"], $trace[0]["args"]);
		$this->showErrorMessage($this->_get_errorname($errno) . ': ' . $errstr, $errfile, $errline, $trace, $errno);
	}

	/**
	 * 错误处理
	 *
	 * @param string $message
	 * @param string $file 异常文件
	 * @param int $line 错误发生的行
	 * @param array $trace
	 * @param int $errorcode 错误代码
	 * @throws WindFinalException
	 */
	protected function showErrorMessage($message, $file, $line, $trace, $errorcode) {
		if (IS_DEBUG) {
			$log = $message . "\r\n" . $file . ":" . $line . "\r\n";
			list($fileLines, $trace) = Utility::crash($file, $line, $trace);
			foreach ($trace as $key => $value) {
				$log .= $value . "\r\n";
			}
			Core::debug ()->error ( $message . ' in ' . $file . ' on line ' . $line );
			log_message ( 'error', $log, TRUE );
			// 包含异常页面模板
			ob_start ();
			include (FW_PATH . 'errors/error_php.php');
			$buffer = ob_get_contents ();
			ob_end_clean ();
			die($buffer) ;
		}
	}

	/**
	 * 返回友好的错误类型名
	 *
	 * @param int $type
	 * @return string|unknown
	 */
	private function _get_errorname($type) {
		switch ($type) {
			case E_ERROR:
				return 'E_ERROR';
			case E_WARNING:
				return 'E_WARNING';
			case E_PARSE:
				return 'E_PARSE';
			case E_NOTICE:
				return 'E_NOTICE';
			case E_CORE_ERROR:
				return 'E_CORE_ERROR';
			case E_CORE_WARNING:
				return 'E_CORE_WARNING';
			case E_CORE_ERROR:
				return 'E_COMPILE_ERROR';
			case E_CORE_WARNING:
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR:
				return 'E_USER_ERROR';
			case E_USER_WARNING:
				return 'E_USER_WARNING';
			case E_USER_NOTICE:
				return 'E_USER_NOTICE';
			case E_STRICT:
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR:
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED:
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED:
				return 'E_USER_DEPRECATED';
		}
		return $type;
	}
}