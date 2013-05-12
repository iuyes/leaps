<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Web_Application extends Base_Application {

	public function run() {
		Core_Router::get_instance ( $app = null, $controller = null, $action = null );
		$app = ! is_null ( $app ) ? trim ( $app ) : APP;
		$controller = ! is_null ( $controller ) ? trim ( $controller ) : CONTROLLER;
		$action = ! is_null ( $action ) ? trim ( $action ) : ACTION;
		$controller = Loader::controller ( $controller, $app );
		if (method_exists ( $controller, $action ) && ! preg_match ( '/^[_]/i', $action )) {
			call_user_func ( array ($controller,$action ) );
		} else {
			throw_exception ( 'You are visiting the action is to protect the private action' );
		}
		ob_end_flush();
	}
}