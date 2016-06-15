<?php

namespace Reso\Bundle\FormBundle\FormHandler\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @mixin \Exception
 */
trait FormHandlerErrorTrait
{
	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return new JsonResponse(['message' => $this->getMessage()], 400, ['Content-Type' => 'application/json']);
	}
}