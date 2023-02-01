<?php 
namespace Core;

use Core\DB;
use Core\Input;

class Validate {
	private $_passed = false, $_errors = [], $_db = null;

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function check($source, $items) {
		$this->_errors = [];
		foreach ($items as $item => $rules) {
			$item = Input::sanitize($item);
			$display = isset($rules['display']) ? $rules['display'] : $item;
			foreach ($rules as $rule => $rule_value) {
				if(isset($source[$item])) {
					$value = Input::sanitize(trim($source[$item]));

					if($rule === 'required' && $rule_value == true && empty($value)) {
						$this->addError(["{$display} is required", $item]);
					} else if(!empty($value)) {
						switch ($rule) {
							case 'min':
								if(strlen($value) < $rule_value) {
									$this->addError(["{$display} must be a minimum of {$rule_value} characters.", $item]);
								}
								break;
							case 'max':
								if(strlen($value) > $rule_value) {
									$this->addError(["{$display} must be a maximum of {$rule_value} characters.", $item]);
								}
								break;
							case 'matches':
								if($value != $source[$rule_value]) {
									$matchDisplay = $items[$rule_value]['display'];
									$this->addError(["{$matchDisplay} and {$display} must match.", $item]);
								}
								break;
							case 'unique':
								$query = $this->_db->query("SELECT {$item} FROM {$rule_value} WHERE {$item} = ?", [$value]);
								if($query->count()) {
									$this->addError(["{$display} already exists. Please choose another {$display}", $item]);
								}
								break;
							case 'unique_update':
								$t = explode('|', $rule_value);
								$table = $t[0];
								$id = $t[1];
								$query = $this->_db->query("SELECT {$item} FROM {$table} WHERE id != ? AND {$item} = ?", [$id, $value]);
								if($query->count()) {
									$this->addError(["{$display} already exists. Please choose another {$display}", $item]);
								}
								break;
							case 'is_numeric':
								if(!is_numeric($value)) {
									$this->addError(["{$display} has to be a number. Please use a numeric value.", $item]);
								}
								break;
							case 'valid_email':
								if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
									$this->addError(["{$display} must be a valid Email address", $item]);
								}
								break;
							default:
								# code...
								break;
						}
					}
				}
			}
		}
		if(empty($this->_errors)) {
			$this->_passed = true;
		}
	}

	public function addError($error) {
		$this->_errors[] = $error;
		$this->_passed = (empty($this->_errors)) ? true : false;
	}

	public function errors() {
		return $this->_errors;
	}

	public function passed() {
		return $this->_passed;
	}

	public function displayErrors() {
		$html = '';
		if(!empty($this->_errors)) {
			$html .= '<ul class="bg-danger">';
			foreach ($this->_errors as $error) {
				if(is_array($error)) {
					$html .= '<li class="">'.$error[0].'</li>';
				} else {
					$html .= '<li class="">'.$error.'</li>';
				}
			}
			$html .= '</ul>';
		}
		return $html;
	}


}