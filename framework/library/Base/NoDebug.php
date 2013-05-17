<?php
/**
 * 关闭Debug加载的类
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-14
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: NoDebug.php 540 2013-05-16 09:36:13Z 85825770@qq.com $
 */
class Base_NoDebug {

	public function __call($m, $v) {
		return $this;
	}

	public function log($i = null) {
		return $this;
	}

	public function info($i = null) {
		return $this;
	}

	public function error($i = null) {
		return $this;
	}

	public function group($i = null) {
		return $this;
	}

	public function groupEnd($i = null) {
		return $this;
	}

	public function table($Label = null, $Table = null) {
		return $this;
	}

	public function profiler($i = null) {
		return $this;
	}

	public function is_open() {
		return false;
	}
}