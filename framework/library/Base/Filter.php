<?php
/**
 * XSS过滤器
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id: Filter.php 558 2013-05-17 06:37:38Z 85825770@qq.com $
 */
class Base_Filter {

	private static $_allowtags = 'p|br|b|strong|hr|a|img|object|param|form|input|label|dl|dt|dd|div|font';
	private static $_allowattrs = 'id|class|align|valign|src|border|href|target|width|height|title|alt|name|action|method|value|type';
	private static $_disallowattrvals = 'expression|javascript:|behaviour:|vbscript:|mocha:|livescript:';

	public function __construct($allowtags = null, $allowattrs = null, $disallowattrvals = null) {
		if ($allowtags) self::$_allowtags = $allowtags;
		if ($allowattrs) self::$_allowattrs = $allowattrs;
		if ($disallowattrvals) self::$_disallowattrvals = $disallowattrvals;
	}

	public static function input($cleanxss = 1) {
		if (! defined ( 'IN_ADMIN' ) && $cleanxss) {
			$_POST = self::xss ( $_POST );
			$_GET = self::xss ( $_GET );
			$_COOKIE = self::xss ( $_COOKIE );
			$_REQUEST = self::xss ( $_REQUEST );
		}
	}

	public static function xss($string) {
		if (is_array ( $string )) {
			$string = array_map ( array ('self','xss' ), $string );
		} else {
			if (strlen ( $string ) > 20) {
				$string = self::_strip_tags ( $string );
			}
		}
		return $string;
	}

	public static function _strip_tags($string) {
		return preg_replace_callback ( "|(<)(/?)(\w+)([^>]*)(>)|", array ('self','_strip_attrs' ), $string );
	}

	public static function _strip_attrs($matches) {
		if (preg_match ( "/^(" . self::$_allowtags . ")$/", $matches [3] )) {
			if ($matches [4]) {
				preg_match_all ( "/\s(" . self::$_allowattrs . ")\s*=\s*(['\"]?)(.*?)\\2/i", $matches [4], $m, PREG_SET_ORDER );
				$matches [4] = '';
				foreach ( $m as $k => $v ) {
					if (! preg_match ( "/(" . self::$_disallowattrvals . ")/", $v [3] )) {
						$matches [4] .= $v [0];
					}
				}
			}
		} else {
			$matches [1] = '&lt;';
			$matches [5] = '&gt;';
		}
		unset ( $matches [0] );
		return implode ( '', $matches );
	}
}