<?php

use Nette\Application\UI\Form;

class ProductListPresenter extends BasePresenter
{

	/** @var \Department\Facade */
	protected $departmentFacade;

	/** @var \Product\Facade */
	protected $productFacade;


	public function __construct(\Department\Facade $departmentFacade, \Product\Facade $productFacade)
	{
		$this->departmentFacade = $departmentFacade;
		$this->productFacade = $productFacade;
	}


	public function renderDefault($departmentId = NULL)
	{
		$this->template->departments = $this->departmentFacade->getAll();

		if ($departmentId !== NULL) {
			$this->template->currentDepartment = $this->departmentFacade->getById($departmentId);
		}

		$this['searchProduct']->action = '?';
	}


	public function createComponentSearchProduct()
	{
		$form = new Form;

		$form->addText('query', 'Search product:');

		$form->addText('priceFrom', 'Price min:')
			->addCondition(Form::FILLED)
				->addRule(Form::NUMERIC, 'Price must be a number.');

		$form->addText('priceTo', 'Price max:')
			->addCondition(Form::FILLED)
				->addRule(Form::NUMERIC, 'Price must be a number.');

		$form->addSubmit('search', 'Search');

		$form->onSuccess[] = $this->searchProduct;
		return $form;
	}


	public function searchProduct(Form $form)
	{
		$values = $form->getValues();

		$restrictor = new \Product\Restrictor;
		if ($values->query) {
			$restrictor->searchName($values->query);
		}

		if ($values->priceFrom || $values->priceTo) {
			$restrictor->limitPrice($values->priceFrom ?: NULL, $values->priceTo ?: NULL);
		}

		if (!$restrictor->hasRestrictions()) {
			$form->addError('K vyhledání produktu musíte zadat alespoň nějaká kritéria.');
		} else {
			$this->template->foundProducts = $this->productFacade->getByRestrictions($restrictor);
		}
	}
}
