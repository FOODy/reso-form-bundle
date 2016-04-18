<?php

namespace Reso\Bundle\FormBundle\FormHandler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class FormHandlerFactory
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var string
	 */
	private $handlerClass = FormHandler::class;

	/**
	 * Constructor.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * @return string
	 */
	public function getHandlerClass()
	{
		return $this->handlerClass;
	}

	/**
	 * @param string $handlerClass
	 */
	public function setHandlerClass($handlerClass)
	{
		$this->handlerClass = $handlerClass;
	}

	/**
	 * @param FormInterface $form
	 * @return FormHandler
	 */
	public function fromForm(FormInterface $form)
	{
		$handlerClass = $this->handlerClass;
		$handler = new $handlerClass($this->container, $form);

		$this->container
			->get('event_dispatcher')
			->dispatch(FormHandlerEvents::CONSTRUCTED, new FormHandlerEvent($handler, null));

		return $handler;
	}

	/**
	 * @param FormBuilderInterface $formBuilder
	 * @return FormHandler
	 */
	public function fromFormBuilder(FormBuilderInterface $formBuilder)
	{
		return $this->fromForm($formBuilder->getForm());
	}
}