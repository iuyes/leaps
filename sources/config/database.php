<?php
/**
 * 数据库配置文件
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-5-13
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: database.php 2 2013-01-14 07:14:05Z xutongle $
 */
return array (
		'default' => array ( // 默认数据库配置
				'hostname' => 'localhost',
				'port'=>3306,
				'driver' => 'mysql',
				'database' => 'leaps_simple',
				'username' => 'root', // 数据库帐户
				'password' => '123456', // 数据库密码
				'charset' => 'utf8', // 数据库编码
				'prefix' => 'yun_', // 数据库前缀
				'pconnect' => true,
				'autoconnect'=>true,//自动连接数据库
		),
		'mssql'=>array(
				'hostname' => '192.168.1.11',
				'port'=>1433,
				'driver' => 'mssql',
				'database' => 'nxt',
				'username' => 'sa', // 数据库帐户
				'password' => 'caitong12316', // 数据库密码
				'charset' => 'utf8', // 数据库编码
				'prefix' => 'yun_', // 数据库前缀
				'pconnect' => true,
				'autoconnect'=>true,//自动连接数据库
			),
);