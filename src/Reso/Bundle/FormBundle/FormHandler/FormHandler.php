<?php

namespace Reso\Bundle\FormBundle\FormHandler;

use Doctrine\ORM\EntityManager;
use Reso\Bundle\FormBundle\FormHandler\Exception\FormHandlerError;
use Reso\Bundle\FormBundle\FormHandler\Exception\FormHandlerErrorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FormHandler
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var EventDispatcher
	 */
	private $dispatcher;

	/**
	 * @var string
	 */
	private $entityManagerName;

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var array
	 */
	private $attributes = [];

	/**
	 * @var FormInterface
	 */
	private $form;

	/**
	 * @var mixed
	 */
	private $originalData;

	/**
	 * @var callable
	 */
	private $serializer;

	/**
	 * @var mixed
	 */
	private $serializedData;

	/**
	 * @var FormHandlerErrorInterface
	 */
	private $caughtError;

	/**
	 * @var callable
	 */
	private $persister;

	/**
	 * Constructor.
	 *
	 * @param ContainerInterface $container
	 * @param FormInterface $form
	 * @param array $options
	 */
	public function __construct(ContainerInterface $container, FormInterface $form, array $options)
	{
		$this->container = $container;
		$this->form = $form;
		$this->options = $options;
		$this->serializer = function ($data) {
			/** @var object $data */
			return $data->toArray();
		};

		$this->persister = function (FormHandler $formHandler) {
			$object = $formHandler->getData();

			if (is_object($object)) {
				$formHandler->getEntityManager()->persist($object);
			}
		};

		$this->dispatcher = new EventDispatcher();
	}

	/**
	 * @return FormInterface
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * @return string
	 */
	public function getEntityManagerName()
	{
		return $this->entityManagerName;
	}

	/**
	 * @param string $entityManagerName
	 * @return $this
	 */
	public function setEntityManagerName($entityManagerName)
	{
		$this->entityManagerName = $entityManagerName;

		return $this;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->container->get('doctrine')->getManager($this->entityManagerName);
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasOption($name)
	{
		return array_key_exists($name, $this->options);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getOption($name)
	{
		if ($this->hasOption($name)) {
			return $this->options[$name];
		}

		throw new \InvalidArgumentException(sprintf('Option "%s" does not exist!', $name));
	}

	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * @param array $attributes
	 * @return $this
	 */
	public function setAttributes(array $attributes)
	{
		$this->attributes = $attributes;

		return $this;
	}

	/**
	 * @param array $attributes
	 * @return $this
	 */
	public function addAttributes(array $attributes)
	{
		$this->attributes = array_merge($this->attributes, $attributes);

		return $this;
	}

	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getAttribute($name, $defaultValue = null)
	{
		return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $defaultValue;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return $this
	 */
	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;

		return $this;
	}

	/**
	 * @return callable
	 */
	public function getSerializer()
	{
		return $this->serializer;
	}

	/**
	 * @param callable $serializer
	 * @return FormHandler
	 */
	public function setSerializer($serializer)
	{
		$this->serializer = $serializer;

		return $this;
	}

	/**
	 * @return FormHandlerErrorInterface
	 */
	public function getCaughtError()
	{
		return $this->caughtError;
	}

	/**
	 * @param FormHandlerErrorInterface $caughtError
	 * @return $this
	 */
	public function setCaughtError(FormHandlerErrorInterface $caughtError = null)
	{
		$this->caughtError = $caughtError;

		return $this;
	}

	/**
	 * @return callable
	 */
	public function getPersister()
	{
		return $this->persister;
	}

	/**
	 * @param callable $persister
	 * @return $this
	 */
	public function setPersister($persister)
	{
		$this->persister = $persister;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->form->getData();
	}

	/**
	 * @return mixed
	 */
	public function getOriginalData()
	{
		return $this->originalData;
	}

	/**
	 * @param mixed $serializedData
	 * @return $this
	 */
	public function setSerializedData($serializedData)
	{
		$this->serializedData = $serializedData;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSerializedData()
	{
		if ($this->serializedData === null) {
			$event = $this->dispatcher->dispatch(
				FormHandlerEvents::PRE_SERIALIZE,
				new FormHandlerEvent($this, $this->getData())
			);

			if ($this->serializedData === null) {
				$this->serializedData = call_user_func($this->serializer, $event->getData(), $this);
			}
		}

		return $this->serializedData;
	}

	/**
	 * @param string $eventName
	 * @param callable $listener
	 * @param int $priority
	 * @return $this
	 */
	public function addListener($eventName, $listener, $priority = 0)
	{
		$this->dispatcher->addListener($eventName, $listener, $priority);

		return $this;
	}

	/**
	 * @param EventSubscriberInterface $subscriber
	 * @return $this
	 */
	public function addSubscriber(EventSubscriberInterface $subscriber)
	{
		$this->dispatcher->addSubscriber($subscriber);

		return $this;
	}

	/**
	 * @param Request $request
	 * @param mixed $data
	 * @return $this
	 */
	public function submitAndFlush(Request $request, $data = null)
	{
		try {
			$this
				->setData($data)
				->submit($request)
				->flush();
		} catch (FormHandlerErrorInterface $error) {
			$this->setCaughtError($error);
		} catch (HttpException $error) {
			$this->setCaughtError(FormHandlerError::fromMessage($error->getMessage(), $error->getStatusCode(), $error));
		} catch (\Exception $error) {
			$debugMessage = '[' . get_class($error) . '] ' . $error->getMessage();

			$this->container->get('logger')->addError($debugMessage, [
				'file' => $error->getFile(),
				'line' => $error->getLine(),
				'trace' => $error->getTraceAsString(),
			]);

			if ($this->container->getParameter('kernel.debug')) {
				$this->setCaughtError(FormHandlerError::fromMessage($debugMessage, 500, $error, [
					'traceString' => $error->getTraceAsString(),
				]));
			} else {
				$this->setCaughtError(FormHandlerError::fromMessage('', 500, $error));
			}
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isValid()
	{
		return $this->caughtError === null && $this->form->isValid();
	}

	/**
	 * @return Response
	 */
	public function toResponse()
	{
		if ($this->isValid()) {
			return new JsonResponse($this->getSerializedData());
		}

		if ($this->caughtError !== null) {
			return $this->caughtError->getResponse();
		}

		foreach ($this->form->getErrors(true) as $error) {
			return new JsonResponse([
				'message' => $error->getMessage(),
				'formError' => [
					'origin' => ($origin = $error->getOrigin()) ? $origin->getName() : null,
				],
			], 400);
		}

		return new Response(null, 500);
	}

	/**
	 * @param mixed $data
	 * @return $this
	 */
	protected function setData($data)
	{
		$this->caughtError = null;
		$this->serializedData = null;

		if (is_object($data)) {
			$this->originalData = clone $data;
		} else {
			$this->originalData = $data;
		}

		$data = $this->dispatcher->dispatch(FormHandlerEvents::PRE_SET_DATA, new FormHandlerEvent($this, $data))->getData();

		$this->form->setData($data);

		$this->dispatcher->dispatch(FormHandlerEvents::POST_SET_DATA, new FormHandlerEvent($this, $data));

		return $this;
	}

	/**
	 * @param Request $request
	 * @return $this
	 */
	protected function submit(Request $request)
	{
		try {
			$data = json_decode($request->getContent(), true);

			// Pre submit
			$data = $this->dispatcher
				->dispatch(
					FormHandlerEvents::PRE_SUBMIT,
					new FormHandlerEvent($this, $data)
				)
				->getData();

			// Submit
			$this->form->submit($data);

			// Post submit
			$this->dispatcher->dispatch(
				FormHandlerEvents::POST_SUBMIT,
				new FormHandlerEvent($this, $this->getData())
			);
		} catch (HttpException $error) {
			$this->form->addError(new FormError($error->getMessage()));
		}

		if (!$this->isValid()) {
			return $this;
		}

		$this->dispatcher->dispatch(
			FormHandlerEvents::POST_VALIDATE,
			new FormHandlerEvent($this, $this->getData())
		);

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function flush()
	{
		if (!$this->isValid()) {
			return $this;
		}

		$this->dispatcher->dispatch(FormHandlerEvents::PRE_FLUSH, new FormHandlerEvent($this, $this->getData()));

		if ($this->persister !== null) {
			call_user_func($this->persister, $this);
		}

		$this->getEntityManager()->flush();

		$this->dispatcher->dispatch(FormHandlerEvents::POST_FLUSH, new FormHandlerEvent($this, $this->getData()));

		return $this;
	}
}