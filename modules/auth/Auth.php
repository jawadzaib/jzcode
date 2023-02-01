<?php 
namespace Auth;

/*
* Module: Auth
* Author:	JZCoding
* Author URI:	http://jzcoding.com
*/

use Core\Module;

class Auth extends Module {
	public function initialize() {
		/*
		* Register shortcodes here
		* Register stylesheets and scripts
		*/
		$this->registerAction('register_scripts', array($this, 'load_scripts_callback'));
	}
	public function load_scripts_callback() {
		$this->registerStyle('auth_module_primary_style', $this->moduleUrl('css/screen.css'));
		$this->registerScript('auth_module_primary_script', $this->moduleUrl('js/primary.js'));
	}
}