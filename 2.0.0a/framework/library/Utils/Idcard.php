<?php
/**
 * 身份证工具类
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-6-6
 * @copyright Copyright (c) 2003-2103 tintsoft.com
 * @license http://www.tintsoft.com
 * @version $Id$
 */

class Utils_Idcard {
	/**
	 * 中国公民身份证号码最小长度。
	 */
	const CHINA_ID_MIN_LENGTH = 15;

	/**
	 * 中国公民身份证号码最大长度。
	 */
	const CHINA_ID_MAX_LENGTH = 18;

	/**
	 * 最低年限
	 */
	const MIN = 1930;

	/**
	 * 省、直辖市代码表
	 */
	public static $cityCode = array ("11","12","13","14","15","21","22","23","31","32","33","34","35","36","37","41","42","43","44","45","46","50","51","52","53","54","61","62","63","64","65","71","81","82","91" );

	/**
	 * 每位加权因子
	 */
	public static $power = array (7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2 );

	/**
	 * 第18位校检码
	 */
	public static $verifyCode = array ("1","0","X","9","8","7","6","5","4","3","2" );
	/**
	 * 国内身份证校验
	 */
	public static $cityCodes = array ('11' => '北京','12' => '天津','13' => '河北','14' => '山西','15' => '内蒙古','21' => '辽宁','22' => '吉林','23' => '黑龙江','31' => '上海','32' => '江苏','33' => '浙江','34' => '安徽','35' => '福建','36' => '江西','37' => '山东','41' => '河南','42' => '湖北','43' => '湖南','44' => '广东','45' => '广西',
			'46' => '海南','50' => '重庆','51' => '四川','52' => '贵州','53' => '云南','54' => '西藏','61' => '陕西','62' => '甘肃','15' => '内蒙古','21' => '辽宁','22' => '吉林','23' => '黑龙江','31' => '上海','32' => '江苏','33' => '浙江','34' => '安徽','35' => '福建','36' => '江西','37' => '山东','41' => '河南','42' => '湖北','43' => '湖南',
			'44' => '广东','45' => '广西','46' => '海南','50' => '重庆','51' => '四川','52' => '贵州','53' => '云南','54' => '西藏','61' => '陕西','62' => '甘肃','63' => '青海','64' => '宁夏','65' => '新疆','71' => '台湾','81' => '香港','82' => '澳门','91' => '国外','63' => '青海','64' => '宁夏','65' => '新疆','71' => '台湾','81' => '香港',
			'82' => '澳门','91' => '国外' );

	/**
	 * 台湾身份证校验
	 *
	 * @var array
	 */
	public static $twFirstCode = array ('A' => 10,'B' => 11,'C' => 12,'D' => 13,'E' => 14,'F' => 15,'G' => 16,'H' => 17,'J' => 18,'K' => 19,'L' => 20,'M' => 21,'N' => 22,'P' => 23,'Q' => 24,'R' => 25,'S' => 26,'T' => 27,'U' => 28,'V' => 29,'X' => 30,'Y' => 31,'W' => 32,'Z' => 33,'I' => 34,'O' => 35 );

	/**
	 * 香港身份证校验
	 */
	public static $hkFirstCode = array ('A' => 1,'B' => 2,'C' => 3,'R' => 18,'U' => 21,'Z' => 26,'X' => 24,'W' => 23,'O' => 15,'N' => 14 );

	/**
	 * 将15位身份证号码转换为18位
	 *
	 * @param idCard 15位身份编码
	 * @return 18位身份编码
	 */
	public static function conver15CardTo18($idCard) {
		$idCard18 = "";
		if (strlen ( $idCard ) != self::CHINA_ID_MIN_LENGTH) {
			return null;
		}
		if (self::isNum ( $idCard )) {
			$idCard18 = substr ( $idCard, 0, 6 ) . '19' . substr ( $idCard, 6 );
			// 转换字符数组
			$iArr = str_split ( $idCard18 );
			if ($iArr != null) {
				$iSum17 = self::getPowerSum ( $iArr );
				// 获取校验位
				$sVal = self::getCheckCode18 ( $iSum17 );
				if (strlen ( $sVal ) > 0) {
					$idCard18 .= $sVal;
				} else {
					return null;
				}
			}
		} else {
			return null;
		}
		return $idCard18;
	}

