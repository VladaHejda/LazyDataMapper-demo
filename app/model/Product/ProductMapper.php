<?php

namespace Product;

use LazyDataMapper\Suggestor;
use LazyDataMapper\DataHolder;

class Mapper implements \LazyDataMapper\IMapper
{

	/** @var \Nette\Database\Connection */
	private $db;


	public function __construct(\Nette\Database\Connection $db)
	{
		$this->db = $db;
	}


	public function exists($id)
	{
		return (bool) $this->db->fetchField('SELECT 1 FROM product WHERE id = ?', $id);
	}


	public function getById($id, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$params = $suggestor->getSuggestions();
		$columns = implode(', ', $params);
		$data = $this->db->fetch("SELECT $columns FROM product WHERE id = ?", $id);
		$holder->setData(iterator_to_array($data));
		return $holder;
	}


	public function getIdsByRestrictions(\LazyDataMapper\IRestrictor $restrictor, $limit = NULL)
	{
		list($conditions, $parameters) = $restrictor->getRestrictions();
		$ids = $this->db->queryArgs("SELECT id FROM product p WHERE $conditions", $parameters)->fetchAll();
		foreach ($ids as &$id) {
			$id = $id->id;
		}
		return $ids;
	}


	public function getByIdsRange(array $ids, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$params = $suggestor->getSuggestions();
		$columns = implode(', ', $params);
		$in = implode(', ', array_fill(0, count($ids), '?'));
		$result = $this->db->queryArgs("SELECT id, $columns FROM product WHERE id IN ($in)", $ids)->fetchAll();
		$data = [];
		foreach ($result as &$subdata) {
			$data[$subdata->id] = (array) $subdata;
		}
		return $holder->setData($data);
	}


	public function getAllIds($maxCount = NULL)
	{
	}


	public function save($id, DataHolder $holder)
	{
		$changes = $holder->getData();
		$columns = implode(' = ?, ', array_keys($changes)) . ' = ?';
		$this->db->queryArgs("UPDATE product SET $columns WHERE id = ?", array_merge($changes, [$id]));
	}


	public function create(DataHolder $holder)
	{
		$data = $holder->getData();
		$columns = implode(', ', array_keys($data));
		$values = implode(', ', array_fill(0, count($data), '?'));
		$this->db->queryArgs("INSERT INTO product ($columns) VALUES($values)", $data);
		return (int) $this->db->getInsertId();
	}


	public function remove($id)
	{
		$this->db->query('DELETE FROM product WHERE id = ? LIMIT 1', $id);
	}


	public function removeByIdsRange(array $ids)
	{
		$count = count($ids);
		$in = implode(', ', array_fill(0, $count, '?'));
		$this->db->queryArgs("DELETE FROM product WHERE id IN ($in) LIMIT $count", $ids);
	}
}
