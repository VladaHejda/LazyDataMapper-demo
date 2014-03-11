<?php

namespace Product;

class Facade extends \LazyDataMapper\Facade
{

	/**
	 * @param string $name
	 * @param float $price
	 * @param int $department
	 * @param bool $throwFirst
	 * @return \LazyDataMapper\IEntity
	 */
	public function create($name, $price, $department, $throwFirst = TRUE)
	{
		$publicData = [
			'name' => $name,
			'price' => $price,
		];

		$privateData = [
			'department_id' => $department,
		];
		return $this->createEntity($publicData, $privateData, $throwFirst);
	}
}
