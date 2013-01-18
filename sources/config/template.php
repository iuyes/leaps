<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-12
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: template.php 2 2013-01-14 07:14:05Z xutongle $
 */
return array(
		'name' => 'default',
		'edit'=>true,
		'ext'=>'.html',
		'referesh'=>true,

		/**
		 * 可选参数是 file system 默认是file。system由系统缓存流接管，性能有影响，如本地目录支持读写建议使用file缓存，
		 * 使用封装的缓存流跟原生file速度相差近4倍，此选项是为了兼容本地不支持读写的环境如SAE云计算平台或百度云计算平台
		 */
		'cache'=>'file',
);