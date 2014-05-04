<?php

use Nette\Application\UI\Form;
use Nette\Application\BadRequestException;
use LazyDataMapper\IntegrityException;

class ProductPresenter extends BasePresenter
{

	/** @var \Product\Facade */
	protected $productFacade;

	/** @var Product */
	protected $product;


	public function __construct(\Product\Facade $productFacade)
	{
		$this->productFacade = $productFacade;
	}


	public function actionDefault($productId)
	{
		$this->product = $this->productFacade->getById($productId);
		if (!$this->product) {
			throw new BadRequestException("Unknown product ID $productId.");
		}

		$this->template->product = $this->product;
	}


	public function createComponentModifyProduct()
	{
		$form = new Form;

		$form->addText('name', 'Name:')
			->setDefaultValue($this->product->name)
			->setRequired('Product name cannot be empty.');

		$form->addText('price', 'Price €:')
			->setDefaultValue($this->product->price)
			->setRequired('Product price cannot be empty.')
			->addRule(Form::RANGE, 'Product price must be positive number.', [0, NULL]);

		$form->addText('stockCount', 'Stock count:')
			->setDefaultValue($this->product->stockCount)
			->setRequired('Stock count cannot be empty.')
			->addRule(Form::RANGE, $m = 'Stock count must be positive integer.', [0, NULL])
			->addRule(Form::INTEGER, $m);

		$form->addSubmit('modify', 'Modify');

		$form->onSuccess[] = $this->modifyProduct;
		return $form;
	}


	public function modifyProduct(Form $form)
	{
		$values = $form->getValues();

		$this->product->name = $values->name;
		$this->product->price = $values->price;
		$this->product->stockCount = $values->stockCount;
		try {
			$this->product->save(FALSE);
		} catch (IntegrityException $e) {
			foreach ($e->getAllMessages() as $message) {
				$form->addError($message);
			}
			return;
		}

		$this->flashMessage('Product was updated.');
		$this->redirect('this');
	}
}
