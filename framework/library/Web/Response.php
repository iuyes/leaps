<?php
/**
 * Response.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Web_Response {
	/**
	 * 设置相应状态码
	 *
	 * @var string
	 */
	private static $_status = '';

	/**
	 * 是否直接跳转
	 *
	 * @var boolean
	 */
	private static $_is_redirect = false;

	/**
	 * 设置输出的头部信息
	 *
	 * @var array
	 */
	private static $_headers = array ();

	public static function send_headers() {
		if (self::is_sended_header ()) return;
		foreach ( self::$_headers as $header ) {
			header ( $header ['name'] . ': ' . $header ['value'], $header ['replace'] );
		}
		if (self::$_status) {
			header ( 'HTTP/1.x ' . self::$_status . ' ' . ucwords ( self::code_map ( self::$_status ) ) );
			header ( 'Status: ' . self::$_status . ' ' . ucwords ( self::code_map ( self::$_status ) ) );
		}
	}

	/**
	 * 重定向一个响应信息
	 *
	 * @param string $location 重定向的地址
	 * @param int $status 状态码,默认为302
	 * @return void
	 */
	public static function send_redirect($location, $status = 302) {
		if (! is_int ( $status ) || $status < 300 || $status > 399) return;
		self::add_header ( 'Location', $location, true );
		self::set_status ( $status );
		self::$_is_redirect = true;
		self::send_headers ();
		exit ();
	}

	/**
	 * 设置响应头信息，如果已经设置过同名的响应头，该方法将用新的设置取代原来的头字段
	 *
	 * @param string $name 响应头的名称
	 * @param string $value 响应头的字段取值
	 * @param int $replace 响应头信息的replace项值
	 * @return void
	 */
	public static function set_header($name, $value, $replace = false) {
		if (! $name || ! $value) return;
		$name = self::_normalizeHeader ( $name );
		$setted = false;
		foreach ( self::$_headers as $key => $one ) {
			if ($one ['name'] == $name) {
				self::$_headers [$key] = array ('name' => $name,'value' => $value,'replace' => $replace );
				$setted = true;
				break;
			}
		}
		if ($setted === false) self::$_headers [] = array ('name' => $name,'value' => $value,'replace' => $replace );
	}

	/**
	 * 设置响应头信息，如果已经设置过同名的响应头，该方法将增加一个同名的响应头
	 *
	 * @param string $name 响应头的名称
	 * @param string $value 响应头的字段取值
	 * @param int $replace 响应头信息的replace项值
	 * @return void
	 */
	public static function add_header($name, $value, $replace = false) {
		if ($name == '' || $value == '') return;
		$name = self::_normalizeHeader ( $name );
		self::$_headers [] = array ('name' => $name,'value' => $value,'replace' => $replace );
	}

	/**
	 * 设置响应头状态码
	 *
	 * @param int $status 响应状态码
	 * @param string $message 相应状态信息,默认为空字串
	 * @return void
	 */
	public static function set_status($status, $message = '') {
		$status = intval ( $status );
		if ($status < 100 || $status > 505) return;
		self::$_status = ( int ) $status;
	}

	/**
	 * 获取响应头信息
	 *
	 * @return array
	 */
	public static function get_headers() {
		return self::$_headers;
	}

	/**
	 * 清除响应头信息
	 *
	 * @return void
	 */
	public static function clear_headers() {
		self::$_headers = array ();
	}

	/**
	 * 是否已经发送了响应头部
	 *
	 * @param boolean $throw 是否抛出错误,默认为false：
	 *        <ul>
	 *        <li>true: 如果已经发送了头部则抛出异常信息</li>
	 *        <li>false: 无论如何都不抛出异常信息</li>
	 *        </ul>
	 * @return boolean 已经发送头部信息则返回true否则返回false
	 */
	public static function is_sended_header($throw = false) {
		$sended = headers_sent ( $file, $line );
		if ($throw && $sended) throw new Base_Exception ( '[web.Response.is_sended_header] the headers are sent in file ' . $file . ' on line ' . $line );
		return $sended;
	}

	/**
	 * 响应代码信息
	 *
	 * @param int $code
	 * @return string
	 */
	public static function code_map($code) {
		$maps = array (505 => 'http version not supported',504 => 'gateway timeout',503 => 'service unavailable',503 => 'bad gateway',502 => 'bad gateway',501 => 'not implemented',500 => 'internal server error',417 => 'expectation failed',416 => 'requested range not satisfiable',415 => 'unsupported media type',414 => 'request uri too long',413 => 'request entity too large',412 => 'precondition failed',411 => 'length required',410 => 'gone',409 => 'conflict',408 => 'request timeout',407 => 'proxy authentication required',406 => 'not acceptable',405 => 'method not allowed',404 => 'not found',403 => 'forbidden',402 => 'payment required',401 => 'unauthorized',400 => 'bad request',300 => 'multiple choices',301 => 'moved permanently',302 => 'moved temporarily',302 => 'found',303 => 'see other',304 => 'not modified',305 => 'use proxy',307 => 'temporary redirect',100 => 'continue',101 => 'witching protocols',200 => 'ok',201 => 'created',202 => 'accepted',203 => 'non authoritative information',204 => 'no content',205 => 'reset content',206 => 'partial content' );
		return isset ( $maps [$code] ) ? $maps [$code] : '';
	}

	/**
	 * 格式化响应头信息
	 *
	 * @param string $name 响应头部名字
	 * @return string
	 */
	private static function _normalizeHeader($name) {
		$filtered = str_replace ( array ('-','_' ), ' ', ( string ) $name );
		$filtered = ucwords ( strtolower ( $filtered ) );
		$filtered = str_replace ( ' ', '-', $filtered );
		return $filtered;
	}
}