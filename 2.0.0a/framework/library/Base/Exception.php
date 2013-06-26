<?php
/**
 * 异常基类
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Base_Exception extends Exception {
	/**
	 * 架构函数
	 *
	 * @param string $message 异常信息
	 */
	public function __construct($message, $code = 0) {
		parent::__construct ( $message, $code );
	}
}