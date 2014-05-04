<?php

namespace Department;

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
		$statement = $this->pdo->prepare('SELECT 1 FROM department WHERE id = ?');
		$statement->execute([$id]);
		return (bool) $statement->fetchColumn();
	}


	public function getById($id, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$params = $suggestor->getSuggestions();
		$columns = SuggestorHelpers::wrapColumns($params);
		$statement = $this->pdo->prepare("SELECT $columns FROM department WHERE id = ?");
		$statement->execute([$id]);
		$holder->setData($statement->fetch());
		return $holder;
	}


	public function getIdsByRestrictions(\LazyDataMapper\IRestrictor $restrictor, $limit = NULL)
	{
	}


	public function getByIdsRange(array $ids, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$params = $suggestor->getSuggestions();
		$columns = SuggestorHelpers::wrapColumns($params);
		$in = implode(',', array_fill(0, count($ids), '?'));
		$statement = $this->pdo->prepare("SELECT id, $columns FROM department WHERE id IN ($in)");
		$statement->execute($ids);
		$holder->setIdSource('id');
		while ($row = $statement->fetch()) {
			// todo set it as $holder->setData($row);
			$holder->setData([$row]);
		}
		return $holder;
	}


	public function getAllIds($limit = NULL)
	{
		$statement = $this->pdo->query("SELECT id FROM department" . ($limit ? " LIMIT $limit" : ''));
		$ids = [];
		foreach ($statement->fetchAll() as $row) {
			$ids[] = $row['id'];
		}
		return $ids;
	}


	public function save($id, DataHolder $holder)
	{
	}


	public function create(DataHolder $holder)
	{
	}


	public function remove($id)
	{
	}


	public function removeByIdsRange(array $ids)
	{
	}
}
