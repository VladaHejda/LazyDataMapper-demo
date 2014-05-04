<?php

namespace Product;

class Restrictor implements \LazyDataMapper\IRestrictor
{

	/** @var array */
	protected $conditions = [], $parameters = [], $order = [];


	/**
	 * @param int|\Department $department
	 * @return self
	 */
	public function limitDepartment($department)
	{
		if ($department instanceof \Department) {
			$department = $department->id;
		}
		$this->conditions[] = "department_id = ?";
		$this->parameters[] = (int) $department;
		return $this;
	}


	/**
	 * @param float|NULL $min
	 * @param float|NULL $max
	 * @return self
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
		return $this;
	}


	/**
	 * @param string $name
	 */
	public function searchName($name)
	{
		$name = (string) $name;
		if ($name) {
			$this->conditions[] = "p.name LIKE ?";
			$this->parameters[] = "%$name%";
		}
	}


	/**
	 * @param string $paramName
	 * @param bool $descending
	 * @return self
	 * @throws \InvalidArgumentException
	 */
	public function orderBy($paramName, $descending = FALSE)
	{
		switch ($paramName) {
			case 'department':
				$paramName = 'department_id';
			case 'name':
			case 'price':
			case' stock':
				break;
			default:
				throw new \InvalidArgumentException("Unknown parameter '$paramName'.");
		}

		if ($descending) {
			$paramName .= ' DESC';
		}
		$this->order[] = $paramName;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasRestrictions()
	{
		return (bool) $this->conditions;
	}


	/**
	 * @return array [string conditions, array parameters, string order]
	 */
	public function getRestrictions()
	{
		$conditions = empty($this->conditions) ? '1' : implode(' AND ', $this->conditions);
		return [$conditions, $this->parameters, implode(', ', $this->order)];
	}
}
