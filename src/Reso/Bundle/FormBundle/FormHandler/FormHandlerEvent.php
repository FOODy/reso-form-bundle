<?php

namespace Reso\Bundle\FormBundle\FormHandler;

use Symfony\Component\Form\FormEvent;

class FormHandlerEvent extends FormEvent
{
	/**
	 * @var FormHandler
	 */
	private $formHandler;

	/**
	 * @param FormHandler $formHandler
	 * @param mixed $data
	 */
	public function __construct(FormHandler $formHandler, $data)
	{
		$this->formHandler = $formHandler;

		parent::__construct($formHandler->getForm(), $data);
	}

	/**
	 * @return FormHandler
	 */
	public function getFormHandler()
	{
		return $this->formHandler;
	}
}