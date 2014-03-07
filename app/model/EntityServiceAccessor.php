<?php

namespace LDMDemo;

class EntityServiceAccessor extends \LazyDataMapper\EntityServiceAccessor
{

	/** @var \PDO */
	protected $pdo;


	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}


	public function getEntityClass(\LazyDataMapper\Facade $facade)
	{
		return substr(get_class($facade), 0, -7);
	}


	protected function getParamMapClass($entityClass)
	{
		return $entityClass . '\ParamMap';
	}


	protected function getMapperClass($entityClass)
	{
		return $entityClass . '\Mapper';
	}


	protected function getCheckerClass($entityClass)
	{
		return $entityClass . '\Checker';
	}


	protected function createMapper($mapper)
	{
		return new $mapper($this->pdo);
	}
}
