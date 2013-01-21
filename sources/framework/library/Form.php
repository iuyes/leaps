<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-1-16
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id$
 */
class Form {

	/**
	 * 下拉选择框
	 */
	public static function select($array = array(), $id = 0, $str = '', $default_option = '') {
		$string = '<select ' . $str . '>';
		$default_selected = (empty ( $id ) && $default_option) ? 'selected' : '';
		if ($default_option) $string .= "<option value='' $default_selected>$default_option</option>";
		if (! is_array ( $array ) || count ( $array ) == 0) return false;
		$ids = array ();
		if (isset ( $id )) $ids = explode ( ',', $id );
		foreach ( $array as $key => $value ) {
			$selected = in_array ( $key, $ids ) ? 'selected' : '';
			$string .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
		}
		$string .= '</select>';
		return $string;
	}

	/**
	 * 复选框
	 *
	 * @param $array 选项 二维数组
	 * @param $id 默认选中值，多个用 '逗号'分割
	 * @param $str 属性
	 * @param $defaultvalue 是否增加默认值 默认值为 -99
	 * @param $width 宽度
	 */
	public static function checkbox($array = array(), $id = '', $str = '', $defaultvalue = '', $width = 0, $field = '') {
		$string = '';
		$id = trim ( $id );
		if ($id != '') $id = strpos ( $id, ',' ) ? explode ( ',', $id ) : array ($id );
		if ($defaultvalue) $string .= '<input type="hidden" ' . $str . ' value="-99">';
		$i = 1;
		foreach ( $array as $key => $value ) {
			$key = trim ( $key );
			$checked = ($id && in_array ( $key, $id )) ? 'checked' : '';
			if ($width) $string .= '<label class="ib" style="width:' . $width . 'px">';
			$string .= '<input type="checkbox" ' . $str . ' id="' . $field . '_' . $i . '" ' . $checked . ' value="' . htmlspecialchars ( $key ) . '"> ' . htmlspecialchars ( $value );
			if ($width) $string .= '</label>';
			$i ++;
		}
		return $string;
	}

	/**
	 * 单选框
	 *
	 * @param $array 选项 二维数组
	 * @param $id 默认选中值
	 * @param $str 属性
	 */
	public static function radio($array = array(), $id = 0, $str = '', $width = 0, $field = '') {
		$string = '';
		foreach ( $array as $key => $value ) {
			$checked = trim ( $id ) == trim ( $key ) ? 'checked' : '';
			if ($width) $string .= '<label class="ib" style="width:' . $width . 'px">';
			$string .= '<input type="radio" ' . $str . ' id="' . $field . '_' . htmlspecialchars ( $key ) . '" ' . $checked . ' value="' . $key . '"> ' . $value;
			if ($width) $string .= '</label>';
		}
		return $string;
	}

	/**
	 * 验证码
	 *
	 * @param string $id 生成的验证码ID
	 * @param integer $code_len 生成多少位验证码
	 * @param integer $font_size 验证码字体大小
	 * @param integer $width 验证图片的宽
	 * @param integer $height 验证码图片的高
	 * @param string $font 使用什么字体，设置字体的URL
	 * @param string $font_color 字体使用什么颜色
	 * @param string $background 背景使用什么颜色
	 */
	public static function checkcode($id = 'checkcode', $code_len = 4, $width = 150, $height = 38, $background = '') {
		return "<img id='$id' style=\"cursor:pointer;\" onclick='this.src=this.src+\"&\"+Math.random()' src='" . SITE_URL . "api.php?controller=checkcode&code_len=$code_len&width=$width&height=$height&background=" . urlencode ( $background ) . "'>";
	}

	/**
	 * 日期时间控件
	 *
	 * @param $name 控件name，id
	 * @param $value 选中值
	 * @param $isdatetime 是否显示时间
	 * @param $loadjs 是否重复加载js，防止页面程序加载不规则导致的控件无法显示
	 * @param $showweek 是否显示周，使用，true | false
	 */
	public static function date($name, $value = '', $isdatetime = 0, $loadjs = 0, $showweek = 'true') {
		if ($value == '0000-00-00 00:00:00') $value = '';
		$id = preg_match ( "/\[(.*)\]/", $name, $m ) ? $m [1] : $name;
		if ($isdatetime) {
			$size = 21;
			$format = '%Y-%m-%d %H:%M:%S';
			$showsTime = 12;
		} else {
			$size = 10;
			$format = '%Y-%m-%d';
			$showsTime = 'false';
		}
		$str = '';
		if ($loadjs || ! defined ( 'CALENDAR_INIT' )) {
			define ( 'CALENDAR_INIT', 1 );
			$str .= '<link rel="stylesheet" type="text/css" href="' . JS_PATH . 'calendar/css/jscal2.css"/>
	<link rel="stylesheet" type="text/css" href="' . JS_PATH . 'calendar/css/border-radius.css"/>
	<link rel="stylesheet" type="text/css" href="' . JS_PATH . 'calendar/css/win2k/win2k.css"/>
	<script type="text/javascript" src="' . JS_PATH . 'calendar/jscal2.js"></script>
	<script type="text/javascript" src="' . JS_PATH . 'calendar/unicode-letter.js"></script>
	<script type="text/javascript" src="' . JS_PATH . 'calendar/lang/cn.js"></script>';
		}
		$str .= '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '" size="' . $size . '" class="date" readonly>&nbsp;';
		$str .= '<script type="text/javascript">
	Calendar.setup({
	weekNumbers: ' . $showweek . ',
	inputField : "' . $id . '",
	trigger    : "' . $id . '",
	dateFormat: "' . $format . '",
	showTime: ' . $showsTime . ',
	minuteStep: 1,
	onSelect   : function() {this.hide();}
	});
	</script>';
		return $str;
	}
}