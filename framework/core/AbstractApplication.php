<?php
/**
 * 应用基础接口
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
abstract class AbstractApplication {

	/**
	 * 请求对象
	 *
	 * @var HttpRequest
	 */
	protected $request;

	/**
	 * 响应对象
	 *
	 * @var HttpResponse
	 */
	protected $response;

	/**
	 * 组建工厂对象
	 *
	 * @var Factory
	 */
	protected $factory = null;

	/**
	 * 路由对象
	 *
	 * @var Router
	 */
	protected $handlerAdapter = null;

	/**
	 * 应用初始化操作
	 *
	 * @param HttpRequest $request
	 * @param HttpResponse $response
	 * @param Factory $factory
	 */
	public function __construct($request, $response, $factory) {
		$this->response = $response;
		$this->request = $request;
		$this->factory = $factory;
	}

	/**
	 * 请求处理完毕后，进一步分发
	 *
	 * @param Forward $forward
	 * @param boolean $display
	 */
	abstract public function doDispatch($forward);

	/**
	 * 处理错误请求
	 * 根据错误请求的相关信息,将程序转向到错误处理句柄进行错误处理
	 *
	 * @param ErrorMessage $errorMessage
	 * @param int $errorcode
	 * @return void
	 */
	abstract protected function sendErrorMessage($errorMessage, $errorcode);
}