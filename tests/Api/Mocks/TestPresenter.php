<?php

namespace Adeira\Api\Tests\Mocks;

class TestPresenter extends \Adeira\Api\RestPresenter
{

//	public function read()
//	{
//		$this->payload->test = 'ok';
//	}

	public function getSession()
	{
		return new \Kdyby\FakeSession\Session(
			new \Nette\Http\Session(
				new HttpRequest(new \Nette\Http\UrlScript('')),
				new HttpResponse
			)
		);
	}

}
