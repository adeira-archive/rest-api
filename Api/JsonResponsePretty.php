<?php

namespace Adeira\Api;

use Nette\Utils\Json;

class JsonResponsePretty extends \Nette\Application\Responses\JsonResponse
{

	/**
	 * Sends response to output.
	 * @return void
	 * @throws \Nette\Utils\JsonException
	 */
	public function send(\Nette\Http\IRequest $httpRequest, \Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->getContentType());
		$httpResponse->setExpiration(FALSE);
		echo Json::encode($this->getPayload(), Json::PRETTY);
	}

}
