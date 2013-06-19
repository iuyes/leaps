<?php
/**
 *
 *
 * Observer.php class file.
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @link http://www.tintsoft.com/
 * @copyright Copyright &copy; 2009-2013 TintSoft Development Co., LTD
 * @license http://www.tintsoft.com/html/about/copyright/
 * @version $Id$
 */
class Base_Observer implements SplObserver {

	private $config = array (), $dir = null;
	private static $plugin;

	public function __construct($dir) {
		$this->dir = $dir;
		$config = include ($dir . 'config.php');
		foreach ( $config as $file => $events ) {
			foreach ( $events as $event ) {
				$this->config [$event] [] = $file;
			}
		}
	}

	public function update(SplSubject $subject) {
		$event = $subject->event;
		foreach ( $this->config [$event] as $file ) {
			if (! isset ( self::$plugin [$file] )) {
				require_once ($this->dir . $file . '.php');
				$class = 'plugin_' . $file;
				self::$plugin [$file] = new $class ( $subject );
			}
			self::$plugin [$file]->$event ();
		}
	}
}