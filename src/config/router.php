<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-12-10
 * @copyright Copyright (c) 2003-2103 www.tintsoft.com
 * @version $Id: router.php 556 2013-05-17 06:20:15Z 85825770@qq.com $
 */
return array (
		'default' => array ('application' => 'content','controller' => 'index','action' => 'init','data' => array ('POST' => array ('catid' => 1 ),'GET' => array ('contentid' => 1 ) ) ),
		'cli' => array ('controller' => 'index','action' => 'init' ),
);