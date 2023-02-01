<?php 
namespace Core;

use PDO;

class DB {
	private static $_instance = null;
	private $_pdo, $_query, $_error = false, $_result, $_count = 0, $_lastInsertID = null;
	private $_table;
	private $_select_params;
	private $_joins = [];
	private $_symbols = ['=', '!=', '>', '<', '>=', '<='];
	private $_where = [];

	public function __construct() {
		try {
			$this->_pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}

	public static function getInstance() {
		if(!isset(self::$_instance)) {
			self::$_instance = new DB();
		}
		return self::$_instance;
	}

	public function query($sql, $params = []) {
		$this->_error = false;
		if($this->_query = $this->_pdo->prepare($sql)) {
			$x = 1;
			if(count($params)) {
				foreach ($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			if($this->_query->execute()) {
				$this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
				$this->_lastInsertID = $this->_pdo->lastInsertId();
			} else {
				$this->_error = true;
			}
		}
		return $this;
	}

	public function select($fields = '*') {
		$this->_select_params = $fields;
		return $this;
	}
	public function from($table_name) {
		$this->_table = $table_name;
		return $this;
	}
	public function leftJoin($table, $condition = '') {
		array_push($this->_joins, array(
			'type' => 'left',
			'table' => $table,
			'condition' => $condition
		));
		return $this;
	}
	public function innerJoin($table, $condition = '') {
		array_push($this->_joins, array(
			'type' => 'inner',
			'table' => $table,
			'condition' => $condition
		));
		return $this;
	}
	public function where($field, $symbol = '', $value = '') {
		if(!in_array($symbol, $this->_symbols)) {
			$value = $symbol;
			$symbol = '=';
		}
		array_push($this->_where, array(
			'field' => $field,
			'symbol' => $symbol,
			'value' => $value
		));
		return $this;
	}
	public function begin() {}
	public function end() {}
	public function orWhere() {}
	public function inWhere() {}
	public function like() {}
	public function orLike() {}
	public function orderBy($field, $method = 'ASC') {
		return $this;
	}
	public function limit($limit = null, $start = 0) {
		return $this;
	}

	public function get($table = false) {
		if($table) {
			$this->_table = $table;
		}
		// construct sql statement
		return $this;
	} 

	protected function _read($table, $params = []) {
		$conditionsString = '';
		$bind = [];
		$order = '';
		$limit = '';

		// conditions
		if(isset($params['conditions'])) {
			if(is_array($params['conditions'])) {
				foreach ($params['conditions'] as $condition) {
					$conditionsString .= ' '.$condition.' AND';
				}
				$conditionsString = rtrim(trim($$conditionsString), ' AND');
			} else {
				$conditionsString = $params['conditions'];
			}
			if($conditionsString != '') {
				$conditionsString = ' WHERE '.$conditionsString;
			}
		}

		// bind
		if(array_key_exists('bind', $params)) {
			$bind = $params['bind'];
		}

		// order
		if(array_key_exists('order', $params)) {
			$order = ' ORDER BY '.$params['order'];
		}

		// limit
		if(array_key_exists('limit', $params)) {
			$limit = ' LIMIT '.$params['limit'];
		}

		$sql = "SELECT * FROM {$table}{$conditionsString}{$order}{$limit}";
		if($this->query($sql, $bind)) {
			if(!$this->_result || !count($this->_result)) return false;
			return true;
		}
		return false;
	}

	public function find($table, $params = []) {
		if($this->_read($table, $params)) {
			return $this->results();
		}
		return false;
	}

	public function findFirst($table, $params = []) {
		if($this->_read($table, $params)) {
			return $this->first();
		}
		return false;
	}

	public function insert($table, $fields = []) {
		$fieldsString = '';
		$valuesString = '';
		$values = [];
		foreach ($fields as $field => $value) {
			$fieldsString .= '`'.$field.'`,';
			$valuesString .= '?,';
			$values[] = $value;
		}
		$fieldsString = rtrim($fieldsString, ',');
		$valuesString = rtrim($valuesString, ',');
		$sql = "INSERT INTO {$table} ({$fieldsString}) VALUES({$valuesString})";
		if(!$this->query($sql, $values)->error()) {
			return true;
		}
		return false;
	}

	public function update($table, $id, $fields = []) {
		$fieldsString = '';
		$values = [];
		foreach ($fields as $field => $value) {
			$fieldsString .= ' `'.$field.'` = ?,';
			$values[] = $value;
		}
		$fieldsString = rtrim(trim($fieldsString), ',');
		$sql = "UPDATE {$table} SET {$fieldsString} WHERE `id` = {$id}";
		if(!$this->query($sql, $values)->error()) {
			return true;
		}
		return false;
	}

	public function delete($table, $id) {
		$sql = "DELETE FROM {$table} WHERE `id` = {$id}";
		if(!$this->query($sql)->error()) {
			return true;
		}
		return false;
	}

	public function results() {
		return $this->_result;
	}

	public function first() {
		return (!empty($this->_result)) ? $this->_result[0] : [];
	}

	public function count() {
		return $this->_count;
	}

	public function lastID() {
		return $this->_lastInsertID;
	}

	public function get_columns($table) {
		return $this->query("SHOW COLUMNS FROM {$table}")->results();
	}

	public function error() {
		return $this->_error;
	}
}