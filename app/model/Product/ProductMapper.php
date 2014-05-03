<?php

namespace Product;

use LazyDataMapper\Suggestor,
	LazyDataMapper\DataHolder,
	\LazyDataMapper\SuggestorHelpers;

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


	public function getById($id, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$params = $suggestor->getSuggestions();
		$columns = SuggestorHelpers::wrapColumns($params);
		$statement = $this->pdo->prepare("SELECT $columns FROM product WHERE id = ?");
		$statement->execute([$id]);
		$data = array_intersect_key($statement->fetch(), array_flip($params));
		$holder->setData($data);
		return $holder;
	}


	public function getIdsByRestrictions(\LazyDataMapper\IRestrictor $restrictor, $limit = NULL)
	{
		list($conditions, $parameters) = $restrictor->getRestrictions();
		$statement = $this->pdo->prepare("SELECT id FROM product WHERE $conditions");
		$statement->execute($parameters);
		$ids = [];
		while ($id = $statement->fetchColumn()) {
			$ids[] = $id;
		}
		return $ids;
	}


	public function getByIdsRange(array $ids, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$params = $suggestor->getSuggestions();
		$columns = SuggestorHelpers::wrapColumns($params);
		$in = implode(',', array_fill(0, count($ids), '?'));
		$statement = $this->pdo->prepare("SELECT id, $columns FROM product WHERE id IN ($in)");
		$statement->execute($ids);
		$params = array_flip($params);
		while ($row = $statement->fetch()) {
			$data = array_intersect_key($row, $params);
			$holder->setData([$row['id'] => $data]);
		}
		return $holder;
	}


	public function save($id, DataHolder $holder)
	{
		$changes = $holder->getData();
		$columns = SuggestorHelpers::wrapColumns(array_keys($changes), '= ?');
		$statement = $this->pdo->prepare("UPDATE product SET $columns WHERE id = ?");
		$statement->execute(array_merge(array_values($changes), [$id]));
	}


	public function create(DataHolder $holder)
	{
		$data = $holder->getData();
		$columns = SuggestorHelpers::wrapColumns(array_keys($data));
		$values = implode(',', array_fill(0, count($data), '?'));
		$statement = $this->pdo->prepare("INSERT INTO product ($columns) VALUES($values)");
		$statement->execute(array_values($data));
		return (int) $this->pdo->query("SELECT LAST_INSERT_ID()")->fetchColumn();
	}


	public function remove($id)
	{
		$statement = $this->pdo->prepare('DELETE FROM product WHERE id = ? LIMIT 1');
		$statement->execute([$id]);
	}


	public function removeByIdsRange(array $ids)
	{
		$count = count($ids);
		$in = implode(',', array_fill(0, $count, '?'));
		$statement = $this->pdo->prepare("DELETE FROM product WHERE id IN ($in) LIMIT $count");
		$statement->execute($ids);
	}
}
