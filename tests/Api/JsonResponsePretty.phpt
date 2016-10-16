<?php

namespace Adeira\Api\Tests;

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class JsonResponsePretty extends \Tester\TestCase
{

	public function testSend()
	{
		$payload = new \stdClass;
		$payload->values = [
			'aaa' => [
				'bbb',
			],
		];
		$jsonPretty = new \Adeira\Api\JsonResponsePretty($payload);
		ob_start();

		$jsonPretty->send(
			new Mocks\HttpRequest(new \Nette\Http\UrlScript('')),
			$httpResponse = new Mocks\HttpResponse
		);

		$expected = <<<JSON
{
    "values": {
        "aaa": [
            "bbb"
        ]
    }
}
JSON;
		Assert::same($expected, ob_get_clean());
		Assert::same('application/json', $jsonPretty->getContentType());
		Assert::same(200, $httpResponse->getCode());
	}

}

(new JsonResponsePretty)->run();
