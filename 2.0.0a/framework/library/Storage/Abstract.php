<?php
/**
 * 数据存储抽象类
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
abstract class Storage_Abstract {

	/**
	 * 错误代码
	 * @var int
	 */
	public $error_code = '0';

	/**
	 * 将数据写入存储
	 *
	 * @param string $domain 域
	 * @param string $destFile
	 * @param string $content
	 * @param int $size
	 * @param array $attr
	 * @param bool $compress
	 */
	abstract public function write($domain, $destFile, $content, $size = -1, $attr = array(), $compress = false);

	/**
	 * 获取文件的内容
	 *
	 * @param string $domain 域
	 * @param string $filename 文件名
	 */
	abstract public function read($domain, $filename);

	/**
	 * 删除文件
	 * @param string $domain 域
	 * @param string $filename
	 */
	abstract public function delete($domain, $filename);



	/**
	 * 检查文件是否存在
	 *
	 * @param string $domain 域
	 * @param string $filename 文件名
	 */
	abstract public function file_exists($domain, $filename);

	/**
	 * 设置文件属性
	 * @param unknown $domain
	 * @param unknown $filename
	 * @param unknown $attr
	 */
	abstract public function set_file_attr($domain, $filename, $attr = array());

	/**
	 * 获取错误信息
	 * @return multitype:
	 */
	public function get_error(){
		$msg = array('0'=>'成功','-2'=>'配额统计错误');
		return $msg[$this->error_code];
	}
}