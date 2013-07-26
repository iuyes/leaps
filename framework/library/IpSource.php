<?php
/**
 * 根据ip地址获取ip所在地区工具类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-26
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: IpSource.php 564 2013-05-17 09:34:41Z 85825770@qq.com $
 */
class IpSource {
	protected static $instance = null;
	public $fp = NULL; // 定义文件指针
	public $func; // 处理的方法
	private $offset;
	private $index;
	public static function &instance() {
		if (null === self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	public function __construct() {
		if (@file_exists ( FW_PATH . 'resource' . DIRECTORY_SEPARATOR . 'ipdata' . DIRECTORY_SEPARATOR . 'mini.Dat' )) {
			$this->func = 'data_mini';
			$this->fp = @fopen ( FW_PATH . 'resource' . DIRECTORY_SEPARATOR . 'ipdata' . DIRECTORY_SEPARATOR . 'mini.Dat', 'rb' );
			$this->offset = unpack ( 'Nlen', fread ( $this->fp, 4 ) );
			$this->index = fread ( $this->fp, $this->offset ['len'] - 4 );
		} elseif (@file_exists ( FW_PATH . 'resource' . DIRECTORY_SEPARATOR . 'ipdata' . DIRECTORY_SEPARATOR . 'QQWry.Dat' )) {
			$this->func = 'data_full';
			$this->fp = @fopen ( FW_PATH . 'resource' . DIRECTORY_SEPARATOR . 'ipdata' . DIRECTORY_SEPARATOR . 'QQWry.Dat', 'rb' );
		}
	}

	/**
	 * 取得地区名
	 *
	 * @param string $ip IP地址
	 *        @ return string/null
	 */
	public function get($ip) {
		$return = '';
		if (Validate::is_ipv4 ( $ip )) {
			$iparray = explode ( '.', $ip );
			if ($iparray [0] == 10 || $iparray [0] == 127 || ($iparray [0] == 192 && $iparray [1] == 168) || ($iparray [0] == 172 && ($iparray [1] >= 16 && $iparray [1] <= 31))) {
				$return = 'LAN';
			} elseif ($iparray [0] > 255 || $iparray [1] > 255 || $iparray [2] > 255 || $iparray [3] > 255) {
				$return = 'Invalid IP Address';
			} else {
				$return = $this->func ? $this->{$this->func} ( $ip ) : '';
				if (strpos ( $return, ' ' ) !== false) $return = substr ( $return, 0, strpos ( $return, ' ' ) );
			}
			if (strtolower ( CHARSET ) == 'utf-8') $return = iconv ( 'gbk', 'utf-8', $return );
		}
		return $return;
	}
	public function get_by_api($ip) {
		$http = Loader::lib ( 'HttpClient' );
		$result = $http->get ( 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip );
		$res = json_decode ( $result, true );
		return $res ['code'] == 0 ? $res ['data'] : false;
	}

	/**
	 * 获取城市名称
	 */
	public function get_city($ip) {
		$localinfo = '';
		$address = $this->get ( $ip );
		if (strpos ( $address, '省' ) !== false && strpos ( $address, '市' ) !== false) {
			$address = explode ( '省', $address );
			$address = $address [1];
		}
		$address = str_replace ( '市', '', $address );
		$localinfo ['city'] = trim ( $address );
		$name = CHARSET == 'gbk' ? $localinfo ['city'] : iconv ( 'utf-8', 'gbk', $localinfo ['city'] );
		$name = str_replace ( '市', '', $name );
		$letters = Pinyin::instance ()->output ( $name, false );
		$localinfo ['pinyin'] = strtolower ( $letters );
		return $localinfo;
	}

	/**
	 * 使用mini.Dat ip数据包获取地区
	 *
	 * @param string $ip IP地址
	 *        @ return string/null
	 */
	private function data_mini($ip) {
		$ipdot = explode ( '.', $ip );
		$ipdot [0] = ( int ) $ipdot [0];
		$ipdot [1] = ( int ) $ipdot [1];
		$ip = pack ( 'N', ip2long ( $ip ) );
		$length = $this->offset ['len'] - 1028;
		$start = unpack ( 'Vlen', $this->index [$ipdot [0] * 4] . $this->index [$ipdot [0] * 4 + 1] . $this->index [$ipdot [0] * 4 + 2] . $this->index [$ipdot [0] * 4 + 3] );
		for($start = $start ['len'] * 8 + 1024; $start < $length; $start += 8) {
			if ($this->index {$start} . $this->index {$start + 1} . $this->index {$start + 2} . $this->index {$start + 3} >= $ip) {
				$this->index_offset = unpack ( 'Vlen', $this->index {$start + 4} . $this->index {$start + 5} . $this->index {$start + 6} . "\x0" );
				$this->index_length = unpack ( 'Clen', $this->index {$start + 7} );
				break;
			}
		}
		fseek ( $this->fp, $this->offset ['len'] + $this->index_offset ['len'] - 1024 );
		if ($this->index_length ['len']) {
			return str_replace ( '- ', '', fread ( $this->fp, $this->index_length ['len'] ) );
		} else
			return 'Unknown';
	}

	/**
	 * 使用QQWry.Dat ip数据包获取地区
	 *
	 * @param string $ip IP地址
	 *        @ return string/null
	 */
	private function data_full($ip) {
		rewind ( $this->fp );
		$ip = explode ( '.', $ip );
		$ipNum = $ip [0] * 16777216 + $ip [1] * 65536 + $ip [2] * 256 + $ip [3];
		if (! ($DataBegin = fread ( $this->fp, 4 )) || ! ($DataEnd = fread ( $this->fp, 4 ))) return;
		@$ipbegin = implode ( '', unpack ( 'L', $DataBegin ) );
		if ($ipbegin < 0) $ipbegin += pow ( 2, 32 );
		@$ipend = implode ( '', unpack ( 'L', $DataEnd ) );
		if ($ipend < 0) $ipend += pow ( 2, 32 );
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;
		$BeginNum = $ip2num = $ip1num = 0;
		$ipAddr1 = $ipAddr2 = '';
		$EndNum = $ipAllNum;
		while ( $ip1num > $ipNum || $ip2num < $ipNum ) {
			$Middle = intval ( ($EndNum + $BeginNum) / 2 );
			fseek ( $this->fp, $ipbegin + 7 * $Middle );
			$ipData1 = fread ( $this->fp, 4 );
			if (strlen ( $ipData1 ) < 4) {
				fclose ( $this->fp );
				return 'System Error';
			}
			$ip1num = implode ( '', unpack ( 'L', $ipData1 ) );
			if ($ip1num < 0) $ip1num += pow ( 2, 32 );
			if ($ip1num > $ipNum) {
				$EndNum = $Middle;
				continue;
			}
			$DataSeek = fread ( $this->fp, 3 );
			if (strlen ( $DataSeek ) < 3) {
				fclose ( $this->fp );
				return 'System Error';
			}
			$DataSeek = implode ( '', unpack ( 'L', $DataSeek . chr ( 0 ) ) );
			fseek ( $this->fp, $DataSeek );
			$ipData2 = fread ( $this->fp, 4 );
			if (strlen ( $ipData2 ) < 4) {
				fclose ( $this->fp );
				return 'System Error';
			}
			$ip2num = implode ( '', unpack ( 'L', $ipData2 ) );
			if ($ip2num < 0) $ip2num += pow ( 2, 32 );
			if ($ip2num < $ipNum) {
				if ($Middle == $BeginNum) {
					fclose ( $this->fp );
					return 'Unknown';
				}
				$BeginNum = $Middle;
			}
		}
		$ipFlag = fread ( $this->fp, 1 );
		if ($ipFlag == chr ( 1 )) {
			$ipSeek = fread ( $this->fp, 3 );
			if (strlen ( $ipSeek ) < 3) {
				fclose ( $this->fp );
				return 'System Error';
			}
			$ipSeek = implode ( '', unpack ( 'L', $ipSeek . chr ( 0 ) ) );
			fseek ( $this->fp, $ipSeek );
			$ipFlag = fread ( $this->fp, 1 );
		}
		if ($ipFlag == chr ( 2 )) {
			$AddrSeek = fread ( $this->fp, 3 );
			if (strlen ( $AddrSeek ) < 3) {
				fclose ( $this->fp );
				return 'System Error';
			}
			$ipFlag = fread ( $this->fp, 1 );
			if ($ipFlag == chr ( 2 )) {
				$AddrSeek2 = fread ( $this->fp, 3 );
				if (strlen ( $AddrSeek2 ) < 3) {
					fclose ( $this->fp );
					return 'System Error';
				}
				$AddrSeek2 = implode ( '', unpack ( 'L', $AddrSeek2 . chr ( 0 ) ) );
				fseek ( $this->fp, $AddrSeek2 );
			} else
				fseek ( $this->fp, - 1, SEEK_CUR );
			while ( ($char = fread ( $this->fp, 1 )) != chr ( 0 ) )
				$ipAddr2 .= $char;
			$AddrSeek = implode ( '', unpack ( 'L', $AddrSeek . chr ( 0 ) ) );
			fseek ( $this->fp, $AddrSeek );
			while ( ($char = fread ( $this->fp, 1 )) != chr ( 0 ) )
				$ipAddr1 .= $char;
		} else {
			fseek ( $this->fp, - 1, SEEK_CUR );
			while ( ($char = fread ( $this->fp, 1 )) != chr ( 0 ) )
				$ipAddr1 .= $char;
			$ipFlag = fread ( $this->fp, 1 );
			if ($ipFlag == chr ( 2 )) {
				$AddrSeek2 = fread ( $this->fp, 3 );
				if (strlen ( $AddrSeek2 ) < 3) {
					fclose ( $this->fp );
					return 'System Error';
				}
				$AddrSeek2 = implode ( '', unpack ( 'L', $AddrSeek2 . chr ( 0 ) ) );
				fseek ( $this->fp, $AddrSeek2 );
			} else
				fseek ( $this->fp, - 1, SEEK_CUR );
			while ( ($char = fread ( $this->fp, 1 )) != chr ( 0 ) )
				$ipAddr2 .= $char;
		}
		if (preg_match ( '/http/i', $ipAddr2 )) $ipAddr2 = '';
		$ipaddr = "$ipAddr1 $ipAddr2";
		$ipaddr = preg_replace ( '/CZ88\.NET/is', '', $ipaddr );
		$ipaddr = preg_replace ( '/^\s*/is', '', $ipaddr );
		$ipaddr = preg_replace ( '/\s*$/is', '', $ipaddr );
		if (preg_match ( '/http/i', $ipaddr ) || $ipaddr == '') $ipaddr = 'Unknown';
		return '' . $ipaddr;
	}
	private function close() {
		@fclose ( $this->fp );
	}

	/**
	 * 析构方法
	 */
	public function __destruct() {
		$this->close ();
	}
}