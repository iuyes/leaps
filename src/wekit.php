<?php
/**
 *
 * wekit.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */

define ( 'WEKIT_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
define('APPS_PATH', WEKIT_PATH.'apps'.DIRECTORY_SEPARATOR);
define ( 'DATA_PATH', WEKIT_PATH . 'data' . DIRECTORY_SEPARATOR );
require WEKIT_PATH . '../framework/kernel.php';