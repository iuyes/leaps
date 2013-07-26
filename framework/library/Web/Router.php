<?php
/**
 * Router.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Web_Router extends Base_Router {
	protected $pattern ='/^(\w+)?(\/\w+)?(\/\w+)?.*$/i';
	protected $reverse = '/%s/%s/%s';
	protected $separator = '&=';
	protected $params = array ('action' => 3,'controller' => 2,'app' => 1 );

	public function _initialize() {
		if (C ( 'router', SITE_HOST )) { // 加载基于域名的URL 路由配置
			$this->_config = C ( 'router', SITE_HOST );
			define ( 'SUB_DOMAIN', strtolower ( substr ( SITE_HOST, 0, strpos ( SITE_HOST, '.' ) ) ) ); // 二级域名定义
			$this->params = array ('action' => 2,'controller' => 1 );
		} else { // 使用默认路由配置
			$this->_config = C ( 'router', 'default' );
		}
		$full_url = Base_Request::get_host_info () .Base_Request::get_request_uri ();
		$this->_pathinfo = $_pathinfo = trim ( str_replace ( Base_Request::get_base_url (true), '', $full_url ), '/' );
	}
}