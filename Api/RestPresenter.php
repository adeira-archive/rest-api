<?php

namespace Adeira\Api;

use Nette;
use Nette\Application;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class RestPresenter implements Application\IPresenter
{

	const HEADER_AUTHORIZATION = 'Authorization';
	const HTTP_HEADER_ALLOW = 'Access-Control-Allow-Methods';

	/**
	 * @var string[]
	 */
	private static $actionMap = [
		'read' => IRequest::GET,
		'readAll' => IRequest::GET,
		'create' => IRequest::POST,
		'update' => IRequest::PUT,
		'delete' => IRequest::DELETE,
	];

	/** @var Application\Request */
	private $appRequest;

	/** @var Nette\Application\IResponse */
	private $appResponse;

	/** @var Nette\Http\IResponse */
	private $httpResponse;

	/** @var \stdClass | NULL (before run) */
	protected $payload;

	public function run(Application\Request $request)
	{
		$this->appRequest = $request;
		$this->payload = new \stdClass;

		if (!$this->httpResponse->isSent()) {
			$this->httpResponse->addHeader('Vary', 'X-Requested-With');
		}

		$appParameters = $this->appRequest->getParameters();
		$action = $appParameters['action'];

		//TODO: simple canonicalize

		if (!$this->isMethodAllowed($action)) {
			$this->httpResponse->addHeader(self::HTTP_HEADER_ALLOW, implode(', ', $this->getAllowedMethods()));
			$this->error("Action '{$action}' is not allowed via {$request->getMethod()} method.", IResponse::S405_METHOD_NOT_ALLOWED);
		}

		// calls $this->action<Action>()
		$presenterRc = new \ReflectionClass(get_called_class());
		$actionMethod = $action;
		if ($presenterRc->hasMethod($actionMethod)) {
			$rm = $presenterRc->getMethod($actionMethod);
			if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
				$rm->invoke($this);
			}
		}

		if ($this->appResponse === NULL) {
			//TODO: server error (exception)?
			$this->appResponse = new JsonResponsePretty($this->payload);
		}
		return $this->appResponse;
	}

	/**
	 * Injector (lazy constructor)!
	 *
	 * @param \Nette\Http\IResponse $httpResponse
	 */
	public function injectPrimary(IResponse $httpResponse)
	{
		$this->httpResponse = $httpResponse;
	}

	public function success($status = 'ok')
	{
		$this->payload->status = $status;
		$this->appResponse = new JsonResponsePretty($this->payload);
	}

	public function error($error = NULL, $httpCode = IResponse::S404_NOT_FOUND)
	{
		$this->httpResponse->setCode($httpCode);
		$this->payload->error = [
			'message' => $error,
		];
		$this->payload->status = 'error';
		$this->appResponse = new JsonResponsePretty($this->payload);
	}

	/**
	 * Returns TRUE if given action is supported by current presenter.
	 *
	 * @return bool
	 */
	private function isMethodAllowed($action)
	{
		$presenter = new \ReflectionClass(get_called_class());
		return $presenter->hasMethod($action);
	}

	/**
	 * Returns array of allowed methods by current presenter.
	 *
	 * @return string[]
	 */
	private function getAllowedMethods()
	{
		$allowedMethods = [];
		foreach (self::$actionMap as $action => $method) {
			if ($this->isMethodAllowed($action)) {
				$allowedMethods[] = $method;
			}
		}
		return array_unique($allowedMethods);
	}

}
