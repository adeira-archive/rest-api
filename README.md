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
	errorPresenter: 'Api:Error',
	enableForModules:
		- Api
	presenterMapping:
		Api: App\ApiModule\Presenters\*Presenter
```

Create first REST API enndpoint (simple presenter):
```php
<?php

namespace App\Presenters;

class UsersPresenter extends \Adeira\Api\RestPresenter
{

	public function readAll()
	{
		$this->payload->test = 'ok';
	}

}
```
