<?php
/**
 * 文件处理类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-5-14
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id: File.php 546 2013-05-17 03:39:11Z 85825770@qq.com $
 */
class File {

	/**
	 * 文件读取模式
	 *
	 * @var int
	 */
	const FILE_READ_MODE = 0644;

	/**
	 * 文件写入模式
	 *
	 * @var int
	 */
	const FILE_WRITE_MODE = 0666;

	/**
	 * 目录读取模式
	 *
	 * @var int
	 */
	const DIR_READ_MODE = 0755;

	/**
	 * 目录写入模式
	 *
	 * @var int
	 */
	const DIR_WRITE_MODE = 0777;

	/**
	 * 以读的方式打开文件，具有较强的平台移植性
	 *
	 * @var string
	 */
	const READ = 'rb';

	/**
	 * 以读写的方式打开文件，具有较强的平台移植性
	 *
	 * @var string
	 */
	const READWRITE = 'rb+';

	/**
	 * 以写的方式打开文件，具有较强的平台移植性
	 *
	 * @var string
	 */
	const WRITE = 'wb';

	/**
	 * 以读写的方式打开文件，具有较强的平台移植性
	 *
	 * @var string
	 */
	const WRITEREAD = 'wb+';

	/**
	 * 以追加写入方式打开文件，具有较强的平台移植性
	 *
	 * @var string
	 */
	const APPEND_WRITE = 'ab';

	/**
	 * 以追加读写入方式打开文件，具有较强的平台移植性
	 *
	 * @var string
	 */
	const APPEND_WRITEREAD = 'ab+';

	/**
	 * 删除文件
	 *
	 * @param string $filename 文件名称
	 * @return boolean
	 */
	public static function del($filename) {
		return @unlink ( $filename );
	}

	/**
	 * 保存文件
	 *
	 * @param string $file_name 保存的文件名
	 * @param mixed $data 保存的数据
	 * @param boolean $is_build_return 是否组装保存的数据是return
	 *        $params的格式，如果没有则以变量声明的方式保存,默认为true则以return的方式保存
	 * @param string $method 打开文件方式，默认为rb+的形式
	 * @param boolean $if_lock 是否对文件加锁，默认为true即加锁
	 *
	 */
	public static function save_php($file_name, $data, $is_build_return = true, $method = self::READWRITE, $if_lock = true) {
		$temp = "<?php\r\n ";
		if (! $is_build_return && is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				if (! preg_match ( '/^\w+$/', $key )) continue;
				$temp .= "\$" . $key . " = " . String::var_to_string ( $value ) . ";\r\n";
			}
			$temp .= "\r\n?>";
		} else {
			($is_build_return) && $temp .= " return ";
			$temp .= String::var_to_string ( $data ) . ";\r\n?>";
		}
		return self::write ( $file_name, $temp, $method, $if_lock );
	}

	/**
	 * 写文件
	 *
	 * @param string $file_name 文件绝对路径
	 * @param string $data 数据
	 * @param string $method 读写模式,默认模式为rb+
	 * @param bool $if_lock 是否锁文件，默认为true即加锁
	 * @param bool $if_check_path 是否检查文件名中的“..”，默认为true即检查
	 * @param bool $if_chmod 是否将文件属性改为可读写,默认为true
	 * @return int 返回写入的字节数
	 */
	public static function write($file_name, $data, $method = self::READWRITE, $if_lock = true, $if_check_path = true, $if_chmod = true) {
		touch ( $file_name );
		if (! $handle = fopen ( $file_name, $method )) return false;
		$if_lock && flock ( $handle, LOCK_EX );
		$write_check = fwrite ( $handle, $data );
		$method == self::READWRITE && ftruncate ( $handle, strlen ( $data ) );
		fclose ( $handle );
		$if_chmod && chmod ( $file_name, 0777 );
		return $write_check;
	}

	/**
	 * 读取文件
	 *
	 * @param string $filename 文件绝对路径
	 * @param string $method 读取模式默认模式为rb
	 * @return string 从文件中读取的数据
	 */
	public static function read($filename, $method = self::READ) {
		$data = '';
		if (! file_exists ( $filename )) return false;
		if (! $handle = fopen ( $filename, $method )) return false;
		while ( ! feof ( $handle ) )
			$data .= fgets ( $handle, 4096 );
		fclose ( $handle );
		return $data;
	}

	/**
	 * 文件下载
	 *
	 * @param $filepath 文件路径
	 * @param $filename 文件名称
	 */
	public static function down($filepath, $filename = '') {
		if (! $filename) $filename = basename ( $filepath );
		if (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE' )) $filename = rawurlencode ( $filename );
		$filetype = self::get_suffix ( $filename );
		$filesize = sprintf ( "%u", filesize ( $filepath ) );
		if (ob_get_length () !== false) @ob_end_clean ();
		header ( 'Pragma: public' );
		header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' );
		header ( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header ( 'Cache-Control: pre-check=0, post-check=0, max-age=0' );
		header ( 'Content-Transfer-Encoding: binary' );
		header ( 'Content-Encoding: none' );
		header ( 'Content-type: ' . $filetype );
		header ( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header ( 'Content-length: ' . $filesize );
		readfile ( $filepath );
		exit ();
	}

	/**
	 * 检查指定的文件名是否是正常的文件
	 *
	 * @param string $filename
	 * @return boolean
	 */
	public static function is_file($filename) {
		return $filename ? is_file ( $filename ) : false;
	}

	/**
	 * 取得文件信息
	 *
	 * @param string $filename 文件名字
	 * @return array 文件信息
	 */
	public static function get_info($filename) {
		return self::is_file ( $filename ) ? stat ( $filename ) : array ();
	}

	/**
	 * 取得文件后缀
	 *
	 * @param string $filename 文件名称
	 * @return string
	 */
	public static function get_suffix($filename) {
		if (false === ($rpos = strrpos ( $filename, '.' ))) return '';
		return substr ( $filename, $rpos + 1 );
	}
}