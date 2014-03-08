<?php

use Nette\Application\UI\Form,
	LazyDataMapper\IntegrityException;

class HomepagePresenter extends BasePresenter
{

	/** @var \Product\Facade */
	protected $productFacade;

	/** @var \Product */
	private $product13;


	public function __construct(\Product\Facade $productFacade)
	{
		$this->productFacade = $productFacade;
	}


	public function renderDefault()
	{
		$this->template->product = $this->productFacade->getById(6);

		$this->template->products = $this->productFacade->getByIdsRange([7, 10, 11]);
	}


	public function createComponentModifyProduct13()
	{
		$this->product13 = $this->productFacade->getById(13);

		$form =  new Form;
		$form->addText('name', 'Name:')
			->setDefaultValue($this->product13->name);
		$form->addText('price', 'Price:')
			->setDefaultValue($this->product13->price);
		$form->addText('stock', 'Count on stock:')
			->setDefaultValue($this->product13->stockCount);
		$form->addSubmit('save', 'Save');
		$form->onSuccess[] = $this->modifyProduct13;
		return $form;
	}


	public function modifyProduct13(Form $form)
	{
		$values = $form->getValues();

		try {
			$this->product13->name = $values->name;
		} catch (IntegrityException $e) {
			$form['name']->addError($e->getMessage());
		}

		try {
			$this->product13->price = $values->price;
		} catch (IntegrityException $e) {
			$form['price']->addError($e->getMessage());
		}

		try {
			$this->product13->stockCount = $values->stock;
		} catch (IntegrityException $e) {
			$form['stock']->addError($e->getMessage());
		}

		if ($form->hasErrors()) {
			$this->product13->reset();
		} else {
			$this->product13->save();
			$this->flashMessage('Product 13 successfully saved!');
		}
	}
}
