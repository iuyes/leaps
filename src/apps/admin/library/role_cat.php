<?php
/**
 * 角色栏目权限
 * @author Tongle Xu <xutongle@gmail.com> 2012-11-5
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: role_cat.php 34 2012-11-05 11:45:21Z xutongle $
 */
class role_cat {
	static $db;
	private static function _connect() {
		self::$db = Loader::model ( 'category_priv_model' );
	}

	/**
	 * 获取角色配置权限
	 *
	 * @param integer $roleid
	 *        	角色ID
	 */
	public static function get_roleid($roleid) {
		if (empty ( self::$db )) self::_connect ();
		if ($data = self::$db->where ( array ('roleid' => $roleid,'is_admin' => 1 ) )->select ()) {
			$priv = array ();
			foreach ( $data as $k => $v ) {
				$priv [$v ['catid']] [$v ['action']] = true;
			}
			return $priv;
		} else
			return false;
	}

	/**
	 * 获取站点栏目列表
	 *
	 * @return array() 返回为数组
	 */
	public static function get_category() {
		$category = S ( 'common/category_content' );
		foreach ( $category as $k => $v ) {
			if (! in_array ( $v ['type'], array (0,1 ) )) unset ( $category [$k] );
		}
		return $category;
	}

	/**
	 * 更新数据库信息
	 *
	 * @param integer $roleid
	 *        	角色ID
	 * @param array $data
	 *        	需要更新的数据
	 */
	public static function updata_priv($roleid, $data) {
		if (empty ( self::$db )) self::_connect ();
		// 删除该角色当前的权限
		self::$db->where(array ('roleid' => $roleid,'is_admin' => 1 ))->delete (  );
		foreach ( $data as $k => $v ) {
			if (is_array ( $v ) && ! empty ( $v [0] )) {
				foreach ( $v as $key => $val ) {
					self::$db->insert ( array ('catid' => $k,'is_admin' => 1,'roleid' => $roleid,'action' => $val ) );
				}
			}
		}
	}
}