	/**
	 * 验证身份证是否合法
	 */
	public static function validateCard($idCard) {
		$card = trim ( $idCard );
		if (self::validateIdCard18 ( $card )) {
			return true;
		}
		if (self::validateIdCard15 ( $card )) {
			return true;
		}
		$cardval = self::validateIdCard10 ( $card );
		if ($cardval != null) {
			if ($cardval [2] == "true") {
				return true;
			}
		}
		return false;
	}

	/**
	 * 验证18位身份编码是否合法
	 *
	 * @param int $idCard 身份编码
	 * @return boolean 是否合法
	 */
	public static function validateIdCard18($idCard) {
		$bTrue = false;
		if (strlen ( $idCard ) == self::CHINA_ID_MAX_LENGTH) {
			// 前17位
			$code17 = substr ( $idCard, 0, 17 );
			// 第18位
			$code18 = substr ( $idCard, 17, 1 );
			if (self::isNum ( $code17 )) {
				$iArr = str_split ( $code17 );
				if ($iArr != null) {
					$iSum17 = self::getPowerSum ( $iArr );
					// 获取校验位
					$val = self::getCheckCode18 ( $iSum17 );
					if (strlen ( $val ) > 0 && $val == $code18) {
						$bTrue = true;
					}
				}
			}
		}
		return $bTrue;
	}

	/**
	 * 验证15位身份编码是否合法
	 *
	 * @param string $idCard 身份编码
	 * @return boolean 是否合法
	 */
	public static function validateIdCard15($idCard) {
		if (strlen ( $idCard ) != self::CHINA_ID_MIN_LENGTH) {
			return false;
		}
		if (self::isNum ( $idCard )) {
			$proCode = substr ( $idCard, 0, 2 );
			if (! isset ( self::$cityCodes [$proCode] )) {
				return false;
			}
			// 升到18位
			$idCard = self::conver15CardTo18 ( $idCard );
			return self::validateIdCard18 ( $idCard );
		} else {
			return false;
		}
		return true;
	}

	/**
	 * 验证10位身份编码是否合法
	 *
	 * @param idCard 身份编码
	 * @return 身份证信息数组 <p>
	 *         [0] - 台湾、澳门、香港 [1] - 性别(男M,女F,未知N) [2] - 是否合法(合法true,不合法false)
	 *         若不是身份证件号码则返回null
	 *         </p>
	 */
	public static function validateIdCard10($idCard) {
		$info = array ();
		$card = str_replace ( "[\\(|\\)]", "", $card );
		$len = strlen ( $card );
		if ($len != 8 && $len != 9 && $len != 10) {
			return null;
		}
		if (0 < preg_match ( "/^[a-zA-Z][0-9]{9}$/", $idCard )) { // 台湾
			$info [0] = "台湾";
			$char2 = substr ( $idCard, 1, 1 );
			if ($char2 == "1") {
				$info [1] = "M";
			} else if ($char2 == "2") {
				$info [1] = "F";
			} else {
				$info [1] = "N";
				$info [2] = "false";
				return $info;
			}
			$info [2] = self::validateTWCard ( $idCard ) ? "true" : "false";
		} else if (0 < preg_match ( "/^[1|5|7][0-9]{6}\\(?[0-9A-Z]\\)?$/", $idCard )) { // 澳门
			$info [0] = "澳门";
			$info [1] = "N";
			// TODO
		} else if (0 < preg_match ( "/^[A-Z]{1,2}[0-9]{6}\\(?[0-9A]\\)?$/", $idCard )) { // 香港
			$info [0] = "香港";
			$info [1] = "N";
			$info [2] = self::validateHKCard ( $idCard ) ? "true" : "false";
		} else {
			return null;
		}
		return info;
	}

