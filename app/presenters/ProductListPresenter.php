<?php

use Nette\Application\UI\Form;
use Nette\Application\BadRequestException;
use LazyDataMapper\IntegrityException;

class ProductListPresenter extends BasePresenter
{

	/** @var \Department\Facade */
	protected $departmentFacade;

	/** @var \Product\Facade */
	protected $productFacade;

	/** @var Department */
	protected $department;


	public function __construct(\Department\Facade $departmentFacade, \Product\Facade $productFacade)
	{
		$this->departmentFacade = $departmentFacade;
		$this->productFacade = $productFacade;
	}


	public function renderDefault($departmentId = NULL)
	{
		$this->template->departments = $this->departmentFacade->getAll();

		if ($departmentId !== NULL) {
			$department = $this->departmentFacade->getById($departmentId);
			if (!$department) {
				throw new BadRequestException("Unknown department ID $departmentId.");
			}
			$this->template->currentDepartment = $this->department = $department;
		}

		$this['searchProduct']->action = '?';
	}


	public function actionNew($departmentId)
	{
		$department = $this->departmentFacade->getById($departmentId);
		if (!$department) {
			throw new BadRequestException("Unknown department ID $departmentId.");
		}
		$this->template->department = $this->department = $department;
	}


	public function createComponentSearchProduct()
	{
		$form = new Form;

		$form->addText('query', 'Search product:');

		$form->addText('priceFrom', 'Price min:')
			->addCondition(Form::FILLED)
				->addRule(Form::RANGE, 'Price must be a positive number.', [0, NULL]);

		$form->addText('priceTo', 'Price max:')
			->addCondition(Form::FILLED)
				->addRule(Form::RANGE, 'Price must be a positive number.', [0, NULL]);

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
			$form->addError('To search product you must input at least some criteria.');
		} else {
			$this->template->foundProducts = $this->productFacade->getByRestrictions($restrictor);
		}
	}


	public function createComponentCreateProduct()
	{
		$form = new Form;

		$form->addHidden('department', $this->params['departmentId']);

		$form->addText('name', 'Product name:')
			->setRequired('Please fill the name.');

		$form->addText('price', 'Price:')
			->setRequired('Please fill the price.')
			->addRule(Form::RANGE, 'Fill price as positive number.', [0, NULL]);

			$form->addSubmit('create', 'Create');

		$form->onSuccess[] = $this->createProduct;
		return $form;
	}


	public function createProduct(Form $form)
	{
		$values = $form->getValues();

		try {
			$product = $this->productFacade->create($values->name, $values->price, $values->department, FALSE);
		} catch (IntegrityException $e) {
			foreach ($e->getAllMessages() as $message) {
				$form->addError($message);
			}
			return;
		}

		$departmentName = ucfirst($this->department->name);
		$this->flashMessage("Created $departmentName $product->name.");
		$this->redirect('default', ['departmentId' => $values->department]);
	}
}
