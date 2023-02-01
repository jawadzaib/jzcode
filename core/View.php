<?php 
namespace Core;

use Core\Configuration;
use Core\Hooks;
use Core\ShortCode;

class View {
	private $_configuration;
	protected $_head, $_body, $_footer, $_siteTitle = SITE_TITLE, $_outputBuffer, $_layout = DEFAULT_LAYOUT;
	public $layout_url = null;
	public $module = null;
	

	public function __construct() {
		$this->_layout = DEFAULT_LAYOUT;
		$this->layout_url = PROOT.'_layouts/'.$this->_layout.'/';
		$this->_configuration = new Configuration();
		Hooks::fireAction('register_scripts');
	}

	public function getMenu($name) {
		return $this->_configuration->getMenu($name);
	}

	public function render($viewName) {
		$viewArray = explode('/', $viewName);
		$viewString = implode(DS, $viewArray);
		$view_root_path = ROOT.DS.'app'.DS.'views';
		if($this->module) {
			$view_root_path = ROOT.DS.'modules'.DS.$this->module->name.DS.'views';
		}
		if(file_exists($view_root_path.DS.$viewString.'.php')) {
			ob_start();
			$this->loadTemplate($view_root_path.DS.$viewString.'.php');
			$view_content = ob_get_clean();
			if(!$this->_head) {
				$this->start('head');
				$this->end();
			}
			if(!$this->_body) {
				$this->start('body');
				echo $view_content;
				$this->end();
			} else {
				echo $view_content;
			}
			if(!$this->_footer) {
				$this->start('footer');
				$this->end();
			}
			$this->loadTemplate(ROOT.DS.'app'.DS.'views'.DS.'layouts'.DS.$this->_layout.'/index.php');
		} else {
			die('The view \"'.$viewName.'\" does not exist.');
		}
	}
	public function loadTemplate($viewName) {
		include $viewName;
	}

	public function content($type) {
		if($type == 'head') {
			return $this->_head;
		} else if($type == 'body') {
			$this->applyShortCodes();
			return $this->_body;
		} else if($type == 'footer') {
			return $this->_footer;
		}
		return false;
	}

	private function applyShortCodes() {
		if($this->_body) {
			$codes = ShortCode::getAll();
			if(!empty($codes)) {
				foreach ($codes as $key => $value) {
					// [my_search_form | field1:val1;field2:some thing]
					$index = strpos($this->_body, '['.$key);
					if($index > -1) {
						$code = '';
						do {
							$ch = substr($this->_body, $index, 1);
							$code .= $ch;
							$index++;
						} while($ch != ']');
						$code_string = ltrim($code, '[');
						$code_array = explode('|', $code_string);
						$code_string = rtrim(trim($code_array[0]), ']');
						array_shift($code_array);
						$code_params = array();
						if(isset($code_array[0])) {
							$params = explode(';', rtrim($code_array[0], ']'));
							if(!empty($params)) {
								foreach ($params as $param) {
									$param = explode(':', trim($param));
									if(isset($param[0])) {
										$code_params[trim($param[0])] = null;
										if(isset($param[1])) {
											$code_params[trim($param[0])] = trim($param[1]);
										}
									}
								}
							}
						}
						ob_start();
						ShortCode::fire($code_string, $code_params);
						$code_html = ob_get_clean();
						$this->_body = str_replace($code, $code_html, $this->_body);
					}
				}
			}
		}
	}
	private function applyShortCodesWithoutParams() {
		if($this->_body) {
			$codes = ShortCode::getAll();
			if(!empty($codes)) {
				foreach ($codes as $key => $value) {
					$code = '['.$key.']';
					// [my_search_form]
					if(strpos($this->_body, $code) > -1) {
						ob_start();
						ShortCode::fire($key);
						$code_html = ob_get_clean();
						$this->_body = str_replace($code, $code_html, $this->_body);
					}
				}
			}
		}
	}

	public function start($type) {
		$this->_outputBuffer = $type;
		ob_start();
	}
	public function end() {
		if($this->_outputBuffer == 'head') {
			if($this->module) {
				Hooks::displayStyles($this->module->name);
			}
			Hooks::displayStyles();
			if($this->module) {
				Hooks::displayScripts(true, $this->module->name);
			}
			Hooks::displayScripts(true);
			$this->_head = ob_get_clean();
		} else if($this->_outputBuffer == 'body') {
			$this->_body = ob_get_clean();
		} else if($this->_outputBuffer == 'footer') {
			if($this->module) {
				Hooks::displayScripts(false, $this->module->name);
			}
			Hooks::displayScripts();
			$this->_footer = ob_get_clean();
		} else {
			die('You must first run start method.');
		}
	}

	public function siteTitle() {
		return $this->_siteTitle;
	}
	public function setSiteTitle($title) {
		$this->_siteTitle = $title;
	}

	public function setLayout($layout) {
		$this->_layout = $layout;
		$this->layout_url = PROOT.'_layouts/'.$this->_layout.'/';
	}
}