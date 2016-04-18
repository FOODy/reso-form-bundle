<?php

namespace Reso\Bundle\FormBundle\FormHandler\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

interface FormHandlerErrorInterface extends HttpExceptionInterface
{
	/**
	 * @return Response
	 */
	public function toResponse();
}