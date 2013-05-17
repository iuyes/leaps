<?php
/**
 * 应用程序创建类
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-16
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: Application.php 546 2013-05-17 03:39:11Z 85825770@qq.com $
 */
class Command_Application extends Base_Application {
	public function _initialize(){
		//echo self::colorize("Your command has successfully executed...", "SUCCESS");
	}

	protected function execute() {

		//echo '运行命令行程序';
	}

	/**
	 * !CodeTemplates.overridecomment.nonjd!
	 * @see Base_Application::showErrorMessage()
	 */
	protected function showErrorMessage($message, $file, $line, $trace, $errorcode) {
		parent::showErrorMessage($message, $file, $line, $trace, $errorcode);
		printf ( $message . ' in ' . $file . ' on line ' . $line );
	}
}