<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-14
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: session.php 556 2013-05-17 06:20:15Z 85825770@qq.com $
 */
return array (
		//Session驱动
		'driver' => 'file',

		//Session 公共配置
		'maxlifetime' => 1440,
		'cache_expire' => '30',
		'cookie_lifetime' => '0',
		'cookie_domain' => '',
		'cookie_path' => '/',

		//session 文本保存相关配置
		'session_n'=>0,
		'session_path' => DATA_PATH.'session/',

		//Session memcache保存相关设置
		'memcache_servers' => 'tcp://127.0.0.1:11211?persistent=1&weight=2&timeout=2&retry_interval=10',

);