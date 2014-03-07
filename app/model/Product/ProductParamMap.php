<?php

namespace Product;

class ParamMap extends \LazyDataMapper\ParamMap
{

	protected $map = [
		'department_id', 'name', 'price', 'stock',
	];
}