	/**
	 * 验证台湾身份证号码
	 *
	 * @param string idCard 身份证号码
	 * @return 验证码是否符合
	 */
	public static function validateTWCard($idCard) {
		$start = substr ( $idCard, 0, 1 );
		$mid = substr ( $idCard, 1, 8 );
		$end = substr ( $idCard, 9, 1 );
		$iStart = self::$twFirstCode ['start'];
		$sum = $iStart / 10 + ($iStart % 10) * 9;
		$chars = str_split ( $mid );
		$iflag = 8;
		foreach ( $chars as $c ) {
			$sum = $sum + $c + "" * $iflag;
			$iflag --;
		}
		return ($sum % 10 == 0 ? 0 : (10 - $sum % 10)) == $end ? true : false;
	}

	/**
	 * 验证香港身份证号码(存在Bug，部份特殊身份证无法检查)
	 * <p>
	 * 身份证前2位为英文字符，如果只出现一个英文字符则表示第一位是空格，对应数字58 前2位英文字符A-Z分别对应数字10-35
	 * 最后一位校验码为0-9的数字加上字符"A"，"A"代表10
	 * </p>
	 * <p>
	 * 将身份证号码全部转换为数字，分别对应乘9-1相加的总和，整除11则证件号码有效
	 * </p>
	 *
	 * @param idCard 身份证号码
	 * @return 验证码是否符合
	 */
	public static function validateHKCard($idCard) {
		$card = str_replace ( "[\\(|\\)]", "", $card );
		$sum = 0;
		if (strlen ( $card ) == 9) {
			$card0_arr = str_split ( strtoupper ( substr ( $card, 0, 1 ) ) );
			$card1_arr = str_split ( strtoupper ( substr ( $card, 1, 1 ) ) );
			$sum = ($card0_arr [0] - 55) * 9 . ($card1_arr [0] - 55) * 8;
			$card = substr ( $card, 1, 8 );
		} else {
			$card0_arr = str_split ( strtoupper ( substr ( $card, 0, 1 ) ) );
			$sum = 522 + ($card0_arr [0] - 55) * 8;
		}
		$mid = substr ( $card, 1, 6 );
		$end = substr ( $card, 7, 1 );
		$chars = str_split ( $mid );
		$iflag = 7;
		foreach ( $chars as $c ) {
			$sum = $sum + $c + "" * $iflag;
			$iflag --;
		}
		if (strtoupper ( $end ) == "A") {
			$sum = $sum + 10;
		} else {
			$sum = $sum + $end;
		}
		return ($sum % 11 == 0) ? true : false;
	}

	/**
	 * 根据身份编号获取年龄
	 *
	 * @param string idCard 身份编号
	 * @return 年龄
	 */
	public static function getAgeByIdCard($idCard) {
		$iAge = 0;
		if (strlen ( $idCard ) == self::CHINA_ID_MIN_LENGTH) {
			$idCard = self::conver15CardTo18 ( $idCard );
		}
		$year = substr ( $idCard, 6, 4 );
		$iCurrYear = date ( 'Y', time () );
		$iAge = $iCurrYear - $year;
		return $iAge;
	}

	/**
	 * 根据身份编号获取生日天
	 *
	 * @param string $idCard 身份编号
	 * @return NULL string
	 */
	public static function getDateByIdCard($idCard) {
		$len = strlen ( $idCard );
		if ($len < self::CHINA_ID_MIN_LENGTH) {
			return null;
		} else if ($len == CHINA_ID_MIN_LENGTH) {
			$idCard = self::conver15CardTo18 ( $idCard );
		}
		return substr ( $idCard, 12, 2 );
	}

