<?php 
namespace Core;

use Core\DB;

class Service {
	protected $_db;
	public function __construct() {
		$this->_db = DB::getInstance();
	}
}