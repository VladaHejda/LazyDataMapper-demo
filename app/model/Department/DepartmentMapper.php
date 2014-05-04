<?php

namespace Department;

use LazyDataMapper\Suggestor,
	LazyDataMapper\DataHolder,
	\LazyDataMapper\SuggestorHelpers;

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
		return (bool) $this->db->fetchField('SELECT 1 FROM department WHERE id = ?', $id);
	}


	public function getById($id, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$params = $suggestor->getSuggestions();
		$columns = SuggestorHelpers::wrapColumns($params);
		$data = $this->db->fetch("SELECT $columns FROM department WHERE id = ?", $id);
		$holder->setData(iterator_to_array($data));
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

		$result = $this->db->queryArgs("SELECT id, $columns FROM department WHERE id IN ($in)", $ids)->fetchAll();
		$data = [];
		foreach ($result as &$subdata) {
			$data[$subdata->id] = (array) $subdata;
		}
		$holder->setData($data);
		return $holder;
	}


	public function getAllIds($limit = NULL)
	{
		$ids = $this->db->fetchAll("SELECT id FROM department" . ($limit ? " LIMIT $limit" : ''));
		foreach ($ids as &$id) {
			$id = $id->id;
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
