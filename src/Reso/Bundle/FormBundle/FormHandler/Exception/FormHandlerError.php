<?php

namespace Reso\Bundle\FormBundle\FormHandler\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FormHandlerError extends \Exception implements FormHandlerErrorInterface
{
	/**
	 * @var int
	 */
	protected $statusCode;

	/**
	 * Constructor.
	 *
	 * @param string $message
	 * @param int $statusCode
	 * @param \Exception|null $previous
	 * @param int $code
	 */
	public function __construct($message, $statusCode = 400, \Exception $previous = null, $code = 0)
	{
		$this->statusCode = $statusCode;

		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * @return Response
	 */
	public function toResponse()
	{
		return new JsonResponse(['message' => $this->getMessage()], $this->getStatusCode());
	}
}