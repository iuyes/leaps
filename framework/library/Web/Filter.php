<?php
/**
 * 过滤器
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Web_Filter {
	private $_allowtags = 'p|br|b|strong|hr|a|img|object|param|form|input|label|dl|dt|dd|div|font';
	private $_allowattrs = 'id|class|align|valign|src|border|href|target|width|height|title|alt|name|action|method|value|type';
	private $_disallowattrvals = 'expression|javascript:|behaviour:|vbscript:|mocha:|livescript:';

	public function __construct($allowtags = null, $allowattrs = null, $disallowattrvals = null) {
		if ($allowtags) $this->_allowtags = $allowtags;
		if ($allowattrs) $this->_allowattrs = $allowattrs;
		if ($disallowattrvals) $this->_disallowattrvals = $disallowattrvals;
	}

	public function input($cleanxss = 1) {
		if (get_magic_quotes_gpc ()) {
			$_POST = new_stripslashes ( $_POST );
			$_GET = new_stripslashes ( $_GET );
			$_COOKIE = new_stripslashes ( $_COOKIE );
			$_REQUEST = new_stripslashes ( $_REQUEST );
		}
		if (! defined ( 'IN_ADMIN' ) && $cleanxss) {
			$_POST = $this->xss ( $_POST );
			$_GET = $this->xss ( $_GET );
			$_COOKIE = $this->xss ( $_COOKIE );
			$_REQUEST = $this->xss ( $_REQUEST );
		}
	}

	public function xss($string) {
		if (is_array ( $string )) {
			$string = array_map ( array ($this,'xss' ), $string );
		} else {
			if (strlen ( $string ) > 20) {
				$string = $this->_strip_tags ( $string );
			}
		}
		return $string;
	}

	public function _strip_tags($string) {
		return preg_replace_callback ( "|(<)(/?)(\w+)([^>]*)(>)|", array ($this,'_strip_attrs' ), $string );
	}

	public function _strip_attrs($matches) {
		if (preg_match ( "/^(" . $this->_allowtags . ")$/", $matches [3] )) {
			if ($matches [4]) {
				preg_match_all ( "/\s(" . $this->_allowattrs . ")\s*=\s*(['\"]?)(.*?)\\2/i", $matches [4], $m, PREG_SET_ORDER );
				$matches [4] = '';
				foreach ( $m as $k => $v ) {
					if (! preg_match ( "/(" . $this->_disallowattrvals . ")/", $v [3] )) {
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