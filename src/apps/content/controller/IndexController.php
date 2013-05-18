<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-26
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: IndexController.php 6 2013-01-16 03:57:50Z xutongle $
 */
class IndexController extends Web_Controller {

	public function __construct() {
		parent::__construct ();
	}

	/**
	 * 首页
	 */
	public function init() {
		echo 444;
		include V ( 'index' );
	}
}