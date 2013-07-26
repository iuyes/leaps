<?php
/**
 * 错误处理句柄
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Base_Error {

	/**
	 * 一般错误页面
	 *
	 * @param string	the heading
	 * @param string	the message
	 * @param string	the template name
	 * @param int		the status code
	 * @return string
	 */
	public static function show_error($message, $status_code = 500, $template = 'general', $heading = 'An Error Was Encountered') {
		set_status_header ( $status_code );
		$message = '<p>' . implode ( '</p><p>', (! is_array ( $message )) ? array ($message ) : $message ) . '</p>';
		include (FW_PATH . 'errors/' . $template . '.php');
		die ();
	}

	public static function halt($message, $file, $line, $trace, $error_code = 0) {
		if (IS_DEBUG) { // 调试模式下输出错误信息
			list ( $file_lines, $trace, $current_line ) = self::crash ( $file, $line, $trace );
			// 包含异常页面模板
			ob_start ();
			include (FW_PATH . 'errors/error.php');
			$buffer = ob_get_contents ();
			ob_end_clean ();
			die ( $buffer );
		} else {
			$error_page = C ( 'config', 'error_page' );
			if (! empty ( $error_page )) { // 如果错误页面不为空就重定向到配置文件中设置的地址
				redirect ( $error_page );
			} else {
				if (! C ( 'config', 'show_error_msg' )) {
					$message = C ( 'config', 'error_message' );
				}
			}
			self::show_error ( $message );
		}
	}

	/**
	 * 错误信息处理方法
	 *
	 * @param string $file
	 * @param string $line
	 * @param array $trace
	 */
	public static function crash($file, $line, $trace) {
		$count = count ( $trace );
		$pad_len = strlen ( $count );
		$time = date ( "y-m-d H:i:m" );
		foreach ( $trace as $key => $call ) {
			if (! isset ( $call ['file'] ) || $call ['file'] == '') {
				$call ['file'] = '~Internal Location~';
				$call ['line'] = 'N/A';
			}
			$trace_line = '#' . str_pad ( ($count - $key), $pad_len, "0", STR_PAD_LEFT ) . '  ' . '[' . $time . '] ' . self::_get_call_line ( $call );
			$trace [$key] = $trace_line;
		}
		$file_lines = array ();
		if (is_file ( $file )) {
			$current_line = $line - 1;
			$file_lines = explode ( "\n", file_get_contents ( $file, null, null, 0, 10000000 ) );
			$topLine = $current_line - 5;
			$file_lines = array_slice ( $file_lines, $topLine > 0 ? $topLine : 0, 10, true );
			if (($count = count ( $file_lines )) > 0) {
				$padLen = strlen ( $count );
				foreach ( $file_lines as $line => &$file_line )
					$fileLine = " " . htmlspecialchars ( str_pad ( $line + 1, $padLen, "0", STR_PAD_LEFT ) . ": " . str_replace ( "\t", "    ", rtrim ( $file_line ) ), null, "UTF-8" );
			}
		}
		return array ($file_lines,$trace,$current_line );
	}

	/**
	 * 返回友好的错误类型名
	 *
	 * @param int $type
	 * @return string 错误类型名
	 */
	public static function get_name($error_number) {
		$error_map = array (
				E_ERROR => "E_ERROR",
				E_WARNING => "E_WARNING",
				E_PARSE => "E_PARSE",
				E_NOTICE => "E_NOTICE ",
				E_CORE_ERROR => "E_CORE_ERROR",
				E_CORE_WARNING => "E_CORE_WARNING",
				E_COMPILE_ERROR => "E_COMPILE_ERROR",
				E_COMPILE_WARNING => "E_COMPILE_WARNING",
				E_USER_ERROR => "E_USER_ERROR",
				E_USER_WARNING => "E_USER_WARNING",
				E_USER_NOTICE => "E_USER_NOTICE",
				E_STRICT => "E_STRICT",
				E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
				E_ALL => "E_ALL"
		);
		return @isset ( $error_map [$error_number] ) ? $error_map [$error_number] : 'E_UNKNOWN';
	}

	/**
	 * 获取调用行
	 *
	 * @param array $call
	 * @return string
	 */
	private static function _get_call_line($call) {
		$call_signature = "";
		if (isset ( $call ['file'] )) $call_signature .= $call ['file'] . " ";
		if (isset ( $call ['line'] )) $call_signature .= "(" . $call ['line'] . ") ";
		if (isset ( $call ['function'] )) {
			if (isset ( $call ['class'] )) $call_signature .= $call ['class'];
			if (isset ( $call ['type'] )) $call_signature .= $call ['type'];
			$call_signature .= $call ['function'] . "(";
			if (isset ( $call ['args'] )) {
				foreach ( $call ['args'] as $arg ) {
					if (is_string ( $arg ))
						$arg = '"' . (strlen ( $arg ) <= 64 ? $arg : substr ( $arg, 0, 64 ) . "…") . '"';
					else if (is_object ( $arg ))
						$arg = "[Instance of '" . get_class ( $arg ) . "']";
					else if ($arg === true)
						$arg = "true";
					else if ($arg === false)
						$arg = "false";
					else if ($arg === null)
						$arg = "null";
					else
						$arg = strval ( $arg );
					$call_signature .= $arg . ',';
				}
			}
			$call_signature = trim ( $call_signature, ',' ) . ")";
		}
		return $call_signature;
	}
}