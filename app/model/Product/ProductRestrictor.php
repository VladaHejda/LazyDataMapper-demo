<?php

namespace Product;

class Restrictor implements \LazyDataMapper\IRestrictor
{

	/** @var array */
	protected $conditions = [];

	/** @var array */
	protected $parameters = [];


	/**
	 * @param int $departmentId
	 */
	public function limitDepartment($departmentId)
	{
		$this->conditions[] = "department_id = ?";
		$this->parameters[] = (int) $departmentId;
	}


	/**
	 * @param float|NULL $min
	 * @param float|NULL $max
	 */
	public function limitPrice($min, $max = NULL)
	{
		if (NULL !== $min) {
			$this->conditions[] = "price >= ?";
			$this->parameters[] = $min;
		}
		if (NULL !== $max) {
			$this->conditions[] = "price <= ?";
			$this->parameters[] = $max;
		}
	}


	/**
	 * @return array [string conditions, array parameters]
	 */
	public function getRestrictions()
	{
		return [implode(' AND ', $this->conditions), $this->parameters];
	}
}
