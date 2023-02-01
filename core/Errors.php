<?php 
namespace Core;

class Errors {
	private $_name = null;


	public function index() {
		$args = func_get_args();
		$error_type = isset($args[0]) ? $args[0] : '';
		$file_name = '';
		switch ($error_type) {
			case '403':
				$file_name = '403.php';
				break;
			case '404':
				$file_name = '404.php';
				break;
			case '401':
				$file_name = '401.php';
				break;
			default:
				# code...
				break;
		}
		if($file_name) {
			include (ROOT.DS.'app'.DS.'views'.DS.'errors'.DS.$file_name);
		}
	}
}