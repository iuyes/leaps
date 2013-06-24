<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-14
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: View.php 560 2013-05-17 07:01:02Z 85825770@qq.com $
 */
class View extends Base_View{

	/**
	 * 初始化模板引擎
	 */
	public function init(){
		$this->_ext = C ( 'template', 'ext' );
		$this->_referesh = C ( 'template', 'referesh' );
		$this->view_dir = WEKIT_PATH . 'template' . DIRECTORY_SEPARATOR;
		$this->compile_dir = DATA_PATH . 'template' . DIRECTORY_SEPARATOR;
	}

	/**
	 * 当前视图实例
	 *
	 * @var object
	 */
	protected static $instance = null;

	public static function &instance() {
		if (null === self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
}