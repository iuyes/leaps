<?php
define ( 'WEKIT_PATH', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR );
require WEKIT_PATH . '../framework/framework.php';
$directory = array ();
foreach ($directory as $namespace => $path) {
	$realpath = realpath(WEKIT_PATH . $path);
	Core::register($realpath, $namespace);
	define($namespace . '_PATH', $realpath . DIRECTORY_SEPARATOR);
}
Core::register(WEKIT_PATH, 'WEKIT');
class Wekit {
}