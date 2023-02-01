<?php 
namespace Core;

use Core\DB;
use Core\RepositoryManager;
use Doctrine\ORM\EntityRepository;


class Repository extends EntityRepository {
	protected $_db;
	private $_table, $_softDelete;

	public function initialize($table_name = null) {
		$this->_db = DB::getInstance();
		$this->_table = $table_name;
	}

	public function getRecords($params = []) {
		$results = [];
		$resultQuery = $this->_db->find($this->_table, $params);
		return $resultQuery;
	}
	public function getFirstRecord($params = []) {
		dnd($params);
		$resultQuery = $this->_db->findFirst($this->_table, $params);
		dnd($resultQuery);
		$result = new $this->_modelName();
		if(!empty($resultQuery)) {
			$result->populateObjData($resultQuery);
		}
		return $result;
	}
	public function getRecordById($id) {
		return $this->findFirst(['conditions' => "id = ?", 'bind' => [$id]]);
	}
	
	public function save($model) {
		RepositoryManager::save($model);
	}

	public function assign($params) {
		if(!empty($params)) {
			foreach ($params as $key => $value) {
				if(in_array($key, $this->_columnNames)) {
					$this->$key = sanitize($value);
				}
			}
			return true;
		}
		return false;
	}
	public function insertRecord($fields) {
		if(empty($fields)) return false;
		return $this->_db->insert($this->_table, $fields);
	}

	public function updateRecord($id, $fields) {
		if(empty($fields) || $id == '') return false;
		return $this->_db->update($this->_table, $id, $fields);
	}

	public function deleteRecord($id = '') {
		if($id == '' && $this->id == '') return false;
		$id = ($id == '') ? $this->id : $id;
		if($this->_softDelete) {
			return $this->update($id, ['deleted' => 1]);
		}
		return $this->_db->delete($this->_table, $id);
	}

	
}