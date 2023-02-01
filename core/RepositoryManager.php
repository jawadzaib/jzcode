<?php
namespace Core; 

class RepositoryManager {
	public static $entityManager;
	public static function initialize($em) {
		self::$entityManager = $em;
	}
	public static function getRepository($model) {
		$modelName = 'App\Models\\'.$model;
		$table_name = self::$entityManager->getClassMetadata($modelName)->getTableName();
		$repository = self::$entityManager->getRepository($modelName);
		
		if(method_exists($repository, 'initialize')) {
			$repository->initialize($table_name);
		}
		$repository->table_name = $table_name;
		return $repository;
	}
	public static function save($model) {
		self::$entityManager->persist($model);
		self::$entityManager->flush();
	}
	public static function query($sql, $bind = []) {
		return self::$entityManager->createQuery($sql);
	}
}