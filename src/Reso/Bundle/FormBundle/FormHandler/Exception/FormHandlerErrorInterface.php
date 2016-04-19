<?php

namespace Reso\Bundle\FormBundle\FormHandler\Exception;

use Symfony\Component\HttpFoundation\Response;

interface FormHandlerErrorInterface
{
	/**
	 * @return Response
	 */
	public function toResponse();
}