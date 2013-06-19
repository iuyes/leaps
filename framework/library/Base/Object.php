<?php
/**
 * 对象基类
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Base_Object {
	protected $errno, $error;

	public function __construct() {
	}

	public function __get($name) {
		return isset ( $this->$name ) ? $this->$name : null;
	}

	public function __set($name, $value) {
		if ( property_exists($this,$name) ){
			$this->$name = $value;
		}
	}

	public function __isset($name) {
		return isset ( $this->$name );
	}

	public function __unset($name) {
		unset ( $this->$name );
	}

	public function __toString() {
		return get_class ( $this );
	}

	function errno() {
		return $this->errno;
	}

	function error() {
		return $this->error;
	}
}
