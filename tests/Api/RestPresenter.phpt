<?php

namespace Adeira\Api\Tests;

use Nette\Http\IRequest;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class RestPresenter extends \Tester\TestCase
{

	use \Testbench\TCompiledContainer;

	/**
	 * @dataProvider supportedHttpMethods
	 */
	public function testRead($method)
	{
		$response = $this->getResponsePayload('Test:read', $method);
		Assert::same([
			'error' => ['message' => "Action 'read' is not allowed via {$method} method."],
			'status' => 'error',
		], $response);

		/** @var \Nette\Http\IResponse $httpResponse */
		$httpResponse = $this->getService(\Nette\Http\IResponse::class);
		Assert::same(\Nette\Http\IResponse::S405_METHOD_NOT_ALLOWED, $httpResponse->getCode());
	}

	/////

	public function supportedHttpMethods()
	{
		yield [IRequest::GET];
		yield [IRequest::POST];
		yield [IRequest::HEAD];
		yield [IRequest::PUT];
		yield [IRequest::DELETE];
		yield [IRequest::PATCH];
		yield [IRequest::OPTIONS];
	}

	private function getResponsePayload($destination, $method = IRequest::GET)
	{
		list($presenterName, $action) = \Nette\Application\Helpers::splitName($destination);

		/** @var \Nette\Application\IPresenterFactory $presenterFactory */
		$presenterFactory = $this->getContainer()->getByType(\Nette\Application\IPresenterFactory::class);
		$presenter = $presenterFactory->createPresenter($presenterName);

		$appRequest = new \Testbench\Mocks\ApplicationRequestMock(
			$presenterName,
			$method,
			['action' => $action]
		);

		/** @var \Adeira\Api\JsonResponsePretty $response */
		$response = $presenter->run($appRequest);
		Assert::type(\Adeira\Api\JsonResponsePretty::class, $response);
		return (array)$response->getPayload();
	}

}

(new RestPresenter)->run();
