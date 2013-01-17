<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-10-8
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: Template.php 2 2013-01-14 07:14:05Z xutongle $
 */
class template extends View{

	public function __construct($config = null){
		parent::__construct($config);
	}

	/**
	 * (non-PHPdoc)
	 * @see View::clean_vars()
	 */
	public function clean_vars() {
		$this->_vars = array ();
		return $this;
	}
}