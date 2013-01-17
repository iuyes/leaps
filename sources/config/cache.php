<?php
/**
 * 缓存配置
 */
return array (
		'default' => array (
				'driver' => 'Memcache',//缓存存储
				'expire'=>'0',
				'servers' =>
				array(//多个主机书写方式
						array('host' => '127.0.0.1','port'=>11211)
				),
		),
		'apc' => array (
				'driver' => 'Apc',//缓存存储
		),
		'eaccelerator' => array (
				'driver' => 'Eaccelerator',//缓存存储
		),
		'wincache' => array (
				'driver' => 'Wincache',//缓存存储
		),
		'xcache' => array (
				'driver' => 'Xcache',//缓存存储
		),
		'xcache' => array (
				'driver' => 'Xcache',//缓存存储
				'user'=>'',
				'pwd'=>''
		),
		'zendcache' => array (
				'driver' => 'Zendcache',//缓存存储
		),
		'dbcache' => array (
				'driver' => 'Dbcache',//缓存存储
		)

);