<?php

class Products extends \LazyDataMapper\EntityCollection
{


	protected function getPrice()
	{
		$totalPrice = 0;
		foreach ($this->getData('price') as $price) {
			$totalPrice += $price;
		}
		return $totalPrice;
	}
}
