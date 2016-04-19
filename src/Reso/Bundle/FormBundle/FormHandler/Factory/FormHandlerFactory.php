<?php

namespace Reso\Bundle\FormBundle\FormHandler\Factory;

use Reso\Bundle\FormBundle\FormHandler\FormHandler;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormHandlerFactory implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * @var OptionsResolver
	 */
	private $optionsResolver;

	/**
	 * @var string
	 */
	private $handlerClass;

	/**
	 * @var array
	 */
	private $handlerEventSubscriberServices = [];

	/**
	 * Constructor.
	 *
	 * @param string $handlerClass
	 */
	public function __construct($handlerClass)
	{
		$this->handlerClass = $handlerClass;
	}

	/**
	 * @param OptionsResolver $optionsResolver
	 */
	protected function configureOptions(OptionsResolver $optionsResolver)
	{
		/** @noinspection PhpUnusedParameterInspection */
		$optionsResolver
			->setDefault('class', null)
			->setNormalizer('class', function (Options $options, $value) {
				if ($value === null) {
					return $this->getHandlerClass();
				} else {
					return $value;
				}
			})
			->setAllowedTypes('class', ['null', 'string']);

		$this->container
			->get('event_dispatcher')
			->dispatch(FormHandlerFactoryEvents::CONFIGURE_OPTIONS, new Event\ConfigureOptionsEvent($optionsResolver));
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
	 * @param string $serviceId
	 */
	public function addHandlerEventSubscriberService($serviceId)
	{
		$this->handlerEventSubscriberServices[] = $serviceId;
	}

	/**
	 * @param FormInterface $form
	 * @param array $options
	 * @return FormHandler
	 */
	public function fromForm(FormInterface $form, array $options = [])
	{
		if ($this->optionsResolver === null) {
			$this->optionsResolver = new OptionsResolver();

			$this->configureOptions($this->optionsResolver);
		}

		$dispatcher = $this->container->get('event_dispatcher');

		$options = $dispatcher->dispatch(FormHandlerFactoryEvents::PRE_CREATE_HANDLER, new Event\PreCreateHandlerEvent($form, $options))->getOptions();
		$options = $this->optionsResolver->resolve($options);

		$handlerClass = $options['class'];

		/** @var FormHandler $handler */
		$handler = new $handlerClass($this->container, $form, $options);

		foreach ($this->handlerEventSubscriberServices as $serviceId) {
			$subscriber = $this->container->get($serviceId);

			if (!$subscriber instanceof EventSubscriberInterface) {
				throw new \InvalidArgumentException(sprintf('Service "%s" does not implement EventSubscriberInterface.', get_class($subscriber)));
			}

			$handler->addSubscriber($subscriber);
		}

		$dispatcher->dispatch(FormHandlerFactoryEvents::POST_CREATE_HANDLER, new Event\PostCreateHandlerEvent($handler));

		return $handler;
	}

	/**
	 * @param FormBuilderInterface $formBuilder
	 * @param array $options
	 * @return FormHandler
	 */
	public function fromFormBuilder(FormBuilderInterface $formBuilder, array $options = [])
	{
		return $this->fromForm($formBuilder->getForm(), $options);
	}
}