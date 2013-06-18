<?php
/**
 * 文件夹工具类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-14
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: Folder.php 539 2013-05-16 09:35:50Z 85825770@qq.com $
 */
class Utils_Folder {
	const READ_ALL = '0';
	const READ_FILE = '1';
	const READ_DIR = '2';

	/**
	 * 递归创建目录
	 *
	 * @param string $path 目录路径
	 * @param int $permissions 权限
	 * @return boolean
	 */
	public static function mk($path, $permissions = 0777) {
		if (is_dir ( $path ) || $path == '') {
			return true;
		}
		$_path = dirname ( $path );
		if ($_path !== $path) self::mk ( $_path, $permissions );
		return @mkdir ( $path, $permissions );
	}

	/**
	 * 删除目录
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public static function rm($dir) {
		if (! self::is_dir ( $dir )) return false;
		if (! $handle = @opendir ( $dir )) return false;
		while ( false !== ($file = readdir ( $handle )) ) {
			if ('.' === $file || '..' === $file) continue;
			$_path = $dir . '/' . $file;
			if (self::is_dir ( $_path )) {
				self::rm ( $_path );
			} elseif (Utils_File::is_file ( $_path ))
				Utils_File::del ( $_path );
		}
		@rmdir ( $dir );
		@closedir ( $handle );
		return true;
	}

	/**
	 * 清除文件夹下所有文件以及文件夹
	 *
	 * @param string $dir 目录
	 * @return boolean
	 */
	public static function clear($dir) {
		if (! self::is_dir ( $dir )) return false;
		if (! $handle = @opendir ( $dir )) return false;
		while ( false !== ($file = readdir ( $handle )) ) {
			if ('.' === $file || '..' === $file) continue;
			$filename = $dir . '/' . $file;
			if (self::is_dir ( $filename )) {
				self::rm ( $filename );
			} elseif (Utils_File::is_file ( $filename ))
				Utils_File::del ( $filename );
		}
		@closedir ( $handle );
		return true;
	}

	/**
	 * 获取文件列表
	 *
	 * @param string $dir
	 * @param boolean $mode 只读取文件列表,不包含文件夹
	 * @return array
	 */
	public static function read($dir, $mode = self::READ_ALL) {
		if (! $handle = @opendir ( $dir )) return array ();
		$files = array ();
		while ( false !== ($file = @readdir ( $handle )) ) {
			if ('.' === $file || '..' === $file) continue;
			if ($mode === self::READ_DIR) {
				if (self::is_dir ( $dir . '/' . $file )) $files [] = $file;
			} elseif ($mode === self::READ_FILE) {
				if (Utils_File::is_file ( $dir . '/' . $file )) $files [] = $file;
			} else
				$files [] = $file;
		}
		@closedir ( $handle );
		return $files;
	}

	/**
	 * 判断输入是否为目录
	 *
	 * @param string $dir
	 * @return boolean
	 */
	public static function is_dir($dir) {
		return $dir ? is_dir ( $dir ) : false;
	}

	/**
	 * 取得目录信息
	 *
	 * @param string $dir 目录路径
	 * @return array
	 */
	public static function get_info($dir) {
		return self::is_dir ( $dir ) ? stat ( $dir ) : array ();
	}
}