<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-21
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
/**
 * 生成加密后的密码
 *
 * @param string $password
 * @param string $encrypt
 * @return string 加密后的password
 */
function password($password, $encrypt = '') {
	if (empty ( $encrypt )) $encrypt = random ( 6 );
	return md5 ( md5 ( trim ( $password ) ) . $encrypt );
}

/**
 * 转换字节数为其他单位
 *
 *
 * @param	string	$filesize	字节大小
 * @return	string	返回大小
 */
function sizecount($filesize) {
	if ($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
	} elseif ($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 .' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize.' Bytes';
	}
	return $filesize;
}