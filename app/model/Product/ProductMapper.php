<?php

namespace Product;

use LazyDataMapper\ISuggestor,
	LazyDataMapper\IDataHolder,
	LazyDataMapper\DataHolder;

class Mapper implements \LazyDataMapper\IMapper
{

	/** @var \PDO */
	private $pdo;


	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}


	public function exists($id)
	{
		$statement = $this->pdo->prepare('SELECT 1 FROM product WHERE id = ?');
		$statement->execute([$id]);
		return (bool) $statement->fetchColumn();
	}


	public function getById($id, ISuggestor $suggestor)
	{
		$params = $suggestor->getParamNames();
		$columns = '`' . implode('`,`', $params) . '`';
		$statement = $this->pdo->prepare("SELECT $columns FROM product WHERE id = ?");
		$statement->execute([$id]);
		$data = array_intersect_key($statement->fetch(), array_flip($params));
		$holder = new DataHolder($suggestor);
		$holder->setParams($data);
		return $holder;
	}


	public function getIdsByRestrictions(\LazyDataMapper\IRestrictor $restrictor)
	{
	}


	public function getByIdsRange(array $ids, ISuggestor $suggestor)
	{
		$params = $suggestor->getParamNames();
		$columns = '`' . implode('`,`', $params) . '`';
		$in = implode(',', array_fill(0, count($ids), '?'));
		$statement = $this->pdo->prepare("SELECT id, $columns FROM product WHERE id IN ($in)");
		$statement->execute($ids);
		$holder = new DataHolder($suggestor, $ids);
		$params = array_flip($params);
		while ($row = $statement->fetch()) {
			$data = array_intersect_key($row, $params);
			$holder->setParams([$row['id'] => $data]);
		}
		return $holder;
	}


	public function save($id, IDataHolder $holder)
	{
	}


	public function create(IDataHolder $holder)
	{
	}


	public function remove($id)
	{
	}
}
