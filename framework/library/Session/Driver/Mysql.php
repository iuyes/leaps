<?php
/**
 * Session Mysql驱动
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-14
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: Mysql.php 604 2013-06-07 07:01:12Z 85825770@qq.com $
 */
class Session_Driver_Mysql extends Session_Abstract {
	public $lifetime = 1800;
	private $db;

	/**
	 * 构造函数
	 */
	public function __construct($options = array()) {
		$this->db = Loader::model ( 'session_model' );
		$this->lifetime = $options ['maxlifetime'];
		$this->register ();
	}

	public function register() {
		session_set_save_handler ( array (&$this,'open' ), array (&$this,'close' ), array (&$this,'read' ), array (&$this,'write' ), array (&$this,'destroy' ), array (&$this,'gc' ) );
	}

	/**
	 * session_set_save_handler open方法
	 *
	 * @param
	 *        	$save_path
	 * @param
	 *        	$session_name
	 * @return true
	 */
	public function open($save_path, $session_name) {
		return true;
	}

	/**
	 * session_set_save_handler close方法
	 *
	 * @return bool
	 */
	public function close() {
		return $this->gc ( $this->lifetime );
	}

	/**
	 * 读取session_id
	 * session_set_save_handler read方法
	 *
	 * @return string 读取session_id
	 */
	public function read($id) {
		$res = $this->db->where ( array ('sessionid' => $id ) )->field ( 'data' )->find ();
		return $res ? $res['data'] : '';
	}

	/**
	 * 写入session_id 的值
	 *
	 * @param $id session
	 * @param $data 值
	 * @return mixed query 执行结果
	 */
	public function write($id, $data) {
		$uid = isset ( $_SESSION ['userid'] ) ? $_SESSION ['userid'] : 0;
		$roleid = isset ( $_SESSION ['roleid'] ) ? $_SESSION ['roleid'] : 0;
		$groupid = isset ( $_SESSION ['groupid'] ) ? $_SESSION ['groupid'] : 0;
		$application = defined ( 'APP' ) ? APP : '';
		$controller = defined ( 'CONTROLLER' ) ? CONTROLLER : '';
		$action = defined ( 'ACTION' ) ? ACTION : '';
		if (strlen ( $data ) > 255) $data = '';
		$sessiondata = array ('sessionid' => $id,'userid' => $uid,'ip' => IP,'lastvisit' => TIME,'roleid' => $roleid,'groupid' => $groupid,'application' => $application,'controller' => $controller,'action' => $action,'data' => $data );
		return $this->db->insert ( $sessiondata, true ,true);
	}

	/**
	 * 删除指定的session_id
	 *
	 * @param $id session
	 * @return bool
	 */
	public function destroy($id) {
		return $this->db->where ( array ('sessionid' => $id ) )->delete ();
	}

	/**
	 * 删除过期的 session
	 *
	 * @param $maxlifetime 存活期时间
	 * @return bool
	 */
	public function gc($maxlifetime) {
		$expiretime = TIME - $maxlifetime;
		return $this->db->where ( array ('lastvisit' => array ('lt',$expiretime ) ) )->delete ();
	}
}