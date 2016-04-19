<?php

namespace Reso\Bundle\FormBundle\FormHandler\Factory\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

class PreCreateHandlerEvent extends Event
{
	/**
	 * @var FormInterface
	 */
	private $form;

	/**
	 * @var array
	 */
	private $options = [];

	/**
	 * Constructor.
	 *
	 * @param FormInterface $form
	 * @param array $options
	 */
	public function __construct(FormInterface $form, array $options)
	{
		$this->form = $form;
		$this->options = $options;
	}

	/**
	 * @return FormInterface
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
	}

	/**
	 * @param array $options
	 */
	public function addDefaultOptions(array $options)
	{
		$this->options = array_merge($options, $this->options);
	}
}