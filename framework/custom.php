<?php
/**
 * custom.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
define ( 'CUSTOM_FUNCTION', true );
/**
 * 判断验证码是否正确
 *
 * @param string $checkcode
 */
function checkcode($checkcode = '') {
	Loader::session (); // 加载Session
	if (! empty ( $checkcode ) && (isset ( $_SESSION ['code'] ) && $_SESSION ['code'] == strtolower ( $checkcode ))) return true;
	return false;
}

/**
 * 获取IP地址归属地
 *
 * @param string $ip
 * @return string
 */
function ip_source($ip) {
	return IpSource::instance ()->get ( $ip );
}

/**
 * 生成二维码
 *
 * @param string $value 数据
 * @param string $level 纠错级别：L、M、Q、H
 * @param int $size 每个黑点的像素：1到10,用于手机端4就可以了
 * @param int $margin 图片外围的白色边框像素
 * @param bool $saveandprint 保存并打印
 */
function QRcode($value, $level = 'L', $size = 4, $margin = 4) {
	Loader::lib ( 'QRcode.QRcode', false );
	return QRcode::png ( $value, false, $level, $size, $margin );
}

/**
 * 发送电子邮件
 *
 * @param srting $toemail 要发送到的邮箱多个逗号隔开
 * @param srting $subject 邮件标题
 * @param srting $message 邮件内容
 * @param srting $from
 */
function sendmail($toemail, $subject, $message, $from = '') {
	static $mail = null;
	if (null === $mail) $mail = Loader::lib ( 'Mail' );
	return $mail->send ( $toemail, $subject, $message, $from );
}

/**
 * 发送手机短信
 *
 * @param int $mobile 手机号
 * @param string $content 内容
 */
function sendsms($mobile, $content) {
	static $sms = null;
	if ($sms === null) $sms = Factory::sms ();
	if ($sms->send ( $mobile, $content )) return true;
	return false;
}

/**
 * 系统视图类 继承 视图类
 *
 * @param $$application 应用名称
 * @param $template 模版名称
 * @param $style 视图风格名称
 */
function template($application = 'index', $template = 'index', $style = '') {
	if (! empty ( $style ) && preg_match ( '/([a-z0-9\-_]+)/is', $style )) {
	} elseif (empty ( $style ) && defined ( 'STYLE' )) {
		$style = STYLE;
	} else {
		$style = C ( 'template', 'name' );
	}
	if (empty ( $style )) $style = 'default';
	$compiledtplfile = Template::instance ()->compile ( $template, $application, $style );
	return $compiledtplfile;
}

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
		include (Web_Admin::view ( 'showmessage', 'admin' ));
	} else {
		include (template ( 'yuncms', 'message' ));
	}
	if (isset ( $_SESSION ['msg'] )) unset ( $_SESSION ['msg'] );
	exit ();
}

/**
 * 对用户的密码进行加密
 *
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt = '') {
	$pwd = array ();
	$pwd ['encrypt'] = $encrypt ? $encrypt : String::rand_string(6);
	$pwd ['password'] = md5 ( md5 ( trim ( $password ) ) . $pwd ['encrypt'] );
	return $encrypt ? $pwd ['password'] : $pwd;
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
	$string = str_replace ( '%20', '', $string );
	$string = str_replace ( '%27', '', $string );
	$string = str_replace ( '%2527', '', $string );
	$string = str_replace ( '*', '', $string );
	$string = str_replace ( '"', '&quot;', $string );
	$string = str_replace ( "'", '', $string );
	$string = str_replace ( '"', '', $string );
	$string = str_replace ( ';', '', $string );
	$string = str_replace ( '<', '&lt;', $string );
	$string = str_replace ( '>', '&gt;', $string );
	$string = str_replace ( "{", '', $string );
	$string = str_replace ( '}', '', $string );
	$string = str_replace ( '\\', '', $string );
	return $string;
}