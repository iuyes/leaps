<?php
/**
 * 用户自定义
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-26
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: Global.php 2 2013-01-14 07:14:05Z xutongle $
 */
/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.yuncms.net'));
 *
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
	if ($ms == 301) {
		Loader::session ();
		$_SESSION ['msg'] = $msg;
		Header ( "HTTP/1.1 301 Moved Permanently" );
		Header ( "Location: $url_forward" );
		exit ();
	}
	if (defined ( 'IN_ADMIN' )) {
		include (admin::admin_tpl ( 'showmessage', 'admin' ));
	} else {
		include (template ( 'yuncms', 'message' ));
	}
	if (isset ( $_SESSION ['msg'] )) unset ( $_SESSION ['msg'] );
	exit ();
}