<?php
/**
 * 通用工具库
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Utility{
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