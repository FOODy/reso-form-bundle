<?php

namespace Reso\Bundle\FormBundle\FormHandler\Factory\Event;

use Reso\Bundle\FormBundle\FormHandler\FormHandler;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

class PostCreateHandlerEvent extends Event
{
	/**
	 * @var FormHandler
	 */
	private $formHandler;

	/**
	 * Constructor.
	 *
	 * @param FormHandler $formHandler
	 */
	public function __construct(FormHandler $formHandler)
	{
		$this->formHandler = $formHandler;
	}

	/**
	 * @return FormInterface
	 */
	public function getForm()
	{
		return $this->formHandler->getForm();
	}

	/**
	 * @return FormHandler
	 */
	public function getFormHandler()
	{
		return $this->formHandler;
	}
}