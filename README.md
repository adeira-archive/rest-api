[![Build Status](https://travis-ci.org/adeira/rest-api.svg?branch=master)](https://travis-ci.org/adeira/rest-api)

! Work In Progress !
====================

Install:
```
composer require adeira/rest-api
```

Use:
```
extensions:
	restApi: Adeira\Api\DI\Extension
```

Configure:
```
restApi:
	errorPresenter: 'Api:Error' # custom error presenter for API
	enableForModules: # for these modules custom error presenter will be used and session will be disabled (TODO: rename)
		- Api
		- '' # for destination without module (Homepage:default - TODO: improve)
	presenterMapping:
		Api: App\ApiModule\Presenters\*Presenter
```

Create first REST API endpoint (simple presenter):
```php
<?php declare(strict_types = 1);

namespace App\Presenters;

class UsersPresenter extends \Adeira\Api\RestPresenter
{

	public function readAll()
	{
		$this->payload->test = 'ok';
	}

}
```

Custom error presenter example (**work in progress**):
```php
<?php declare(strict_types = 1);

namespace Adeira\Connector\Presenters;

use Nette;

class ApiErrorPresenter extends \Adeira\Api\RestPresenter
{

	public function run(Nette\Application\Request $request): Nette\Application\IResponse
	{
		$this->payload = new \stdClass;
		$this->payload->error = [
			'message' => 'Internal Server Error',
		];
		$this->payload->status = 'error';
		return new \Adeira\Api\JsonResponsePretty($this->payload);
	}

}

```
