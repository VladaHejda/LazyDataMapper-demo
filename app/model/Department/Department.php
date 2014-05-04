<?php

/**
 * @property string $name
 * @property Products $products
 */
class Department extends \LazyDataMapper\Entity
{

	protected function getProducts()
	{
		$restrictor = new Product\Restrictor;
		$restrictor->limitDepartment($this->id);
		return $this->getChild('Product', $restrictor);
	}
}