	/**
	 * 根据身份编号获取性别
	 *
	 * @param string $idCard 身份编号
	 * @return 性别(M-男，F-女，N-未知)
	 */
	public static function getGenderByIdCard($idCard) {
		$sGender = "N";
		if (strlen ( $idCard ) == self::CHINA_ID_MIN_LENGTH) {
			$idCard = self::conver15CardTo18 ( $idCard );
		}
		$sCardNum = substr ( $idCard, 16, 1 );
		if (( int ) $sCardNum % 2 != 0) {
			$sGender = "M";
		} else {
			$sGender = "F";
		}
		return $sGender;
	}

	/**
	 * 根据身份编号获取户籍省份
	 *
	 * @param string $idCard 身份编号
	 * @return string
	 */
	public static function getProvinceByIdCard($idCard) {
		$len = strlen ( $idCard );
		$sProvince = null;
		$sProvinNum = "";
		if ($len == self::CHINA_ID_MIN_LENGTH || $len == self::CHINA_ID_MAX_LENGTH) {
			$sProvinNum = substr ( $idCard, 0, 2 );
		}
		$sProvince = self::$cityCodes [$sProvinNum];
		return $sProvince;
	}

	/**
	 * 数字验证
	 *
	 * @param int $val
	 */
	public static function isNum($val) {
		return $val == null || $val == "" ? false : 0 < preg_match ( '/^[0-9]*$/', $val );
	}

	/**
	 * 验证小于当前日期 是否有效
	 *
	 * @param int $iYear 待验证日期(年)
	 * @param int $iMonth 待验证日期(月 1-12)
	 * @param int $iDate 待验证日期(日)
	 * @return 是否有效
	 */
	public static function valiDate($iYear, $iMonth, $iDate) {
		$year = date ( 'Y', time () );
		if ($iYear < self::MIN || $iYear >= $year) {
			return false;
		}
		if ($iMonth < 1 || $iMonth > 12) {
			return false;
		}
		switch ($iMonth) {
			case 4 :
			case 6 :
			case 9 :
			case 11 :
				$datePerMonth = 30;
				break;
			case 2 :
				$dm = (($iYear % 4 == 0 && $iYear % 100 != 0) || ($iYear % 400 == 0)) && ($iYear > self::MIN && $iYear < $year);
				$datePerMonth = $dm ? 29 : 28;
				break;
			default :
				$datePerMonth = 31;
		}
		return ($iDate >= 1) && ($iDate <= $datePerMonth);
	}

	/**
	 * 将身份证的每位和对应位的加权因子相乘之后，再得到和值
	 *
	 * @param array $iArr
	 * @return 身份证编码。
	 */
	private static function getPowerSum($iArr) {
		$iSum = 0;
		$power_len = count ( self::$power );
		$iarr_len = count ( $iArr );
		if ($power_len == $iarr_len) {
			for($i = 0; $i < $iarr_len; $i ++) {
				for($j = 0; $j < $power_len; $j ++) {
					if ($i == $j) {
						$iSum = $iSum + $iArr [$i] * self::$power [$j];
					}
				}
			}
		}
		return $iSum;
	}

	/**
	 * 将power和值与11取模获得余数进行校验码判断
	 *
	 * @param int $iSum
	 * @return 校验位
	 */
	private static function getCheckCode18($iSum) {
		$sCode = "";
		switch ($iSum % 11) {
			case 10 :
				$sCode = "2";
				break;
			case 9 :
				$sCode = "3";
				break;
			case 8 :
				$sCode = "4";
				break;
			case 7 :
				$sCode = "5";
				break;
			case 6 :
				$sCode = "6";
				break;
			case 5 :
				$sCode = "7";
				break;
			case 4 :
				$sCode = "8";
				break;
			case 3 :
				$sCode = "9";
				break;
			case 2 :
				$sCode = "x";
				break;
			case 1 :
				$sCode = "0";
				break;
			case 0 :
				$sCode = "1";
				break;
		}
		return $sCode;
	}
}