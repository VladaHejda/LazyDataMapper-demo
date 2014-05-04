<?php

namespace Department;

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
		return (bool) $this->db->fetchField('SELECT 1 FROM department WHERE id = ?', $id);
	}


	public function getById($id, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$join = '';

		$params = $suggestor->getSuggestions();
		$columns = 'd.' . implode(', d.', $params);

		if ($suggestor->products) {
			$params = $suggestor->products->getSuggestions();
			$columns .= ', p.id product_id, p.' . implode(', p.', $params);
			$columns = str_replace('p.name', 'p.name product_name', $columns);
			$join = "LEFT JOIN product p ON (d.id = p.department_id)";
		}

		$data = $this->db->fetchAll("SELECT $columns FROM department d $join WHERE d.id = ?", $id);
		foreach ($data as &$subdata) {
			$subdata = iterator_to_array($subdata);
			if (!$holder->isDataLoaded()) {
				$holder->setData($subdata);
			}
			if (isset($subdata['product_name'])) {
				$subdata['name'] = $subdata['product_name'];
			}
		}
		if ($holder->products) {
			$holder->products->setIdSource('product_id')->setData($data);
		}
		return $holder;
	}


	public function getIdsByRestrictions(\LazyDataMapper\IRestrictor $restrictor, $limit = NULL)
	{
	}


	public function getByIdsRange(array $ids, Suggestor $suggestor, DataHolder $holder = NULL)
	{
		$params = $suggestor->getSuggestions();
		$columns = implode(',', $params);
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
