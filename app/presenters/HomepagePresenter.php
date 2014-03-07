<?php

class HomepagePresenter extends BasePresenter
{

	/** @var \Product\Facade */
	protected $productFacade;


	public function __construct(\Product\Facade $productFacade)
	{
		$this->productFacade = $productFacade;
	}


	public function renderDefault()
	{
		$this->template->product = $this->productFacade->getById(6);

		$this->template->products = $this->productFacade->getByIdsRange([7, 10, 11]);
	}
}
