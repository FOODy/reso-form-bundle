<?php

namespace Reso\Bundle\FormBundle\FormHandler\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FormHandlerError extends \Exception implements FormHandlerErrorInterface
{
	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * Constructor.
	 *
	 * @param string $message
	 * @param Response $response
	 * @param \Exception|null $previous
	 */
	public function __construct($message, Response $response, \Exception $previous = null)
	{
		$this->response = $response;

		parent::__construct($message, 0, $previous);
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * @param string $message
	 * @param int $statusCode
	 * @param \Exception $previous
	 * @return static
	 */
	static public function fromMessage($message, $statusCode = 400, \Exception $previous = null)
	{
		return new static($message, new JsonResponse(['message' => $message], $statusCode), $previous);
	}

	/**
	 * @param Response $response
	 * @param \Exception $previous
	 * @return static
	 */
	static public function fromResponse(Response $response, \Exception $previous = null)
	{
		return new static('', $response, $previous);
	}
}