<?php 
namespace Core;

class Layouts {
	private $_name = null;


	public function index() {
		$args = func_get_args();
		$file_path = ROOT.DS.'app'.DS.'views'.DS.'layouts'.DS.implode(DS, $args);
		if(file_exists($file_path)) {
			$path_parts = pathinfo($file_path);


			$extension = isset($path_parts['extension']) ? $path_parts['extension'] : null;
			if($extension) {
				switch ($extension) {
					case 'css':
	                    header('Content-type: text/css');
	                    break;

	                case 'js':
	                    header('Content-type: text/javascript');
	                    break;
	                
	                case 'json':
	                    header('Content-type: application/json');
	                    break;
	                
	                case 'xml':
	                   header('Content-type: text/xml');
	                    break;
	                
	                case 'pdf':
	                  header('Content-type: application/pdf');
	                    break;
	                
	                case 'jpg' || 'jpeg' || 'png' || 'gif':
	                    header('Content-type: image/'.$extension);
	                    // header('Content-type: image/'.$file_type);
	                    break;
				}
			}

			include $file_path;
			exit();
		}
	}
}