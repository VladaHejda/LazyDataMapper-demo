<?php

use LazyDataMapper\IntegrityException;

/**
 * @property string $name
 * @property float $price
 * @property int $stockCount
 * @property-read Department $department
 */
class Product extends \LazyDataMapper\Entity
{

	protected $privateParams = ['department_id', 'stock'];


	protected function getStockCount()
	{
		return $this->getBase('stock');
	}


	protected function getDepartment()
	{
		return $this->getChild('Department', $this->getBase('department_id'));
	}


	protected function setName($name)
	{
		$name = (string) $name;
		if (empty($name)) {
			throw new IntegrityException('Name cannot be empty!');
		}
		return $name;
	}


	protected function setPrice($price)
	{
		if (!is_numeric($price)) {
			throw new IntegrityException('Price must be a number!');
		}
		$price = (float) $price;
		if ($price < 0) {
			throw new IntegrityException('Price cannot be negative!');
		}
		return $price;
	}


	protected function setStockCount($count)
	{
		if (!is_numeric($count)) {
			throw new IntegrityException('Stock count must be a number!');
		}
		$count = (int) $count;
		if ($count < 0) {
			throw new IntegrityException('Stock count cannot be negative!');
		}
		$this->setImmutable('stock', $count);
	}
}
