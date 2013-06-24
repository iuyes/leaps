<?php
/**
 * 本地文件存储
 * @author Tongle Xu <xutongle@gmail.com>
 * @copyright Copyright (c) 2003-2103 Jinan TintSoft development co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Storage_Driver_Local extends Storage_Abstract{

	/**
	 * 构造方法
	 */
	public function __construct(){

	}

	public function write($domain, $destFile, $content, $size = -1, $attr = array(), $compress = false){

	}

	public function read($domain, $filename){

	}

	public function delete($domain, $filename){

	}

	public function file_exists($domain, $filename){

	}

	public function set_file_attr($domain, $filename, $attr = array()){

	}
}