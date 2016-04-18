<?php

namespace Reso\Bundle\FormBundle\FormHandler\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FormHandlerError extends HttpException implements FormHandlerErrorInterface
{
	/**
	 * Constructor.
	 *
	 * @param string $message
	 * @param int $statusCode
	 * @param \Exception|null $previous
	 * @param array $headers
	 * @param int $code
	 */
	public function __construct($message, $statusCode = 400, \Exception $previous = null, array $headers = [], $code = 0)
	{
		parent::__construct($statusCode, $message, $previous, $headers, $code);
	}

	/**
	 * @return Response
	 */
	public function toResponse()
	{
		return new JsonResponse(['message' => $this->getMessage()], $this->getStatusCode());
	}
}