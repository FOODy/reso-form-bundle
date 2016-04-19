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
	 * @var array
	 */
	private $options = [];

	/**
	 * Constructor.
	 *
	 * @param FormHandler $formHandler
	 * @param array $options
	 */
	public function __construct(FormHandler $formHandler, array $options)
	{
		$this->formHandler = $formHandler;
		$this->options = $options;
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

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}
}