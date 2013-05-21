<?php
/**
 * 通用工具库
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: Utility.php 542 2013-05-16 09:37:32Z 85825770@qq.com $
 */
class Utility{
	/**
	 * 404页面
	 *
	 * @param string	页面
	 * @param bool	记录日志？
	 * @return string
	 */
	public static function show_404($page = '', $log_error = TRUE) {
		$heading = "404 Page Not Found";
		$message = "The page you requested was not found.";
		if ($log_error) {
			log_message ( 'error', '404 Page Not Found --> ' . $page );
		}
		echo self::show_error ( $heading, $message, 'error_404', 404 );
		exit ();
	}

	/**
	 * 一般错误页面
	 *
	 * @param string	the heading
	 * @param string	the message
	 * @param string	the template name
	 * @param int		the status code
	 * @return string
	 */
	public static function show_error($message, $status_code = 500, $template = 'error_general', $heading ='An Error Was Encountered') {
		set_status_header ( $status_code );
		$message = '<p>' . implode ( '</p><p>', (! is_array ( $message )) ? array ($message) : $message ) . '</p>';
		include (FW_PATH . 'errors/' . $template . '.php');
		die();
	}

	/**
	 * 错误输出
	 * @param mixed $error 错误
	 * @return void
	 */
	public static function halt($message, $file, $line, $trace, $status = 0){
		if (IS_DEBUG) {//调试模式下输出错误信息
			list($file_lines, $trace) = self::crash($file, $line, $trace);
			// 包含异常页面模板
			ob_start ();
			include (FW_PATH . 'errors/error_php.php');
			$buffer = ob_get_contents ();
			ob_end_clean ();
			die($buffer) ;
		} else {//否则定向到错误页面
			$error_page = C ( 'config', 'error_page' );
			if (! empty ( $error_page )) {//如果错误页面不为空就重定向到配置文件中设置的地址
				redirect($error_page);
			} else {
				if (C('config','show_error_msg')){
					$e['message'] = $message;
				} else {
					$e['message'] = C('config','error_message');
				}
			}
			self::show_error($e['message']);
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
			$trace_line = '#' . str_pad ( ($count - $key), $pad_len, "0", STR_PAD_LEFT ) . '  ' . '[' . $time . '] ' . self::get_call_line ( $call );
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
		return array($file_lines, $trace);
	}

	/**
	 * 获取调用行
	 * @param array $call
	 * @return string
	 */
	private static function get_call_line($call) {
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