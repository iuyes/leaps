<?php
/**
 *
 * 自定义函数
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
	if (! empty ( $checkcode ) && (isset($_SESSION ['code']) && $_SESSION ['code'] == strtolower ( $checkcode ))) return true;
	return false;
}

/**
 * 使用phpqrcode生成二维码
 *
 * @param string $value 二维码数据
 * @param string $level 纠错级别：L、M、Q、H
 * @param int $size 点的大小：1到10,用于手机端4就可以了
 */
function qrcode($value, $level = 'L', $size = 4) {
	Loader::lib ( 'QRcode.QRcode', false );
	return QRcode::png ( $value, false, $level, $size );
}

/**
 * 获取IP地址归属地
 * @param string $ip
 * @return string
 */
function ip_source($ip){
	return IpSource::instance()->get ( $ip );
}